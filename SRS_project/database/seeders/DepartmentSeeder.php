<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'IT Services'],
            ['name' => 'Engineering'],
            ['name' => 'Marketing'],
            ['name' => 'Operation'],
            ['name' => 'Finance'],
        ];

        foreach ($departments as $dept) {
            \Modules\Projects\Models\Department::firstOrCreate($dept);
        }
    }
}
