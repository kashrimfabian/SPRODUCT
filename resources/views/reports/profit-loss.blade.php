@extends('layouts.appw')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center text-primary">Profit & Loss Report</h1>

    <form action="{{ route('reports.profit_loss') }}" method="GET" class="row gx-2 gy-2 align-items-center mb-3">
        <div class="col-md-auto">
            <div class="input-group">
                <input type="date" class="form-control flatpickr" id="start_date" name="start_date"
                    value="{{ $startDate ?? '' }}" placeholder="Start date">
            </div>
        </div>
        <div class="col-md-auto">
            <div class="input-group">
                <input type="date" class="form-control flatpickr" id="end_date" name="end_date"
                    value="{{ $endDate ?? '' }}" placeholder="End date">
            </div>
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </div>
        <div class="col-md-auto">
             <button type="button" class="btn btn-secondary" id="reset_button">Reset</button>
        </div>
        <div class="col-md-auto">
            <div class="input-group">
                <select class="form-control" id="export_type" onchange="exportReport()">
                    <option value="">Select file format</option>
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                    <option value="pdf">PDF</option>
                </select>

            </div>
        </div>
        <div class="col-md-auto">
            <a id="export_link" href="#" class="btn btn-success" style="display: none;">Export</a>
        </div>
    </form>

    @if (isset($startDate) || isset($endDate))
    <p class="mb-3 text-center">
        <strong>Report Range:</strong>
        @if ($startDate)
        {{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}
        @endif
        @if ($startDate && $endDate)
        -
        @endif
        @if ($endDate)
        {{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}
        @endif
    </p>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Profit & Loss Summary</h6>
                </div>
                <div class="card-body">
                    @if (isset($reportData) && count($reportData) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Sales (TZS)</th>
                                    <th>Total Expenses (TZS)</th>
                                    <th>Total Alizeti Cost (TZS)</th>
                                    <th>Profit/Loss (TZS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $overallProfitLoss = 0;
                                @endphp
                                @foreach ($reportData as $data)
                                    @php
                                        // Calculate daily profit/loss: Sales - Expenses - Alizeti Cost
                                        $dailyProfitLoss = $data['total_sales'] - $data['total_expenses'] - $data['total_alizeti_cost'];
                                        $overallProfitLoss += $dailyProfitLoss;
                                    @endphp
                                <tr>
                                    <td>{{ $data['date'] }}</td>
                                    <td>{{ number_format($data['total_sales'], 2) }}</td>
                                    <td>{{ number_format($data['total_expenses'], 2) }}</td>
                                    <td>{{ number_format($data['total_alizeti_cost'], 2) }}</td>
                                    <td class="{{ $dailyProfitLoss >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($dailyProfitLoss, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th>{{ number_format($totalSales, 2) }}</th>
                                    <th>{{ number_format($totalExpenses, 2) }}</th>
                                    <th>{{ number_format($totalAlizetiCost, 2) }}</th>
                                    <th class="{{ $overallProfitLoss >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($overallProfitLoss, 2) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">No report data available for the selected period.</p>
                    @endif
                    <div class="text-center mt-4">
                        <h4 class="{{ ($profitLoss ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            Overall Profit & Loss: {{ number_format($profitLoss ?? 0, 2) }} TZS
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr(".flatpickr", {
        dateFormat: "Y-m-d"
    });

    const exportLink = document.getElementById('export_link');
    exportLink.style.display = 'none';

    const resetButton = document.getElementById('reset_button');
    resetButton.addEventListener('click', function() {
        window.location.href = "{{ route('reports.profit_loss') }}";
    });

    const presetButtons = document.querySelectorAll('.flatpickr-preset');
    presetButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('start_date').value = this.dataset.start;
            document.getElementById('end_date').value = this.dataset.end;
            document.querySelector('form').submit();
        });
    });
});

function exportReport() {
    const exportType = document.getElementById("export_type").value;
    const startDate = document.getElementById("start_date").value;
    const endDate = document.getElementById("end_date").value;
    const exportLink = document.getElementById("export_link");

    if (exportType) {
        exportLink.href = "{{ route('reports.profit_loss.export') }}?type=" + exportType + "&start_date=" + startDate +
            "&end_date=" + endDate;
        exportLink.style.display = "inline-block";
    } else {
        exportLink.style.display = "none";
    }
}
</script>
