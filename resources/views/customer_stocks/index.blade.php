@extends('layouts.appw') 

@section('content')
<div class="container">
    <h4 class="text-center mb-4">Customer Material Stock Overview</h4>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Phone No.</th>
                    <th>Alizeti Batch No.</th>
                    <th>Uncleaned (kg)</th>
                    <th>Cleaned (kg)</th>
                    <th>Crude Oil (Ltr)</th>
                    <th>Refined Oil (Ltr)</th>
                    <th>Mashudu (kg)</th>
                    <th>Ugido (kg)</th>
                    <th>Lami (kg)</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customerStocks as $stock)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $stock->custAlizeti->customer->first_name ?? 'N/A' }} {{ $stock->custAlizeti->customer->last_name ?? '' }}</td>
                    <td>{{ $stock->custAlizeti->customer->phone_number ?? 'N/A' }}</td>
                    <td>{{ $stock->custAlizeti->batch_no ?? 'N/A' }}</td>
                    <td>{{ number_format($stock->uncleaned_kg, 2) }}</td>
                    <td>{{ number_format($stock->cleaned_kg, 2) }}</td>
                    <td>{{ number_format($stock->crude_oil, 2) }}</td>
                    <td>{{ number_format($stock->refined_oil, 2) }}</td>
                    <td>{{ number_format($stock->mashudu_kg, 2) }}</td>
                    <td>{{ number_format($stock->ugido_kg, 2) }}</td>
                    <td>{{ number_format($stock->lami_kg, 2) }}</td>
                    <td>{{ $stock->updated_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('customer_stocks.show', $stock->stock_id) }}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center">No customer material stock records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $customerStocks->links() }}
    </div>
</div>
@endsection