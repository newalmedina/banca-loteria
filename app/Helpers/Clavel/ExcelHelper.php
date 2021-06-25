<?php namespace App\Helpers\Clavel;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelHelper
{
    public static function cellColor($worksheet, $cells, $color)
    {
        $style = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF'.$color,
                ]
            ],
        ];

        $worksheet->getStyle($cells)->applyFromArray($style);
    }

    // Genera el listado de columnas a procesar en un array de contadores
    public static function xrange($start, $end, $limit = 1000)
    {
        $listado = array();
        $count = 0;
        while ($start !== $end && $count < $limit) {
            $listado[] = $start;
            $start ++;
            $count++;
        }
        $listado[] = $end;
        return $listado;
    }

    public static function autoSizeHeader($worksheet, $cabeceras, $row, $color)
    {
        $j = 1;
        foreach ($cabeceras as $titulo) {
            $worksheet->setCellValueByColumnAndRow($j++, $row, $titulo);
        }

        $columna_final = Coordinate::stringFromColumnIndex($j - 1);

        $worksheet->getStyle('A' . $row . ':' . $columna_final . $row)->getFont()->setBold(true);
        $worksheet->getStyle('A' . $row . ':' . $columna_final . $row)->getFont()->setSize(14);

        self::cellColor($worksheet, 'A' . $row . ':' . $columna_final . $row, $color);

        foreach (ExcelHelper::xrange('A', $columna_final) as $columnID) {
            $worksheet->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
    }

    public static function autoSizeCurrentRow($worksheet, $col_ini = "A", $col_end = "AB")
    {
        foreach (range($col_ini, $col_end) as $columnID) {
            $worksheet->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
    }

    public static function downloadFile($spreadsheet, $file_name, $outPath)
    {
        $writer = new Xlsx($spreadsheet);

        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }
        $writer->save($outPath . $file_name . '.xlsx');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx' . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
