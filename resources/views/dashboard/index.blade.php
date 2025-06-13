@extends('layouts.appw')

@section('content')
<div class="container mt-4">
    <h4 class="text-center mb-4 text-primary fw-bold">Oil Production Dashboard</h4>

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

    ---
    <!-- Overall Stock Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-secondary"><i class="fas fa-warehouse me-2"></i>Current Stock Overview</h5>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-0">Clean Alizeti (Kg)</h6>
                            <h3 class="display-5 fw-bold">{{ number_format($totalCleanAlizetiKg, 2) }}</h3>
                        </div>
                        <i class="fas fa-sun fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-0">Mafuta Machafu (Lts)</h6>
                            <h3 class="display-5 fw-bold">{{ number_format($totalMafutaMachafuStock, 2) }}</h3>
                        </div>
                        <i class="fas fa-oil-can fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-0">Mafuta Masafi (Lts)</h6>
                            <h3 class="display-5 fw-bold">{{ number_format($totalMafutaMasafiStock, 2) }}</h3>
                        </div>
                        <i class="fas fa-flask fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-danger shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-0">Mashudu (Kg)</h6>
                            <h3 class="display-5 fw-bold">{{ number_format($totalMashuduStock, 2) }}</h3>
                        </div>
                        <i class="fas fa-box fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-dark shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-0">Ugido (Lts)</h6>
                            <h3 class="display-5 fw-bold">{{ number_format($totalUgidoStock, 2) }}</h3>
                        </div>
                        <i class="fas fa-bong fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-secondary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-0">Lami (Lts)</h6>
                            <h3 class="display-5 fw-bold">{{ number_format($totalLamiStock, 2) }}</h3>
                        </div>
                        <i class="fas fa-flask-poison fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    ---
    <!-- Process Totals and Electricity Units -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-secondary"><i class="fas fa-cogs me-2"></i>Production Process Totals</h5>
        </div>

        <!-- Uzalishaji (Pressing) Totals -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"> {{-- Removed bg-primary text-white --}}
                    <h5 class="mb-0">Uzalishaji (Pressing) Totals</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Alizeti Processed (Kg):
                            <span class="badge bg-primary rounded-pill p-2">{{ number_format($totalUzalishajiAlizetiKgm, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Mafuta Machafu Produced (Lts):
                            <span class="badge bg-primary rounded-pill p-2">{{ number_format($totalUzalishajiMafutaMachafu, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Mashudu Produced (Kg):
                            <span class="badge bg-primary rounded-pill p-2">{{ number_format($totalUzalishajiMashudu, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Electricity Used (Units):
                            <span class="badge bg-info rounded-pill p-2">{{ number_format($totalUzalishajiUnitsUsed, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Uchujaji (Filtration) Totals -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"> {{-- Removed bg-success text-white --}}
                    <h5 class="mb-0">Uchujaji (Filtration) Totals</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Mafuta Masafi Produced (Lts):
                            <span class="badge bg-primary rounded-pill p-2">{{ number_format($totalUchujajiMafutaMasafi, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Ugido Produced (Lts):
                            <span class="badge bg-primary rounded-pill p-2">{{ number_format($totalUchujajiUgido, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Lami Produced (Lts):
                            <span class="badge bg-primary rounded-pill p-2">{{ number_format($totalUchujajiLami, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Electricity Used (Units):
                            <span class="badge bg-info rounded-pill p-2">{{ number_format($totalUchujajiUnitsUsed, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    ---
    <!-- Latest Prices -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-secondary"><i class="fas fa-tags me-2"></i>Latest Prices</h5>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Price of Lami:</h6>
                    </div>
                    <h4 class="fw-bold text-success">{{ number_format($priceOfLami, 2) }} TZS</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Price of Ugido:</h6>
                    </div>
                    <h4 class="fw-bold text-success">{{ number_format($priceOfUgido, 2) }} TZS</h4>
                </div>
            </div>
        </div>
    </div>

    ---
    <!-- Recent Sales -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-secondary"><i class="fas fa-chart-line me-2"></i>Recent Sales Activities</h5>
        </div>
        
        <!-- Recent Mafuta Sales -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"> {{-- Removed bg-info text-white --}}
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

        <!-- Recent Mashudu Sales -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"> {{-- Removed bg-danger text-white --}}
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