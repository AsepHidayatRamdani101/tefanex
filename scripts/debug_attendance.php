<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$from = $argv[1] ?? null;
$to = $argv[2] ?? null;

echo "Debug attendance filter from=" . ($from ?? '[null]') . " to=" . ($to ?? '[null]') . "\n";

$students = App\Models\Siswa::with(['user','kelas'])->whereNotNull('user_id')->get();
$userIds = $students->pluck('user_id')->filter()->values();

$attendanceQuery = App\Models\Attendance::query()
    ->when($userIds->isNotEmpty(), function ($q) use ($userIds) {
        $q->whereIn('user_id', $userIds);
    }, function ($q) {
        $q->whereRaw('1 = 0');
    });

if ($from && $to) {
    $attendanceQuery = $attendanceQuery->whereBetween('date', [$from, $to]);
} elseif ($from) {
    $attendanceQuery = $attendanceQuery->whereDate('date', '>=', $from);
} elseif ($to) {
    $attendanceQuery = $attendanceQuery->whereDate('date', '<=', $to);
}

$attendanceGrouped = $attendanceQuery
    ->selectRaw('user_id, status, COUNT(*) as total')
    ->groupBy('user_id', 'status')
    ->get()
    ->groupBy('user_id');

echo "Students count: " . count($students) . "\n";
echo "Attendance groups found: " . count($attendanceGrouped) . "\n";
foreach ($attendanceGrouped as $uid => $rows) {
    echo "user_id {$uid}: " . json_encode($rows->toArray()) . "\n";
}

// Also print SQL for manual check
try {
    $sql = $attendanceQuery->toSql();
    echo "Generated SQL: " . $sql . "\n";
    echo "Bindings: " . json_encode($attendanceQuery->getBindings()) . "\n";
} catch (Exception $e) {
    echo "Could not get SQL: " . $e->getMessage() . "\n";
}


