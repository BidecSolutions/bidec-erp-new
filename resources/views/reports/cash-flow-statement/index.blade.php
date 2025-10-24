@extends('layouts.layouts')

@section('title', 'Cash Flow Statement')

@section('content')
    <div class="container">
        <h4>Cash Flow Statement</h4>
        <form method="GET" action="{{ route('reports.cash-flow-statement') }}">
            <div class="row">
                <div class="col-md-4">
                    <label for="from_date">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="to_date">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary form-control">Filter</button>
                </div>
            </div>
        </form>

        <hr>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th colspan="2">Cash Flow from Operating Activities</th>
                </tr>
            </thead>
            <tr>
                <td>Inflows</td>
                <td class="text-end">{{ number_format($operatingInflows, 3) }}</td>
            </tr>
            <tr>
                <td>Outflows</td>
                <td class="text-end">({{ number_format($operatingOutflows, 3) }})</td>
            </tr>
            <tr>
                <th>Net Operating Cash Flow</th>
                <th class="text-end">{{ number_format($operatingInflows - $operatingOutflows, 3) }}</th>
            </tr>

            <thead>
                <tr>
                    <th colspan="2">Cash Flow from Investing Activities</th>
                </tr>
            </thead>
            <tr>
                <td>Inflows</td>
                <td class="text-end">{{ number_format($investingInflows, 3) }}</td>
            </tr>
            <tr>
                <td>Outflows</td>
                <td class="text-end">({{ number_format($investingOutflows, 3) }})</td>
            </tr>
            <tr>
                <th>Net Investing Cash Flow</th>
                <th class="text-end">{{ number_format($investingInflows - $investingOutflows, 3) }}</th>
            </tr>

            <thead>
                <tr>
                    <th colspan="2">Cash Flow from Financing Activities</th>
                </tr>
            </thead>
            <tr>
                <td>Inflows</td>
                <td class="text-end">{{ number_format($financingInflows, 3) }}</td>
            </tr>
            <tr>
                <td>Outflows</td>
                <td class="text-end">({{ number_format($financingOutflows, 3) }})</td>
            </tr>
            <tr>
                <th>Net Financing Cash Flow</th>
                <th class="text-end">{{ number_format($financingInflows - $financingOutflows, 3) }}</th>
            </tr>

            <thead>
                <tr>
                    <th>Total Net Cash Flow</th>
                    <th class="text-end">{{ number_format($netCashFlow, 3) }}</th>
                </tr>
            </thead>
        </table>

    </div>
@endsection