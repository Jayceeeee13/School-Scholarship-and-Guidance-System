<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $databaseName = DB::getDatabaseName();

        // ── 1. Find and drop the actual FK on scholar_id (whatever it's really named) ──
        $foreignKey = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'accomplishment_reports'
              AND COLUMN_NAME = 'scholar_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$databaseName]);

        if ($foreignKey) {
            Schema::table('accomplishment_reports', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            });
        }

        // ── 2. Find and drop the actual unique constraint on (scholar_id, term_id) ──
        $uniqueKey = DB::selectOne("
            SELECT DISTINCT s.INDEX_NAME
            FROM information_schema.STATISTICS s
            WHERE s.TABLE_SCHEMA = ?
              AND s.TABLE_NAME = 'accomplishment_reports'
              AND s.NON_UNIQUE = 0
              AND s.INDEX_NAME != 'PRIMARY'
            LIMIT 1
        ", [$databaseName]);

        if ($uniqueKey) {
            Schema::table('accomplishment_reports', function (Blueprint $table) use ($uniqueKey) {
                $table->dropUnique($uniqueKey->INDEX_NAME);
            });
        }

        // ── 3. Add the polymorphic type column (skip if it already exists) ──
        if (! Schema::hasColumn('accomplishment_reports', 'scholar_type')) {
            Schema::table('accomplishment_reports', function (Blueprint $table) {
                $table->string('scholar_type')->after('scholar_id');
            });
        }

        // ── 4. Backfill existing rows ─────────────────────────────────────
        // Every row created before this migration belonged to the
        // Scholars model, since InstitutionalScholar support didn't
        // exist yet.
        DB::table('accomplishment_reports')->update([
            'scholar_type' => \App\Models\Scholars::class,
        ]);

        // ── 5. Re-add uniqueness, now scoped per scholar type + id + term ─
        Schema::table('accomplishment_reports', function (Blueprint $table) {
            $table->unique(['scholar_type', 'scholar_id', 'term_id'], 'unique_scholar_term');
        });
    }

    public function down(): void
    {
        Schema::table('accomplishment_reports', function (Blueprint $table) {
            $table->dropUnique('unique_scholar_term');
        });

        Schema::table('accomplishment_reports', function (Blueprint $table) {
            $table->dropColumn('scholar_type');
        });

        Schema::table('accomplishment_reports', function (Blueprint $table) {
            $table->unique(['scholar_id', 'term_id'], 'unique_scholar_term');

            $table->foreign('scholar_id', 'fk_accomplishment_reports_scholar')
                ->references('id')->on('scholars')
                ->cascadeOnDelete();
        });
    }
};