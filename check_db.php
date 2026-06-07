<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$courses = App\Models\Course::select('id','name')->get();
echo "Courses:\n";
foreach ($courses as $c) echo "  {$c->id}: '{" . $c->name . "'\n";

$assignments = App\Models\Assignment::select('id','course_id','title')->get();
echo "Assignments:\n";
foreach ($assignments as $a) echo "  {$a->id}: course={->course_id} '{" . $a->title . "'\n";
