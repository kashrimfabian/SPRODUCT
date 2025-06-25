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

        // Fetch Expenses Data
        $expensesData = Expense::query()
            ->where('user_id', $userId) // Filter by authenticated user's expenses
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

        // Fetch Alizeti (Sunflower) Cost Data
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

        // NEW: Fetch Filtering Cost Data
        // IMPORTANT: Assumes 'filtering' table has a 'tarehe' column for the date of filtering.
        $filteringData = Filtering::query()
            ->selectRaw('tarehe, SUM(cost_used) as total_filtering_cost')
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
        // Update getUniqueDates to include filteringData
        $dates = $this->getUniqueDates($salesData->toArray(), $expensesData->toArray(), $alizetiData->toArray(), $filteringData->toArray());
        
        $totalSales = 0;
        $totalExpenses = 0;
        $totalAlizetiCost = 0;
        $totalFilteringCost = 0; // NEW: Initialize total filtering cost
        $overallProfitLoss = 0;

        foreach ($dates as $date) {
            $sale = $salesData[$date] ?? (object)['total_sales' => 0];
            $expense = $expensesData[$date] ?? (object)['total_expenses' => 0];
            $alizeti = $alizetiData[$date] ?? (object)['total_alizeti_cost' => 0, 'total_gunia' => 0, 'total_kilogram' => 0];
            $filtering = $filteringData[$date] ?? (object)['total_filtering_cost' => 0]; // NEW: Get filtering data

            $dailySales = $sale->total_sales ?? 0;
            $dailyExpenses = $expense->total_expenses ?? 0;
            $dailyAlizetiCost = $alizeti->total_alizeti_cost ?? 0;
            $dailyGunia = $alizeti->total_gunia ?? 0;
            $dailyKilogram = $alizeti->total_kilogram ?? 0;
            $dailyFilteringCost = $filtering->total_filtering_cost ?? 0; // NEW: Get daily filtering cost


            // Update daily profit/loss calculation
            $dailyProfitLoss = $dailySales - $dailyExpenses - $dailyAlizetiCost + $dailyFilteringCost;

            $reportData[$date] = [
                'date' => $date,
                'total_sales' => $dailySales,
                'total_expenses' => $dailyExpenses,
                'total_alizeti_cost' => $dailyAlizetiCost,
                'total_gunia' => $dailyGunia,
                'total_kilogram' => $dailyKilogram,
                'total_filtering_cost' => $dailyFilteringCost, // NEW: Add to report data
                'daily_profit_loss' => $dailyProfitLoss, 
            ];

            $totalSales += $dailySales;
            $totalExpenses += $dailyExpenses;
            $totalAlizetiCost += $dailyAlizetiCost;
            $totalFilteringCost += $dailyFilteringCost; // NEW: Accumulate total filtering cost
            $overallProfitLoss += $dailyProfitLoss; 
        }

        // Update overall profit/loss calculation
        $profitLoss = $totalSales - $totalExpenses - $totalAlizetiCost + $totalFilteringCost;

        $viewData = [
            'reportData' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSales' => $totalSales,
            'totalExpenses' => $totalExpenses,
            'totalAlizetiCost' => $totalAlizetiCost,
            'totalFilteringCost' => $totalFilteringCost, // NEW: Pass to view
            'profitLoss' => $profitLoss,
        ];

        return view('reports.profit-loss', $viewData);
    }

    /**
     * Export the Profit/Loss report to Excel, PDF, or CSV.
     */
    public function exportProfitLoss(Request $request)
    {
        $type = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = Auth::id();

        // Fetch Sales Data
        $salesData = Mauzo::query()->selectRaw('tarehe, SUM(total_price) as total_sales')->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })->groupBy('tarehe')->get()->keyBy('tarehe');

        // Fetch Expenses Data
        $expensesData = Expense::query()->where('user_id', $userId)->selectRaw('tarehe, SUM(amount) as total_expenses')->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })->groupBy('tarehe')->get()->keyBy('tarehe');

        // Fetch Alizeti (Sunflower) Cost Data
        $alizetiData = Alizeti::query()->selectRaw('tarehe, SUM(gunia_total) as total_gunia, SUM(al_kilogram) as total_kilogram, SUM(total_price) as total_alizeti_cost')->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })->groupBy('tarehe')->get()->keyBy('tarehe');
        
        // NEW: Fetch Filtering Cost Data
        $filteringData = Filtering::query()
            ->selectRaw('tarehe, SUM(cost_used) as total_filtering_cost')
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
        // Update getUniqueDates to include filteringData
        $dates = $this->getUniqueDates($salesData->toArray(), $expensesData->toArray(), $alizetiData->toArray(), $filteringData->toArray());

        $totalSales = 0;
        $totalExpenses = 0;
        $totalAlizetiCost = 0;
        $totalFilteringCost = 0; // NEW: Initialize total filtering cost
        $overallProfitLoss = 0;

        foreach ($dates as $date) {
            $sale = $salesData[$date] ?? (object)['total_sales' => 0];
            $expense = $expensesData[$date] ?? (object)['total_expenses' => 0];
            $alizeti = $alizetiData[$date] ?? (object)['total_alizeti_cost' => 0, 'total_gunia' => 0, 'total_kilogram' => 0];
            $filtering = $filteringData[$date] ?? (object)['total_filtering_cost' => 0]; // NEW: Get filtering data

            $dailySales = $sale->total_sales ?? 0;
            $dailyExpenses = $expense->total_expenses ?? 0;
            $dailyAlizetiCost = $alizeti->total_alizeti_cost ?? 0;
            $dailyGunia = $alizeti->total_gunia ?? 0;
            $dailyKilogram = $alizeti->total_kilogram ?? 0;
            $dailyFilteringCost = $filtering->total_filtering_cost ?? 0; // NEW: Get daily filtering cost

            $dailyProfitLoss = $dailySales - $dailyExpenses - $dailyAlizetiCost + $dailyFilteringCost; // Update daily calculation

            $reportData[$date] = [
                'date' => $date,
                'total_sales' => $dailySales,
                'total_expenses' => $dailyExpenses,
                'total_alizeti_cost' => $dailyAlizetiCost,
                'total_gunia' => $dailyGunia,
                'total_kilogram' => $dailyKilogram,
                'total_filtering_cost' => $dailyFilteringCost, // NEW: Add to report data
                'daily_profit_loss' => $dailyProfitLoss, 
            ];

            $totalSales += $dailySales;
            $totalExpenses += $dailyExpenses;
            $totalAlizetiCost += $dailyAlizetiCost;
            $totalFilteringCost += $dailyFilteringCost; // NEW: Accumulate total filtering cost
            $overallProfitLoss += $dailyProfitLoss;
        }
        
        $profitLoss = $totalSales - $totalExpenses - $totalAlizetiCost + $totalFilteringCost; // Final overall calculation

        $filename = 'profit_loss_report_' . now()->format('YmdHis');

        switch ($type) {
            case 'excel':
                try {
                    return Excel::download(function ($excel) use ($reportData, $totalSales, $totalExpenses, $totalAlizetiCost, $totalFilteringCost, $overallProfitLoss, $startDate, $endDate) {
                        $excel->sheet('Profit Loss Report', function ($sheet) use ($reportData, $totalSales, $totalExpenses, $totalAlizetiCost, $totalFilteringCost, $overallProfitLoss, $startDate, $endDate) {
                            $sheet->fromArray([
                                ['Profit/Loss Report'],
                                ['Start Date:', $startDate],
                                ['End Date:', $endDate],
                                [],
                                // NEW COLUMN HEADER for Excel
                                ['Date', 'Total Sales (TZS)', 'Total Expenses (TZS)', 'Total Alizeti Cost (TZS)', 'Total Filtering Cost (TZS)', 'Total Gunia', 'Total Kilograms', 'Daily Profit/Loss (TZS)'],
                            ], null, 'A1', false, false);
                            
                            foreach ($reportData as $data) {
                                // NEW COLUMN for Excel data row
                                $sheet->appendRow([
                                    $data['date'],
                                    $data['total_sales'],
                                    $data['total_expenses'],
                                    $data['total_alizeti_cost'],
                                    $data['total_filtering_cost'], // NEW: Add filtering cost
                                    $data['total_gunia'],
                                    $data['total_kilogram'],
                                    $data['daily_profit_loss'],
                                ]);
                            }

                            $sheet->appendRow([]); // Empty row for spacing
                            // NEW COLUMN for Excel overall totals row
                            $sheet->appendRow([
                                'Overall Totals',
                                $totalSales,
                                $totalExpenses,
                                $totalAlizetiCost,
                                $totalFilteringCost, // NEW: Add total filtering cost
                                '', // No total for gunia in this row
                                '', // No total for kilograms in this row
                                $overallProfitLoss
                            ]);
                        });
                    }, $filename . '.xlsx');
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
                        'totalFilteringCost' => $totalFilteringCost, // NEW: Pass to PDF view
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
                return response()->streamDownload(function () use ($reportData, $totalSales, $totalExpenses, $totalAlizetiCost, $totalFilteringCost, $overallProfitLoss) {
                    $file = fopen('php://output', 'w');
                    
                    // CSV Headers - NEW COLUMN for CSV header
                    fputcsv($file, ['Date', 'Total Sales (TZS)', 'Total Expenses (TZS)', 'Total Alizeti Cost (TZS)', 'Total Filtering Cost (TZS)', 'Total Gunia', 'Total Kilograms', 'Daily Profit/Loss (TZS)']);

                    // Report data rows
                    foreach ($reportData as $data) {
                        // NEW COLUMN for CSV data row
                        fputcsv($file, [
                            $data['date'],
                            $data['total_sales'],
                            $data['total_expenses'],
                            $data['total_alizeti_cost'],
                            $data['total_filtering_cost'], // NEW: Add filtering cost
                            $data['total_gunia'],
                            $data['total_kilogram'],
                            $data['daily_profit_loss'],
                        ]);
                    }

                    // Overall Totals Row - NEW COLUMN for CSV overall totals row
                    fputcsv($file, []); // Empty row for spacing
                    fputcsv($file, [
                        'Overall Totals',
                        $totalSales,
                        $totalExpenses,
                        $totalAlizetiCost,
                        $totalFilteringCost, // NEW: Add total filtering cost
                        '', // No total for gunia in this row
                        '', // No total for kilograms in this row
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