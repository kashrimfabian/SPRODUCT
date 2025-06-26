<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ProfitLossExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    protected $reportData;
    protected $totalSales;
    protected $totalExpenses;
    protected $totalAlizetiCost;
    protected $totalFilteringRevenue; 
    protected $overallProfitLoss;
    protected $startDate;
    protected $endDate;

    public function __construct(array $reportData, $totalSales, $totalExpenses, $totalAlizetiCost, $totalFilteringRevenue, $overallProfitLoss, $startDate, $endDate)
    {
        $this->reportData = $reportData;
        $this->totalSales = $totalSales;
        $this->totalExpenses = $totalExpenses;
        $this->totalAlizetiCost = $totalAlizetiCost;
        $this->totalFilteringRevenue = $totalFilteringRevenue;
        $this->overallProfitLoss = $overallProfitLoss;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $rows = new Collection();


        $rows->push(['Profit & Loss Report']);
        $rows->push(['Report Period:', ($this->startDate ?? 'N/A') . ' - ' . ($this->endDate ?? 'N/A')]);
        $rows->push([]);

       
        $rows->push([
            'Date', 
            'Total Sales (TZS)', 
            'Total Expenses (TZS)', 
            'Total Alizeti Cost (TZS)', 
            'Total Filtering Revenue (TZS)', 
            'Total Gunia', 
            'Total Kilograms', 
            'Daily Profit/Loss (TZS)'
        ]);

        
        foreach ($this->reportData as $data) {
            $rows->push([
                $data['date'],
                $data['total_sales'],
                $data['total_expenses'],
                $data['total_alizeti_cost'],
                $data['total_filtering_revenue'],
                $data['total_gunia'],
                $data['total_kilogram'],
                $data['daily_profit_loss'], 
            ]);
        }

        $rows->push([]); 
        
        $rows->push([
            'Overall Totals',
            $this->totalSales,
            $this->totalExpenses,
            $this->totalAlizetiCost,
            $this->totalFilteringRevenue, 
            '', 
            '',
            $this->overallProfitLoss
        ]);

        return $rows;
    }

   
    public function headings(): array
    {
        return []; 
    }
}