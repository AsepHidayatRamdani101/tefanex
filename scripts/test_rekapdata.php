<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\AttendanceController;

$from = $argv[1] ?? null;
$to = $argv[2] ?? null;

$request = Request::create('/attendances-rekap-data', 'GET', [
    'date_from' => $from,
    'date_to' => $to,
]);

$controller = new AttendanceController();
$response = $controller->rekapData($request);

if ($response instanceof Illuminate\Http\JsonResponse) {
    echo $response->getContent();
} else {
    echo "Unexpected response type: ";
    var_export($response);
}
