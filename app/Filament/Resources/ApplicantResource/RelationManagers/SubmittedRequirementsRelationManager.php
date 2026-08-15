<?php

namespace App\Filament\Resources\ApplicantResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Requirement;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Computed;

class SubmittedRequirementsRelationManager extends RelationManager
{
    protected static string $relationship = 'submittedRequirements';

    protected static ?string $title = 'Requirements Checklist';

    protected static ?string $recordTitleAttribute = 'name';

    // Livewire reactive properties for the progress bar
    public int $progressTotal   = 0;
    public int $progressDone    = 0;
    public int $progressPercent = 0;

    protected function syncRequirements(): void
    {
        $applicant       = $this->getOwnerRecord();
        $allRequirements = Requirement::where('type_of_application_id', $applicant->type_of_application_id)
            ->pluck('id');

        $existing = $applicant->submittedRequirements()->pluck('requirements.id');
        $missing  = $allRequirements->diff($existing);

        foreach ($missing as $requirementId) {
            $applicant->submittedRequirements()->attach($requirementId, [
                'is_submitted' => false,
                'file_path'    => null,
                'notes'        => null,
            ]);
        }
    }

    protected function refreshProgress(): void
    {
        $applicant            = $this->getOwnerRecord();
        $this->progressTotal   = $applicant->submittedRequirements()->count();
        $this->progressDone    = $applicant->submittedRequirements()->wherePivot('is_submitted', true)->count();
        $this->progressPercent = $this->progressTotal > 0
            ? (int) round(($this->progressDone / $this->progressTotal) * 100)
            : 0;
    }

    public function mount(): void
    {
        parent::mount();
        $this->syncRequirements();
        $this->refreshProgress();
    }

