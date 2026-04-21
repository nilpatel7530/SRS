<?php

use Modules\Projects\Models\Project;
use Modules\Projects\Models\ProjectTarget;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(Project::all() as $p) {
    ProjectTarget::updateOrCreate(
        ['project_id' => $p->id, 'financial_year' => '2026-27'],
        [
            'billed_prev_fy' => rand(1000000, 5000000),
            'apr' => rand(100000, 500000),
            'may' => rand(100000, 500000),
            'jun' => rand(100000, 500000),
            'jul' => rand(100000, 500000),
            'aug' => rand(100000, 500000),
            'sep' => rand(100000, 500000),
            'oct' => rand(100000, 500000),
            'nov' => rand(100000, 500000),
            'dec' => rand(100000, 500000),
            'jan' => rand(100000, 500000),
            'feb' => rand(100000, 500000),
            'mar' => rand(100000, 500000),
            'remarks' => 'Generated target for FY 26-27'
        ]
    );
}

echo "Seeded " . Project::count() . " project targets.\n";
