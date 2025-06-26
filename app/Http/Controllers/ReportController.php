<?php

namespace App\Http\Controllers;

use App\Models\Alizeti;
use App\Models\Expense;
use App\Models\Mauzo;
use App\Models\Filtering; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\ProfitLossExport; 
use Barryvdh\DomPDF\Facade\Pdf; 
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
   
    public function profitLoss(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = Auth::id();

        
        $salesData = Mauzo::query()
            ->selectRaw('tarehe, SUM(total_price) as total_sales')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })
            ->groupBy('tarehe')
            ->get()
            ->keyBy('tarehe');

        
        $expensesData = Expense::query()
            ->where('user_id', $userId) 
            ->selectRaw('tarehe, SUM(amount) as total_expenses')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })
            ->groupBy('tarehe')
            ->get()
            ->keyBy('tarehe');

        
        $alizetiData = Alizeti::query()
            ->selectRaw('tarehe, SUM(gunia_total) as total_gunia, SUM(al_kilogram) as total_kilogram, SUM(total_price) as total_alizeti_cost')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })
            ->groupBy('tarehe')
            ->get()
            ->keyBy('tarehe');

        
        $filteringData = Filtering::query()
            ->selectRaw('tarehe, SUM(cost_used) as total_filtering_revenue')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })
            ->groupBy('tarehe')
            ->get()
            ->keyBy('tarehe');


        $reportData = [];
        
        $dates = $this->getUniqueDates($salesData->toArray(), $expensesData->toArray(), $alizetiData->toArray(), $filteringData->toArray());
        
        $totalSales = 0;
        $totalExpenses = 0;
        $totalAlizetiCost = 0;
        $totalFilteringRevenue = 0; 
        $overallProfitLoss = 0;

        foreach ($dates as $date) {
            $sale = $salesData[$date] ?? (object)['total_sales' => 0];
            $expense = $expensesData[$date] ?? (object)['total_expenses' => 0];
            $alizeti = $alizetiData[$date] ?? (object)['total_alizeti_cost' => 0, 'total_gunia' => 0, 'total_kilogram' => 0];
            $filtering = $filteringData[$date] ?? (object)['total_filtering_revenue' => 0]; 

            $dailySales = $sale->total_sales ?? 0;
            $dailyExpenses = $expense->total_expenses ?? 0;
            $dailyAlizetiCost = $alizeti->total_alizeti_cost ?? 0;
            $dailyGunia = $alizeti->total_gunia ?? 0;
            $dailyKilogram = $alizeti->total_kilogram ?? 0;
            $dailyFilteringRevenue = $filtering->total_filtering_revenue ?? 0; 

            
            $dailyProfitLoss = $dailySales - $dailyExpenses - $dailyAlizetiCost + $dailyFilteringRevenue;

            $reportData[$date] = [
                'date' => $date,
                'total_sales' => $dailySales,
                'total_expenses' => $dailyExpenses,
                'total_alizeti_cost' => $dailyAlizetiCost,
                'total_gunia' => $dailyGunia,
                'total_kilogram' => $dailyKilogram,
                'total_filtering_revenue' => $dailyFilteringRevenue, 
                'daily_profit_loss' => $dailyProfitLoss, 
            ];

            $totalSales += $dailySales;
            $totalExpenses += $dailyExpenses;
            $totalAlizetiCost += $dailyAlizetiCost;
            $totalFilteringRevenue += $dailyFilteringRevenue; 
            $overallProfitLoss += $dailyProfitLoss; 
        }

        
        $profitLoss = $totalSales - $totalExpenses - $totalAlizetiCost + $totalFilteringRevenue;

        $viewData = [
            'reportData' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSales' => $totalSales,
            'totalExpenses' => $totalExpenses,
            'totalAlizetiCost' => $totalAlizetiCost,
            'totalFilteringRevenue' => $totalFilteringRevenue, 
            'profitLoss' => $profitLoss,
        ];

        return view('reports.profit-loss', $viewData);
    }

    public function exportProfitLoss(Request $request)
    {
        $type = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = Auth::id();

        
        $salesData = Mauzo::query()->selectRaw('tarehe, SUM(total_price) as total_sales')->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })->groupBy('tarehe')->get()->keyBy('tarehe');

        $expensesData = Expense::query()->where('user_id', $userId)->selectRaw('tarehe, SUM(amount) as total_expenses')->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })->groupBy('tarehe')->get()->keyBy('tarehe');

        $alizetiData = Alizeti::query()->selectRaw('tarehe, SUM(gunia_total) as total_gunia, SUM(al_kilogram) as total_kilogram, SUM(total_price) as total_alizeti_cost')->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })->groupBy('tarehe')->get()->keyBy('tarehe');
        
        $filteringData = Filtering::query()
            ->selectRaw('tarehe, SUM(cost_used) as total_filtering_revenue')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })
            ->groupBy('tarehe')
            ->get()
            ->keyBy('tarehe');

        $reportData = [];
        $dates = $this->getUniqueDates($salesData->toArray(), $expensesData->toArray(), $alizetiData->toArray(), $filteringData->toArray());

        $totalSales = 0;
        $totalExpenses = 0;
        $totalAlizetiCost = 0;
        $totalFilteringRevenue = 0; 
        $overallProfitLoss = 0;

        foreach ($dates as $date) {
            $sale = $salesData[$date] ?? (object)['total_sales' => 0];
            $expense = $expensesData[$date] ?? (object)['total_expenses' => 0];
            $alizeti = $alizetiData[$date] ?? (object)['total_alizeti_cost' => 0, 'total_gunia' => 0, 'total_kilogram' => 0];
            $filtering = $filteringData[$date] ?? (object)['total_filtering_revenue' => 0]; 

            $dailySales = $sale->total_sales ?? 0;
            $dailyExpenses = $expense->total_expenses ?? 0;
            $dailyAlizetiCost = $alizeti->total_alizeti_cost ?? 0;
            $dailyGunia = $alizeti->total_gunia ?? 0;
            $dailyKilogram = $alizeti->total_kilogram ?? 0;
            $dailyFilteringRevenue = $filtering->total_filtering_revenue ?? 0; 

            $dailyProfitLoss = $dailySales - $dailyExpenses - $dailyAlizetiCost + $dailyFilteringRevenue;

            $reportData[$date] = [
                'date' => $date,
                'total_sales' => $dailySales,
                'total_expenses' => $dailyExpenses,
                'total_alizeti_cost' => $dailyAlizetiCost,
                'total_gunia' => $dailyGunia,
                'total_kilogram' => $dailyKilogram,
                'total_filtering_revenue' => $dailyFilteringRevenue, 
                'daily_profit_loss' => $dailyProfitLoss, 
            ];

            $totalSales += $dailySales;
            $totalExpenses += $dailyExpenses;
            $totalAlizetiCost += $dailyAlizetiCost;
            $totalFilteringRevenue += $dailyFilteringRevenue; 
            $overallProfitLoss += $dailyProfitLoss;
        }
        
        $profitLoss = $totalSales - $totalExpenses - $totalAlizetiCost + $totalFilteringRevenue; 
        
        $filename = 'profit_loss_report_' . now()->format('YmdHis');

        switch ($type) {
            case 'excel':
                try {
                    return Excel::download(
                        new ProfitLossExport(
                            $reportData, 
                            $totalSales, 
                            $totalExpenses, 
                            $totalAlizetiCost, 
                            $totalFilteringRevenue, 
                            $overallProfitLoss,
                            $startDate,
                            $endDate
                        ), 
                        $filename . '.xlsx'
                    );
                } catch (\Exception $e) {
                    Log::error('Excel generation failed: ' . $e->getMessage());
                    return back()->with('error', 'Failed to generate Excel file: ' . $e->getMessage());
                }
                break;
            case 'pdf':
                try {
                    
                    $pdf = Pdf::loadView('reports.profit-loss-pdf', [
                        'reportData' => $reportData,
                        'totalSales' => $totalSales,
                        'totalExpenses' => $totalExpenses,
                        'totalAlizetiCost' => $totalAlizetiCost,
                        'totalFilteringRevenue' => $totalFilteringRevenue, 
                        'profitLoss' => $profitLoss,
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                    ]);
                    return $pdf->download($filename . '.pdf');

                } catch (\Exception $e) {
                    Log::error('PDF generation failed: ' . $e->getMessage());
                    return back()->with('error', 'Failed to generate PDF file: ' . $e->getMessage());
                }
                break;
            case 'csv':
            default:
                return response()->streamDownload(function () use ($reportData, $totalSales, $totalExpenses, $totalAlizetiCost, $totalFilteringRevenue, $overallProfitLoss) {
                    $file = fopen('php://output', 'w');
                    
                    fputcsv($file, ['Date', 'Total Sales (TZS)', 'Total Expenses (TZS)', 'Total Alizeti Cost (TZS)', 'Total Filtering Revenue (TZS)', 'Total Gunia', 'Total Kilograms', 'Daily Profit/Loss (TZS)']);

                    foreach ($reportData as $data) {
                        fputcsv($file, [
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

                    fputcsv($file, []); 
                    fputcsv($file, [
                        'Overall Totals',
                        $totalSales,
                        $totalExpenses,
                        $totalAlizetiCost,
                        $totalFilteringRevenue, 
                        '', 
                        '', 
                        $overallProfitLoss
                    ]);

                    fclose($file);
                }, $filename . '.csv', [
                    'Content-Type' => 'text/csv',
                ]);
        }
    }
    
    private function getUniqueDates(array ...$dataArrays): array
    {
        $allDates = [];
        foreach ($dataArrays as $dataArray) {
            $allDates = array_merge($allDates, array_keys($dataArray));
        }
        $dates = array_unique($allDates);
        sort($dates); 
        return $dates;
    }
}