    public function getProgressBarHtml(): HtmlString
    {
        $total   = $this->progressTotal;
        $done    = $this->progressDone;
        $percent = $this->progressPercent;

        $color = match (true) {
            $percent === 100 => '#22c55e',
            $percent >= 50   => '#f59e0b',
            default          => '#ef4444',
        };

        $label = $percent === 100
            ? '🎉 All requirements submitted!'
            : "{$done} of {$total} requirements submitted ({$percent}%)";

        return new HtmlString("
            <div style='margin-bottom:4px;font-size:0.75rem;color:#6b7280;font-weight:500;'>{$label}</div>
            <div style='background:#e5e7eb;border-radius:9999px;height:8px;overflow:hidden;width:100%;'>
                <div style='width:{$percent}%;background:{$color};height:100%;border-radius:9999px;transition:width 0.4s ease;'></div>
            </div>
        ");
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Document')
                            ->directory('requirements')
                            ->disk('local')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['application/pdf'])
                            ->downloadable()
                            ->openable()
                            ->helperText('PDF only · Max 5MB'),

                        Forms\Components\Group::make([
                            Forms\Components\Toggle::make('is_submitted')
                                ->label('Mark as Submitted')
                                ->default(false)
                                ->inline(false),

                            Forms\Components\Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3)
                                ->maxLength(500)
                                ->placeholder('Optional notes...'),
                        ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->heading('Requirements Checklist')
            ->description(fn () => $this->getProgressBarHtml())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Requirement')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->grow(),

                Tables\Columns\IconColumn::make('pivot.is_submitted')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('pivot.file_path')
                    ->label('File')
                    ->formatStateUsing(fn ($state) => $state ? '📎 Attached' : '—')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->badge()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('pivot.notes')
                    ->label('Notes')
                    ->limit(40)
                    ->placeholder('—')
                    ->tooltip(fn ($state) => strlen((string) $state) > 40 ? $state : null)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('pivot.updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable()
                    ->color('gray')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_submitted')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('✅ Submitted')
                    ->falseLabel('⏳ Pending')
                    ->queries(
                        true: fn ($query) => $query->wherePivot('is_submitted', true),
                        false: fn ($query) => $query->wherePivot('is_submitted', false),
                    ),
            ])
            ->headerActions([
                Tables\Actions\Action::make('submit_requirements')
                    ->label('Submit Requirements')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->modalHeading('')
                    ->modalWidth('4xl')
                    ->modalSubmitActionLabel('💾  Save All Changes')
                    ->modalCancelActionLabel('Cancel')
                    ->form(function () {
                        $applicant    = $this->getOwnerRecord();
                        $requirements = $applicant->submittedRequirements()
                            ->wherePivot('is_submitted', false)
                            ->get();

                        $total   = $applicant->submittedRequirements()->count();
                        $done    = $total - $requirements->count();
                        $pending = $requirements->count();
                        $percent = $total > 0 ? round(($done / $total) * 100) : 0;

                        if ($requirements->isEmpty()) {
                            return [
                                Forms\Components\Placeholder::make('all_done')
                                    ->label('')
                                    ->content(new HtmlString('
                                        <div style="text-align:center;padding:32px 0;">
                                            <div style="font-size:3rem;line-height:1;">🎉</div>
                                            <div style="font-size:1.1rem;font-weight:700;color:#16a34a;margin-top:12px;">All requirements submitted!</div>
                                            <div style="font-size:0.85rem;color:#6b7280;margin-top:4px;">Nothing pending — this applicant is fully compliant.</div>
                                        </div>
                                    ')),
                            ];
                        }

                        $barColor = $percent >= 75 ? '#22c55e' : ($percent >= 40 ? '#f59e0b' : '#ef4444');

                        $schema = [
                            Forms\Components\Placeholder::make('_modal_header')
                                ->label('')
                                ->content(new HtmlString('
                                    <div style="background:linear-gradient(135deg,#1e3a5f 0%,#1d4ed8 100%);border-radius:12px;padding:20px 24px;margin-bottom:4px;">
                                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                                            <div>
                                                <div style="color:#fff;font-size:1.1rem;font-weight:700;letter-spacing:-0.01em;">📋 Submit Requirements</div>
                                                <div style="color:#93c5fd;font-size:0.8rem;margin-top:2px;">Upload documents and mark each requirement as submitted</div>
                                            </div>
                                            <div style="display:flex;gap:10px;">
                                                <div style="background:rgba(255,255,255,0.15);border-radius:8px;padding:8px 14px;text-align:center;">
                                                    <div style="color:#fff;font-size:1.2rem;font-weight:800;">' . $pending . '</div>
                                                    <div style="color:#93c5fd;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;">Pending</div>
                                                </div>
                                                <div style="background:rgba(255,255,255,0.15);border-radius:8px;padding:8px 14px;text-align:center;">
                                                    <div style="color:#4ade80;font-size:1.2rem;font-weight:800;">' . $done . '</div>
                                                    <div style="color:#93c5fd;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;">Done</div>
                                                </div>
                                                <div style="background:rgba(255,255,255,0.15);border-radius:8px;padding:8px 14px;text-align:center;">
                                                    <div style="color:#fbbf24;font-size:1.2rem;font-weight:800;">' . $percent . '%</div>
                                                    <div style="color:#93c5fd;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;">Complete</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="margin-top:14px;">
                                            <div style="background:rgba(255,255,255,0.2);border-radius:9999px;height:6px;overflow:hidden;">
                                                <div style="width:' . $percent . '%;background:' . $barColor . ';height:100%;border-radius:9999px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                ')),

                            Forms\Components\Placeholder::make('_col_headers')
                                ->label('')
                                ->content(new HtmlString('
                                    <div style="display:grid;grid-template-columns:2fr 2.5fr 2fr 80px;gap:8px;padding:6px 12px;margin-top:4px;">
                                        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#9ca3af;">Requirement</div>
                                        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#9ca3af;">File Upload</div>
                                        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#9ca3af;">Notes</div>
                                        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#9ca3af;text-align:center;">Done</div>
                                    </div>
                                    <div style="height:1px;background:#e5e7eb;margin:0 12px;"></div>
                                ')),
                        ];

                        foreach ($requirements as $index => $requirement) {
                            $rowBg = $index % 2 === 0 ? 'background:#f8fafc;' : 'background:#ffffff;';

                            $schema[] = Forms\Components\Grid::make(12)
                                ->schema([
                                    Forms\Components\Placeholder::make("label_{$requirement->id}")
                                        ->label('')
                                        ->content(new HtmlString('
                                            <div style="' . $rowBg . 'border-radius:8px;padding:10px 10px 10px 12px;height:100%;display:flex;align-items:center;gap:10px;border-left:3px solid #f59e0b;">
                                                <div style="background:#fef3c7;color:#92400e;font-size:0.65rem;font-weight:800;border-radius:9999px;min-width:20px;height:20px;display:flex;align-items:center;justify-content:center;">' . ($index + 1) . '</div>
                                                <span style="font-size:0.83rem;font-weight:600;color:#1e293b;line-height:1.3;">' . e($requirement->name) . '</span>
                                            </div>
                                        '))
                                        ->columnSpan(3),

                                    Forms\Components\FileUpload::make("files.{$requirement->id}.file_path")
                                        ->label('')
                                        ->directory('requirements')
                                        ->disk('local')
                                        ->maxSize(5120)
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->helperText('PDF only · Max 5MB')
                                        ->columnSpan(4),

                                    Forms\Components\TextInput::make("files.{$requirement->id}.notes")
                                        ->label('')
                                        ->maxLength(500)
                                        ->placeholder('Add a note...')
                                        ->prefixIcon('heroicon-m-chat-bubble-left-ellipsis')
                                        ->columnSpan(3),

                                    Forms\Components\Toggle::make("files.{$requirement->id}.is_submitted")
                                        ->label('')
                                        ->default(false)
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->inline(true)
                                        ->columnSpan(2),
                                ]);
                        }

                        return $schema;
                    })
                    ->action(function (array $data) {
                        $applicant = $this->getOwnerRecord();
                        $files     = $data['files'] ?? [];
                        $submitted = 0;
                        $saved     = 0;

                        foreach ($files as $requirementId => $values) {
                            $isSubmitted = !empty($values['is_submitted']);
                            $hasFile     = !empty($values['file_path']);

                            if (! $isSubmitted && ! $hasFile) {
                                continue;
                            }

                            $applicant->submittedRequirements()->updateExistingPivot($requirementId, [
                                'file_path'    => $values['file_path'] ?? null,
                                'is_submitted' => $isSubmitted,
                                'notes'        => $values['notes'] ?? null,
                            ]);
                            $saved++;
                            if ($isSubmitted) {
                                $submitted++;
                            }
                        }

                        // Refresh reactive progress properties
                        $this->refreshProgress();
                        $this->resetTable();

                        $total   = $this->progressTotal;
                        $allDone = $this->progressDone;

                        if ($allDone === $total && $total > 0) {
                            Notification::make()
                                ->title('🎉 All Requirements Complete!')
                                ->success()
                                ->body('This applicant has submitted all required documents.')
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Requirements Saved')
                                ->success()
                                ->body("{$submitted} of {$saved} requirement(s) marked as submitted. {$allDone}/{$total} total complete.")
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->modalHeading(fn (Model $record) => $record->name)
                        ->using(function (Model $record, array $data): Model {
                            $this->getOwnerRecord()
                                ->submittedRequirements()
                                ->updateExistingPivot($record->id, [
                                    'file_path'    => $data['file_path'] ?? null,
                                    'is_submitted' => $data['is_submitted'] ?? false,
                                    'notes'        => $data['notes'] ?? null,
                                ]);
                            return $record;
                        })
                        ->after(function () {
                            $this->refreshProgress();
                            $this->resetTable();
                        })
                        ->successNotificationTitle('Requirement updated'),

                    Tables\Actions\Action::make('download')
                        ->label('Download File')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(fn ($record) => response()->download(
                            storage_path('app/' . $record->pivot->file_path)
                        ))
                        ->visible(fn ($record) => !empty($record->pivot->file_path)),

                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) => $record->pivot->is_submitted ? 'Mark Pending' : 'Mark Submitted')
                        ->icon(fn ($record) => $record->pivot->is_submitted ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-check')
                        ->color(fn ($record) => $record->pivot->is_submitted ? 'warning' : 'success')
                        ->action(function ($record) {
                            $this->getOwnerRecord()
                                ->submittedRequirements()
                                ->updateExistingPivot($record->id, [
                                    'is_submitted' => ! $record->pivot->is_submitted,
                                ]);

                            $this->refreshProgress();
                            $this->resetTable();

                            Notification::make()
                                ->title('Status updated')
                                ->success()
                                ->send();
                        }),
                ])
                ->icon('heroicon-m-ellipsis-horizontal')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('mark_submitted')
                    ->label('Mark Submitted')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $this->getOwnerRecord()
                                ->submittedRequirements()
                                ->updateExistingPivot($record->id, ['is_submitted' => true]);
                        }
                        $this->refreshProgress();
                        $this->resetTable();
                        Notification::make()->title('Marked as submitted')->success()->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('mark_pending')
                    ->label('Mark Pending')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $this->getOwnerRecord()
                                ->submittedRequirements()
                                ->updateExistingPivot($record->id, ['is_submitted' => false]);
                        }
                        $this->refreshProgress();
                        $this->resetTable();
                        Notification::make()->title('Marked as pending')->warning()->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->striped()
            ->defaultSort('name', 'asc')
            ->emptyStateHeading('No requirements configured')
            ->emptyStateDescription('No requirements are set up for this application type yet.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }
}