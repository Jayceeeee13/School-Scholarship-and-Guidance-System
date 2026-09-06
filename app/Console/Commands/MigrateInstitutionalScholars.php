<?php

namespace App\Console\Commands;

use App\Models\InstitutionalScholar;
use App\Models\Scholars;
use App\Models\TypeOfScholarship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateInstitutionalScholars extends Command
{
    protected $signature = 'scholars:migrate-institutional {--dry-run : Preview without making changes}';

    protected $description = 'Move existing institutional-type scholars from the scholars table into institutional_scholars.';

    /**
     * Types to keep in the `scholars` table even if they exist in
     * type_of_scholarships — Daily Time Record features specifically
     * query Scholars::where('type_of_scholarship', 'Student Representatives'),
     * so that type must never be moved out.
     */
    protected array $excludedTypes = [
        'Student Representatives',
    ];

    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $institutionalTypes = TypeOfScholarship::pluck('name')
            ->reject(fn ($name) => in_array($name, $this->excludedTypes, true))
            ->values();

        if ($institutionalTypes->isEmpty()) {
            $this->warn('No institutional scholarship types found (after exclusions). Nothing to migrate.');
            return self::SUCCESS;
        }

        $this->info('Institutional types to migrate: ' . $institutionalTypes->implode(', '));

        $candidates = Scholars::whereIn('type_of_scholarship', $institutionalTypes)->get();

        if ($candidates->isEmpty()) {
            $this->info('No matching scholars found. Nothing to migrate.');
            return self::SUCCESS;
        }

        $this->info("Found {$candidates->count()} scholar(s) to migrate.");

        if ($isDryRun) {
            $this->table(
                ['ID', 'Name', 'Type'],
                $candidates->map(fn ($s) => [$s->id, "{$s->first_name} {$s->last_name}", $s->type_of_scholarship])->toArray()
            );
            $this->info('Dry run only — no changes were made. Run without --dry-run to actually migrate.');
            return self::SUCCESS;
        }

        if (! $this->confirm("Move these {$candidates->count()} scholar(s) to institutional_scholars? This cannot be easily undone.")) {
            $this->warn('Cancelled.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($candidates) {
            $bar = $this->output->createProgressBar($candidates->count());
            $bar->start();

            foreach ($candidates as $scholar) {
                $attributes = $scholar->getAttributes();

                // Insert into institutional_scholars preserving the original ID
                // so existing accomplishment_reports rows still resolve correctly.
                InstitutionalScholar::insert($attributes);

                // Repoint any accomplishment reports at the new table.
                DB::table('accomplishment_reports')
                    ->where('scholar_id', $scholar->id)
                    ->where('scholar_type', Scholars::class)
                    ->update(['scholar_type' => InstitutionalScholar::class]);

                $scholar->delete();

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        });

        $this->info('Migration complete.');

        return self::SUCCESS;
    }
}