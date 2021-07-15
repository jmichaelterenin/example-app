<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert(
            'INSERT INTO blocks (starts_at, length, block_type, clinic_id, patient_id) VALUES ( ?, ?, ?, ?, ?)',
            ['2021-08-10 11:00:00', 60, 'appointment', 10425, 899]
        );
        DB::insert(
            'INSERT INTO blocks (starts_at, length, block_type, clinic_id, patient_id) VALUES ( ?, ?, ?, ?, ?)',
            ['2021-08-10 09:30:00', 30, 'appointment', 10425, 753]
        );
        DB::insert(
            'INSERT INTO blocks (starts_at, length, block_type, clinic_id, patient_id) VALUES ( ?, ?, ?, ?, ?)',
            ['2021-08-11 10:00:00', 90, 'appointment', 10425, 555]
        );
        DB::insert(
            'INSERT INTO blocks (starts_at, length, block_type, clinic_id, patient_id) VALUES ( ?, ?, ?, ?, ?)',
            ['2021-08-10 11:30:00', 90, 'appointment', 10425, 555]
        );
        DB::insert(
            'INSERT INTO blocks (starts_at, length, block_type, clinic_id, patient_id) VALUES ( ?, ?, ?, ?, ?)',
            ['2021-08-10 13:00:00', 30, 'clinic', 10425, NULL]
        );
        DB::insert(
            'INSERT INTO blocks (starts_at, length, block_type, clinic_id, patient_id) VALUES ( ?, ?, ?, ?, ?)',
            ['2021-08-10 10:00:00', 60, 'appointment', 51300, 667]
        );
        DB::insert(
            'INSERT INTO blocks (starts_at, length, block_type, clinic_id, patient_id) VALUES ( ?, ?, ?, ?, ?)',
            ['2021-08-10 16:00:00', 60, 'clinic', 10425, NULL]
        );
    }
}