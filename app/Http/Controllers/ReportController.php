<?php

namespace App\Http\Controllers;

use App\Models\Alizeti;
use App\Models\Expense;
use App\Models\Mauzo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel; // For Excel
use Barryvdh\DomPDF\Facade\Pdf; // For PDF
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Generate Profit & Loss Report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = Auth::id();

        // 1. Sales Data (Mauzo)
        $salesData = Mauzo::query()
            ->selectRaw('tarehe, SUM(total_price) as total_sales') //  use total_price
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })
            ->groupBy('tarehe')
            ->get()
            ->keyBy('tarehe');

        // 2. Expenses Data
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

        // 3. Alizeti Data
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

        // 4. Prepare Report Data
        $reportData = [];
        $dates = $this->getUniqueDates($salesData->toArray(), $expensesData->toArray(), $alizetiData->toArray());
        //dd($dates);

        // 5. Calculate totals and populate report data
        $totalSales = 0;
        $totalExpenses = 0;
        $totalAlizetiCost = 0;

        foreach ($dates as $date) {
            $sale = $salesData[$date] ?? (object)['total_sales' => 0];
            $expense = $expensesData[$date] ?? (object)['total_expenses' => 0];
            $alizeti = $alizetiData[$date] ?? (object)['total_alizeti_cost' => 0, 'total_gunia' => 0, 'total_kilogram' => 0];

            $dailySales = $sale->total_sales ?? 0;
            $dailyExpenses = $expense->total_expenses ?? 0;
            $dailyAlizetiCost = $alizeti->total_alizeti_cost ?? 0;
            $dailyGunia = $alizeti->total_gunia ?? 0;
            $dailyKilogram = $alizeti->total_kilogram ?? 0;


            $dailyProfitLoss = $dailySales - $dailyExpenses - $dailyAlizetiCost;

            $reportData[$date] = [
                'date' => $date,
                'total_sales' => $dailySales,
                'total_expenses' => $dailyExpenses,
                'total_alizeti_cost' => $dailyAlizetiCost,
                'total_gunia' => $dailyGunia,
                'total_kilogram' => $dailyKilogram,
                'daily_profit_loss' => $dailyProfitLoss, // Include daily profit/loss
            ];

            $totalSales += $dailySales;
            $totalExpenses += $dailyExpenses;
            $totalAlizetiCost += $dailyAlizetiCost;
        }

        // 6. Calculate overall profit/loss
        $profitLoss = $totalSales - $totalExpenses - $totalAlizetiCost;


        // 7. Return the view
        $viewData = [
            'reportData' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSales' => $totalSales,
            'totalExpenses' => $totalExpenses,
            'totalAlizetiCost' => $totalAlizetiCost,
            'profitLoss' => $profitLoss,
        ];

        return view('reports.profit-loss', $viewData);
    }

    /**
     * Export Profit & Loss Report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    public function exportProfitLoss(Request $request)
    {
        ob_start(); // Start output buffering, VERY IMPORTANT

        $type = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = Auth::id();

        // 1. Sales Data (Mauzo)
        $salesData = Mauzo::query()
           ->selectRaw('tarehe, SUM(total_price) as total_sales') //  use total_price
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('tarehe', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('tarehe', '<=', $endDate);
            })
            ->groupBy('tarehe')
            ->get()
            ->keyBy('tarehe');

        // 2. Expenses Data
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

        // 3. Alizeti Data
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

        // 4. Prepare Report Data
        $reportData = [];
        $dates = $this->getUniqueDates($salesData->toArray(), $expensesData->toArray(), $alizetiData->toArray());  // Pass arrays

        // 5. Calculate totals and populate report data
        $totalSales = 0;
        $totalExpenses = 0;
        $totalAlizetiCost = 0;

        foreach ($dates as $date) {
            $sale = $salesData[$date] ?? (object)['total_sales' => 0];
            $expense = $expensesData[$date] ?? (object)['total_expenses' => 0];
            $alizeti = $alizetiData[$date] ?? (object)['total_alizeti_cost' => 0, 'total_gunia' => 0, 'total_kilogram' => 0];

            $dailySales = $sale->total_sales ?? 0;
            $dailyExpenses = $expense->total_expenses ?? 0;
            $dailyAlizetiCost = $alizeti->total_alizeti_cost ?? 0;
            $dailyGunia = $alizeti->total_gunia ?? 0;
            $dailyKilogram = $alizeti->total_kilogram ?? 0;

            $reportData[$date] = [
                'date' => $date,
                'total_sales' => $dailySales,
                'total_expenses' => $dailyExpenses,
                'total_alizeti_cost' => $dailyAlizetiCost,
                'total_gunia' => $dailyGunia,
                'total_kilogram' => $dailyKilogram,
            ];

            $totalSales += $dailySales;
            $totalExpenses += $dailyExpenses;
            $totalAlizetiCost += $dailyAlizetiCost;
        }

        // 6. Calculate overall profit/loss
        $profitLoss = $totalSales - $totalExpenses - $totalAlizetiCost;

        $filename = 'profit_loss_report_' . now()->format('YmdHis');

        switch ($type) {
            case 'excel':
                // In-Controller Excel handling
                try {
                    $response = Excel::download(function () use ($reportData, $profitLoss) {
                        return view('reports.profit-loss-excel', ['reportData' => $reportData, 'profitLoss' => $profitLoss]); // Pass combined data
                    }, $filename . '.xlsx');
                    ob_end_clean();
                    return $response;
                } catch (\Exception $e) {
                    Log::error('Excel generation failed: ' . $e->getMessage());
                    ob_end_clean();
                    return response()->json(['error' => 'Failed to generate Excel file: ' . $e->getMessage()], 500); // Return error
                }
                break;
            case 'pdf':
                try {
                    $pdf = Pdf::loadView('reports.profit-loss-pdf', ['reportData' => $reportData, 'profitLoss' => $profitLoss]); // Pass combined data
                    $pdfContent = $pdf->output(); // Get PDF content as string
                    if ($pdfContent === false) {
                        throw new \Exception('Failed to generate PDF content.');
                    }
                    Storage::disk('local')->put($filename . '.pdf', $pdfContent); //save
                    $response = response()->file(storage_path('app/' . $filename . '.pdf'), [
                        'Content-Type' => 'application/pdf',
                    ]);
                    ob_end_clean();
                    return $response;

                } catch (\Exception $e) {
                    Log::error('PDF generation failed: ' . $e->getMessage());
                    ob_end_clean();
                    return response()->json(['error' => 'Failed to generate PDF file: ' . $e->getMessage()], 500); // Return error
                }
                break;
            case 'csv':
            default:
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
                ];
                return $this->generateCsvResponse($reportData, $headers, $profitLoss);
        }
    }

   private function generateCsvResponse($reportData, $headers, $profitLoss)
    {
        $callback = function () use ($reportData, $headers, $profitLoss): StreamedResponse {
            $file = fopen('php://output', 'w');
            if ($file === false) {
                Log::error('Failed to open php://output for CSV export.');
                return new StreamedResponse(function () {
                    echo "Error generating CSV: Could not open output stream.";
                }, 500, ['Content-Type' => 'text/plain']);
            }

            // Add headers
            fputcsv($file, ['Date', 'Total Sales (TZS)', 'Total Expenses (TZS)', 'Total Alizeti Cost (TZS)', 'Total Gunia', 'Total Kilograms', 'Profit/Loss']);

            $totalSales = 0;
            $totalExpenses = 0;
            $totalAlizetiCost = 0;
            $totalGunia = 0;
            $totalKilograms = 0;
            $overallProfitLoss = 0;


            foreach ($reportData as $data) {
                $dailyProfitLoss = $data['total_sales']  - $data['total_expenses'] - $data['total_alizeti_cost'];
                fputcsv($file, [
                    $data['date'],
                    $data['total_sales'],
                    $data['total_expenses'],
                    $data['total_alizeti_cost'],
                    $data['total_gunia'],
                    $data['total_kilogram'],
                    $dailyProfitLoss,
                ]);
                $totalSales += $data['total_sales'];
                $totalExpenses += $data['total_expenses'];
                $totalAlizetiCost += $data['total_alizeti_cost'];
                $totalGunia += $data['total_gunia'];
                $totalKilograms += $data['total_kilogram'];
                $overallProfitLoss += $dailyProfitLoss;
            }

            //add totals
            fputcsv($file, [
                'Total',
                $totalSales,
                $totalExpenses,
                $totalAlizetiCost,
                $totalGunia,
                $totalKilograms,
                $overallProfitLoss
            ]);

            fclose($file);

            $response = new StreamedResponse(
                function () use ($file) {
                    fpassthru($file);
                },
                200,
                $headers
            );
            ob_end_clean();
            return $response;
        };
        return $callback();
    }

    /**
     * Helper function to get unique dates from multiple datasets.
     *
     * @param  array  $salesData
     * @param  array  $expensesData
     * @param  array  $alizetiData
     * @return array
     */
    private function getUniqueDates(array $salesData, array $expensesData, array $alizetiData): array
    {
        $dates = array_unique(array_merge(
            array_keys($salesData),
            array_keys($expensesData),
            array_keys($alizetiData)
        ));
        sort($dates); // Ensure dates are in order
        return $dates;
    }
}

