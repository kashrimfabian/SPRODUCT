@extends('layouts.appw')

@section('content')
<div class="container mt-4">
    
    <div class="row justify-content-center">
        <h4 class="text-center text-dark text-uppercase mb-4">
        Oil Production & sales Dashboard
        </h4>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (isset($error))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="text-muted mb-0">HOME / Dashboard</h5>
        </div>
    </div>

    <hr class="my-4">

    <!-- Overall Stock Summary Table -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-secondary"><i class="fas fa-warehouse me-2"></i>Current Stock Overview</h5>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Clean Alizeti</td>
                                    <td>{{ number_format($totalCleanAlizetiKg, 2) }} Kg</td>
                                </tr>
                                <tr>
                                    <td>Crude Oil</td>
                                    <td>{{ number_format($totalMafutaMachafuStock, 2) }} Lts</td>
                                </tr>
                                <tr>
                                    <td>Refined Oil</td>
                                    <td>{{ number_format($totalMafutaMasafiStock, 2) }} Lts</td>
                                </tr>
                                <tr>
                                    <td>Mashudu</td>
                                    <td>{{ number_format($totalMashuduStock, 2) }} Kg</td>
                                </tr>
                                <tr>
                                    <td>Ugido</td>
                                    <td>{{ number_format($totalUgidoStock, 2) }} Lts</td>
                                </tr>
                                <tr>
                                    <td>Lami</td>
                                    <td>{{ number_format($totalLamiStock, 2) }} Lts</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <!-- Production Process Totals Tables -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-secondary"><i class="fas fa-cogs me-2"></i>Production Process Totals</h5>
        </div>

        <!-- Uzalishaji (Pressing) Totals Table -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Uzalishaji (Pressing) Totals</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <td>Total Alizeti Processed (Kg)</td>
                                    <td>{{ number_format($totalUzalishajiAlizetiKgm, 2) }} Kg</td>
                                </tr>
                                <tr>
                                    <td>Total Mafuta Machafu Produced (Lts)</td>
                                    <td>{{ number_format($totalUzalishajiMafutaMachafu, 2) }} Lts</td>
                                </tr>
                                <tr>
                                    <td>Total Mashudu Produced (Kg)</td>
                                    <td>{{ number_format($totalUzalishajiMashudu, 2) }} Kg</td>
                                </tr>
                                <tr>
                                    <td>Electricity Used (Units)</td>
                                    <td>{{ number_format($totalUzalishajiUnitsUsed, 2) }} Units</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Uchujaji (Filtration) Totals Table -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Uchujaji (Filtration) Totals</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <td>Total Mafuta Masafi Produced (Lts)</td>
                                    <td>{{ number_format($totalUchujajiMafutaMasafi, 2) }} Lts</td>
                                </tr>
                                <tr>
                                    <td>Total Ugido Produced (Lts)</td>
                                    <td>{{ number_format($totalUchujajiUgido, 2) }} Lts</td>
                                </tr>
                                <tr>
                                    <td>Total Lami Produced (Lts)</td>
                                    <td>{{ number_format($totalUchujajiLami, 2) }} Lts</td>
                                </tr>
                                <tr>
                                    <td>Electricity Used (Units)</td>
                                    <td>{{ number_format($totalUchujajiUnitsUsed, 2) }} Units</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <!-- Latest Prices Table -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-secondary"><i class="fas fa-tags me-2"></i>Latest Prices</h5>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price (TZS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Price of Lami</td>
                                    <td class="fw-bold text-success">{{ number_format($priceOfLami, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Price of Ugido</td>
                                    <td class="fw-bold text-success">{{ number_format($priceOfUgido, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <!-- Recent Sales Tables -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-secondary"><i class="fas fa-chart-line me-2"></i>Recent Sales Activities</h5>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Recent Mafuta Sales</h5>
                </div>
                <div class="card-body">
                    @if($recentMafutaSales->isEmpty())
                        <p class="text-muted text-center mb-0">No recent mafuta sales found.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Qty (Lts)</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMafutaSales as $sale)
                                <tr>
                                    <td>{{ $sale->sale_date }}</td>
                                    <td>{{ number_format($sale->quantity, 2) }}</td>
                                    <td>{{ number_format($sale->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Recent Mashudu Sales</h5>
                </div>
                <div class="card-body">
                    @if($recentMashuduSales->isEmpty())
                        <p class="text-muted text-center mb-0">No recent mashudu sales found.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Qty (Kg)</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMashuduSales as $sale)
                                <tr>
                                    <td>{{ $sale->sale_date }}</td>
                                    <td>{{ number_format($sale->quantity, 2) }}</td>
                                    <td>{{ number_format($sale->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection