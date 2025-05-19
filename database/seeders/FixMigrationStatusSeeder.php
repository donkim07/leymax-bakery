<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixMigrationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // List of migrations that need to be marked as completed
        $migrations = [
            '2025_05_19_045001_create_assembly_categories_table',
            '2025_05_19_045011_create_assembly_groups_table',
            '2025_05_19_045028_create_assembly_sizes_table',
            '2025_05_19_045045_add_assembly_fields_to_assembled_items_table',
        ];

        $batch = DB::table('migrations')->max('batch') + 1;

        foreach ($migrations as $migration) {
            // Check if migration already exists
            $exists = DB::table('migrations')
                ->where('migration', $migration)
                ->exists();
                
            if (!$exists) {
                $this->command->info("Marking migration {$migration} as run");
                
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $batch,
                ]);
            } else {
                $this->command->info("Migration {$migration} already recorded");
            }
        }
    }
} 