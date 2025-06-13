<?php

namespace App\Http\Controllers;

use App\Models\Mauzo;
use App\Models\Alizeti;
use App\Models\Uchujaji;
use App\Models\Uzalishaji;
use App\Models\Stock;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     * Fetches various sales and inventory data to be shown on the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // NO TRY/CATCH block for debugging initially as per your request
        
        // --- Fetch recent sales by product type ---
        $allRecentSales = Mauzo::with('product')->latest()->take(20)->get(); 

        $recentMashuduSales = $allRecentSales->filter(function ($sale) {
            return $sale->product && ($sale->product->product_name == 'Mashudu');
        })->take(5);

        $recentMafutaSales = $allRecentSales->filter(function ($sale) {
            return $sale->product && ($sale->product->product_name == 'Mafuta');
        })->take(5);
        
        // --- REFINED ALIZETI INVENTORY ---
        $currentStock = Stock::select(
                            'total_al_kgms',   // Clean Alizeti (Alizeti Safi)
                            'mafuta_machafu',
                            'mafuta_masafi',
                            'mashudu',
                            'ugido',
                            'lami'
                        )->first(); 
        
        $totalCleanAlizetiKg = $currentStock->total_al_kgms ?? 0;
        $totalMafutaMachafuStock = $currentStock->mafuta_machafu ?? 0;
        $totalMafutaMasafiStock = $currentStock->mafuta_masafi ?? 0;
        $totalMashuduStock = $currentStock->mashudu ?? 0;
        $totalUgidoStock = $currentStock->ugido ?? 0;
        $totalLamiStock = $currentStock->lami ?? 0;


        // --- Aggregated Data from Uchujaji (Sifting/Filtration) ---
        // CORRECTED: Fetch all Uchujaji records and then sum the accessor 'units_used' on the collection.
        $allUchujajiRecords = Uchujaji::all(); 
        $totalUchujajiMafutaMasafi = $allUchujajiRecords->sum('mafuta_masafi');
        $totalUchujajiUgido = $allUchujajiRecords->sum('ugido');
        $totalUchujajiLami = $allUchujajiRecords->sum('lami');
        $totalUchujajiUnitsUsed = $allUchujajiRecords->sum('units_used'); // Now sums the accessor

        // --- Aggregated Data from Uzalishaji (Production/Pressing) ---
        // CORRECTED: Fetch all Uzalishaji records and then sum the accessor 'units_used' on the collection.
        $allUzalishajiRecords = Uzalishaji::all(); 
        $totalUzalishajiAlizetiKgm = $allUzalishajiRecords->sum('alizeti_kgm');
        $totalUzalishajiMafutaMachafu = $allUzalishajiRecords->sum('mafuta_machafu');
        $totalUzalishajiMashudu = $allUzalishajiRecords->sum('mashudu');
        $totalUzalishajiUnitsUsed = $allUzalishajiRecords->sum('units_used'); // Now sums the accessor

        // --- Fetch Latest Prices for Lami and Ugido ---
        $latestPrices = Price::latest()->first(); 
        $priceOfLami = $latestPrices->price_of_lami ?? 0;
        $priceOfUgido = $latestPrices->price_of_ugido ?? 0;


        // Pass all data to the dashboard view
        return view('dashboard.index', compact(
            'recentMashuduSales',
            'recentMafutaSales',
            'totalCleanAlizetiKg',
            'totalMafutaMachafuStock',
            'totalMafutaMasafiStock',
            'totalMashuduStock',
            'totalUgidoStock',
            'totalLamiStock',
            'totalUchujajiMafutaMasafi',
            'totalUchujajiUgido',
            'totalUchujajiLami',
            'totalUchujajiUnitsUsed',
            'totalUzalishajiAlizetiKgm',
            'totalUzalishajiMafutaMachafu',
            'totalUzalishajiMashudu',
            'totalUzalishajiUnitsUsed',
            'priceOfLami',
            'priceOfUgido'
        ));
    }
}