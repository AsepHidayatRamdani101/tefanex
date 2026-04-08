<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class ConvertCsvToExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-csv-to-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert CSV template to Excel with table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvFile = public_path('templates/template_pertanyaan.csv');
        $data = array_map('str_getcsv', file($csvFile));
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Pertanyaan');
        
        // Add data
        foreach ($data as $rowIndex => $row) {
            foreach ($row as $colIndex => $cell) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $cell);
            }
        }
        
        // Create table
        $table = new Table('A1:G' . count($data), 'Table1');
        $table->setShowHeaderRow(true);
        $table->setShowTotalsRow(false);
        $tableStyle = new TableStyle();
        $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
        $tableStyle->setShowRowStripes(true);
        $table->setStyle($tableStyle);
        $sheet->addTable($table);
        
        // Auto size columns
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filePath = storage_path('app/public/template_pertanyaan.xlsx');
        $writer->save($filePath);
        
        $this->info('Excel file with table created: storage/app/public/template_pertanyaan.xlsx');
    }
}
