<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReporteExport implements Export, WithMultipleSheets
{
    protected array $datos;

    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    public function sheets(): array
    {
        return [
            'Indicadores' => new IndicadoresSheet($this->datos),
            'Estados'     => new EstadosSheet($this->datos),
            'Materiales'  => new MaterialesSheet($this->datos),
            'Rankings'    => new RankingsSheet($this->datos),
        ];
    }
}

// ============================================================
// HOJA 1: INDICADORES
// ============================================================
class IndicadoresSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(protected array $datos) {}

    public function title(): string { return 'Indicadores'; }

    public function headings(): array { return ['Indicador', 'Valor']; }

    public function array(): array
    {
        $i = $this->datos['indicadores'];
        return [
            ['Total Publicaciones', $i['totalPublicaciones']],
            ['Total Solicitudes',   $i['totalSolicitudes']],
            ['Tasa de Aceptación',  $i['tasaAceptacion'] . '%'],
            ['Total Usuarios',      $i['totalUsuarios']],
            ['Periodo',             $this->datos['desde'] . ' al ' . $this->datos['hasta']],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 25,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('A1:B6')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ]);

                $sheet->getStyle('B2:B5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '1E40AF'], 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A6:B6')->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['rgb' => '666666']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }
}

// ============================================================
// HOJA 2: ESTADOS
// ============================================================
class EstadosSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(protected array $datos) {}

    public function title(): string { return 'Estados'; }

    public function headings(): array { return ['Tipo', 'Estado', 'Cantidad']; }

    public function array(): array
    {
        $rows = [];
        foreach ($this->datos['publicacionesPorEstado'] as $estado => $total) {
            $rows[] = ['Publicación', $estado, $total];
        }
        foreach ($this->datos['solicitudesPorEstado'] as $estado => $total) {
            $rows[] = ['Solicitud', $estado, $total];
        }
        return $rows ?: [['Sin datos', '-', 0]];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 15,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle('A1:C' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ]);

                $sheet->getStyle('C2:C' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }
}

// ============================================================
// HOJA 3: MATERIALES
// ============================================================
class MaterialesSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(protected array $datos) {}

    public function title(): string { return 'Materiales'; }

    public function headings(): array { return ['Unidad de Medida', 'Cantidad Total', 'N° Intercambios']; }

    public function array(): array
    {
        $rows = [];
        foreach ($this->datos['materialesIntercambiados'] as $m) {
            $rows[] = [$m->unidadMedida, (float) $m->total_cantidad, $m->total_intercambios];
        }
        return $rows ?: [['Sin datos', 0, 0]];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle('A1:C' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ]);

                $sheet->getStyle('B2:B' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('B2:C' . $lastRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'font' => ['color' => ['rgb' => '1E40AF'], 'bold' => true],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }
}

// ============================================================
// HOJA 4: RANKINGS
// ============================================================
class RankingsSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(protected array $datos) {}

    public function title(): string { return 'Rankings'; }

    public function headings(): array { return ['Tipo', 'Nombre', 'Total']; }

    public function array(): array
    {
        $rows = [];
        foreach ($this->datos['topEmpresas'] as $e) {
            $rows[] = ['Empresa', $e->nombreEmpresa, $e->publicaciones_count];
        }
        foreach ($this->datos['topMateriales'] as $m) {
            $rows[] = ['Material', $m->nombre, $m->total];
        }
        foreach ($this->datos['porCategoria'] as $c) {
            $rows[] = ['Categoría', $c->nombre, $c->total];
        }
        return $rows ?: [['Sin datos', '-', 0]];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 40,
            'C' => 15,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle('A1:C' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ]);

                // Colorear el tipo según la fila (tonos azules)
                for ($i = 2; $i <= $lastRow; $i++) {
                    $tipo = $sheet->getCell('A' . $i)->getValue();
                    $color = match($tipo) {
                        'Empresa'   => 'DBEAFE',
                        'Material'  => 'BFDBFE',
                        'Categoría' => '93C5FD',
                        default     => 'F3F4F6',
                    };
                    $sheet->getStyle('A' . $i)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                        'font' => ['bold' => true, 'color' => ['rgb' => '1E3A8A']],
                    ]);
                }

                $sheet->getStyle('C2:C' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }
}