<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #333;
            margin-bottom: 5px;
        }
        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-success {
            color: green;
        }
        .text-danger {
            color: red;
        }
        .summary-box {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Profit & Loss Report</h1>

    <p class="text-center">
        <strong>Report Period:</strong>
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

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Sales (TZS)</th>
                <th>Total Expenses (TZS)</th>
                <th>Total Alizeti Cost (TZS)</th>
                <th>Total Filtering Revenue (TZS)</th> {{-- Matched with view and controller --}}
                <th>Profit/Loss (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $data)
                {{-- Ensure daily_profit_loss is directly available or calculated, consistent with controller --}}
                @php
                    // This calculation is safer if 'daily_profit_loss' isn't explicitly passed in each $data array element
                    $dailyProfitLoss = ($data['total_sales'] ?? 0) - ($data['total_expenses'] ?? 0) - ($data['total_alizeti_cost'] ?? 0) + ($data['total_filtering_revenue'] ?? 0);
                @endphp
            <tr>
                <td>{{ $data['date'] }}</td>
                <td class="text-right">{{ number_format($data['total_sales'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($data['total_expenses'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($data['total_alizeti_cost'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($data['total_filtering_revenue'] ?? 0, 2) }}</td>
                <td class="text-right {{ $dailyProfitLoss >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($dailyProfitLoss, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right">Total</th>
                <th class="text-right">{{ number_format($totalSales ?? 0, 2) }}</th>
                <th class="text-right">{{ number_format($totalExpenses ?? 0, 2) }}</th>
                <th class="text-right">{{ number_format($totalAlizetiCost ?? 0, 2) }}</th>
                <th class="text-right">{{ number_format($totalFilteringRevenue ?? 0, 2) }}</th>
                <th class="text-right {{ ($profitLoss ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($profitLoss ?? 0, 2) }}
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="summary-box">
        <h4>Overall Profit & Loss: 
            <span class="{{ ($profitLoss ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($profitLoss ?? 0, 2) }} TZS
            </span>
        </h4>
    </div>
</body>
</html>