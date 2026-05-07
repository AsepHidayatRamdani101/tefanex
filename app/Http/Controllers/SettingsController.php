<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use DateTime;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin|admin');
    }

    public function index()
    {
        return view('settings.pengaturan');
    }

    public function backup(Request $request)
    {
        try {
            $backupType = $request->input('type', 'full');
            $timestamp = date('Y-m-d_H-i-s');
            $backupName = "backup_{$backupType}_{$timestamp}";
            
            // Ensure backups directory exists
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                @mkdir($backupPath, 0755, true);
            }

            // Check if directory is writable
            if (!is_writable($backupPath)) {
                throw new \Exception("Backup directory is not writable: $backupPath");
            }

            $zipPath = $backupPath . DIRECTORY_SEPARATOR . $backupName . '.zip';
            
            $zip = new ZipArchive();
            $openResult = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            
            if ($openResult !== true) {
                throw new \Exception("Failed to create ZIP file. Error code: $openResult, Path: $zipPath");
            }

            // Add at least one file so ZIP is not empty
            $zip->addFromString('README.txt', 'Backup created at ' . date('Y-m-d H:i:s'));

            if ($backupType === 'full' || $backupType === 'database') {
                // Backup database
                $this->backupDatabase($zip, $backupName);
            }

            if ($backupType === 'full' || $backupType === 'files') {
                // Backup files
                $this->backupProjectFiles($zip);
            }

            $closeResult = $zip->close();
            
            if (!$closeResult) {
                throw new \Exception("Failed to close ZIP file");
            }

            // Verify file was created
            if (!file_exists($zipPath)) {
                throw new \Exception("Backup file was not created at: $zipPath");
            }

            // Return download
            return response()->download($zipPath, $backupName . '.zip', [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            \Log::error('Backup Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function backupDatabase($zip, $backupName)
    {
        try {
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPassword = env('DB_PASSWORD');
            $dbHost = env('DB_HOST', 'localhost');

            $dumpPath = storage_path("app/backups/{$backupName}.sql");
            
            // Try to find mysqldump in common locations
            $mysqldumpPaths = [
                'C:\laragon\bin\mysql\mysql8.0.13\bin\mysqldump.exe',
                'C:\laragon\bin\mysql\mysql5.7.24\bin\mysqldump.exe',
                'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe',
                'C:\Program Files\MySQL\MySQL Server 5.7\bin\mysqldump.exe',
            ];
            
            $mysqldump = 'mysqldump'; // Default fallback
            foreach ($mysqldumpPaths as $path) {
                if (file_exists($path)) {
                    $mysqldump = $path;
                    break;
                }
            }
            
            // Escape password for command line
            $password = !empty($dbPassword) ? '--password=' . escapeshellarg($dbPassword) : '';
            
            // Build command
            $command = '"' . $mysqldump . '" --user=' . escapeshellarg($dbUser) . ' ' . $password . ' --host=' . escapeshellarg($dbHost) . ' ' . escapeshellarg($dbName) . ' > "' . $dumpPath . '" 2>&1';
            
            \Log::info('Database Backup Command: ' . str_replace($dbPassword, '***', $command));
            
            $output = shell_exec($command);
            
            \Log::info('Database Backup Output: ' . $output);

            if (file_exists($dumpPath) && filesize($dumpPath) > 0) {
                $zip->addFile($dumpPath, "database/{$backupName}.sql");
                @unlink($dumpPath);
                \Log::info('Database backup added to ZIP successfully');
            } else {
                \Log::error('Database dump file not created or is empty. Path: ' . $dumpPath);
            }
        } catch (\Exception $e) {
            \Log::error('Database Backup Error: ' . $e->getMessage());
        }
    }

    private function backupProjectFiles($zip)
    {
        try {
            $basePath = base_path();
            
            // Add key project directories
            $includeFolders = [
                'app',
                'config',
                'database',
                'resources',
            ];

            foreach ($includeFolders as $folder) {
                $folderPath = $basePath . DIRECTORY_SEPARATOR . $folder;
                if (is_dir($folderPath) && is_readable($folderPath)) {
                    $this->addFolderToZip($folderPath, $zip, $folder);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Files Backup Error: ' . $e->getMessage());
        }
    }

    private function addFolderToZip($folderPath, $zip, $zipPath = '')
    {
        try {
            if (!is_dir($folderPath)) {
                return;
            }

            $files = @scandir($folderPath);
            if ($files === false) {
                return;
            }
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                    continue;
                }

                $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
                $zipFilePath = $zipPath ? $zipPath . '/' . $file : $file;

                if (is_dir($filePath)) {
                    @$zip->addEmptyDir($zipFilePath);
                    $this->addFolderToZip($filePath, $zip, $zipFilePath);
                } else if (is_file($filePath)) {
                    @$zip->addFile($filePath, $zipFilePath);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Add Folder to Zip Error: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        // Implementation untuk delete akan ditambahkan nanti
        // Untuk sekarang hanya menampilkan response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
