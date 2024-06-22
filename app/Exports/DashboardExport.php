<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\{
    Exportable,
    FromQuery,
    WithTitle,
    WithEvents,
    WithMapping,
    WithHeadings,
    WithColumnFormatting,
    ShouldAutoSize,
};
use PhpOffice\PhpSpreadsheet\Style\{
    NumberFormat,
    Alignment, 
    Border,
    Fill,
};
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\ExportDashboard;
use Carbon\Carbon;

class DashboardExport implements FromQuery, WithTitle, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents, WithMapping, Responsable
{
    use Exportable;

    private $fileName = 'Sales_Report.xlsx';

    private $writerType = Excel::XLSX;

    private $headers = [
        'Content-Type' => 'application/xlsx', 
    ];

    public function title(): string
    {
        return 'Sales Report';
    }

    public function query()
    {
        return ExportDashboard::query()->orderBy('Sales_ID');
    }

    public function headings(): array
    {
        return [
            'Customer Code',
            'Customer', 
            'Product', 
            'Transaction Type', 
            'Year', 
            'Group Type', 
            'Prospect Type', 
            'Strategic Initiative', 
            'Sales Type', 
            'Rate',
            'Flight Hour',
            'Project Manager', 
            'Hangar',
            'Line',
            'AMS',
            'Area',
            'Country',
            'Region',
            'AC/ENG/APU/COMP',
            'AC-Reg',
            'Maintenance Event',
            'Market Share',
            'Sales Plan',
            'Remarks',
            'SO Number',
            'Start Date',
            'TAT',
            'End Date',
            'Sales Level',
            'Status',
            'Cancel Reason',
            'Detailed Cancel Reason'
        ];
    }

    public function map($row): array
    {
        return [
            $row->Customer_Code,
            $row->Customer,
            $row->Product,
            $row->Transaction_Type,
            $row->Year,
            $row->Group_Type,
            $row->Prospect_Type,
            $row->Strategic_Initiative,
            $row->Sales_Type,
            $row->Rate,
            $row->Flight_Hour,
            $row->Project_Manager,
            $row->Hangar,
            $row->Line,
            $row->AMS,
            $row->Area,
            $row->Country,
            $row->Region,
            $row->Acengapucomp,
            $row->AC_Reg,
            $row->Maintenance_Event,
            $row->Market_Share,
            $row->Sales_Plan,
            $row->Remarks,
            $row->SO_Number,
            Date::dateTimeToExcel(Carbon::parse($row->Start_Date)),
            $row->TAT,
            Date::dateTimeToExcel(Carbon::parse($row->End_Date)),
            $row->Sales_Level,
            $row->Status,
            $row->Cancel_Reason,
            $row->Detailed_Cancel_Reason
        ];
    }

    public function columnFormats(): array
    {
        return [
            'V' => '#,##0',
            'W' => '#,##0',
            'Z' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'AB' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                $max_row = $sheet->getDelegate()->getHighestRow();
                $sheet->getDelegate()->freezePane('A2');

                $sheet->getStyle('A1:AF1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'b4b4b4']
                    ],
                ]);

                $sheet->getStyle("A1:AF{$max_row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000']
                        ],
                    ],
                ]);
            }
        ];
    }
}