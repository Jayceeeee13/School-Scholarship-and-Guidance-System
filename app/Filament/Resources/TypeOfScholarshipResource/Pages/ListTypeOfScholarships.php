<?php

namespace App\Filament\Resources\TypeOfScholarshipResource\Pages;

use App\Filament\Resources\ApplicantResource;
use App\Filament\Resources\TypeOfScholarshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypeOfScholarships extends ListRecords
{
    protected static string $resource = TypeOfScholarshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Applicants')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(ApplicantResource::getUrl('index', ['activeTab' => 'scholarship_types'])),

            Actions\CreateAction::make(),
        ];
    }
}