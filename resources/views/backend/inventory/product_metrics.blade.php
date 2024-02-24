@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Product Metrics') }}</h1>
            </div>
        </div>
    </div>
    {{-- <h1>{{ count($productMetrics['orderContributionRatio']) }}%</h1> --}}
    <div class="card">
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>{{ translate('Product') }}</th>
                        <th>{{ translate('Order Contribution Ratio') }}</th>
                        <th>{{ translate('Sales Amount Contribution Ratio') }}</th>
                        <th>{{ translate('Current Stock') }}</th>
                        <th>{{ translate('Restock Suggestion Quantity') }}</th>
                        <th>{{ translate('Demand') }}</th> <!-- New row for comments -->
                    </tr>
                </thead>
                @php
                    function getColor($ratio)
                    {
                        if ($ratio >= 7) {
                            return 'success';
                        } elseif ($ratio >= 3) {
                            return 'warning';
                        } elseif ($ratio >= 0) {
                            return 'secondary';
                        } else {
                            return 'dark';
                        }
                    }
                @endphp

                <tbody class="">
                    @foreach ($productMetrics as $metrics)
                        <tr class=" text-dark ">
                            <td>{{ $metrics['productName'] }}</td>
                            <td>
                                <span
                                    class="mb-2 badge badge-inline badge-{{ getColor($metrics['orderContributionRatio']) }}">{{ number_format($metrics['orderContributionRatio'], 2) }}%</span>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ getColor($metrics['orderContributionRatio']) }}"
                                        role="progressbar" style="width: {{ $metrics['orderContributionRatio'] }}%;"
                                        aria-valuenow="{{ $metrics['orderContributionRatio'] }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="mb-2 badge badge-inline badge-{{ getColor($metrics['salesAmountContributionRatio']) }}">{{ number_format($metrics['salesAmountContributionRatio'], 2) }}%</span>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ getColor($metrics['salesAmountContributionRatio']) }}"
                                        role="progressbar" style="width: {{ $metrics['salesAmountContributionRatio'] }}%;"
                                        aria-valuenow="{{ $metrics['salesAmountContributionRatio'] }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <h4
                                    class="badge badge-inline  {{ $metrics['current_stock'] <= 0 ? 'badge-dark' : 'badge-primary' }}">
                                    {{ $metrics['current_stock'] }}
                                </h4>
                            </td>
                            <td>
                                <span
                                    class="badge badge-inline badge-{{ getColor($metrics['restockSuggestionQuantity']) }}">

                                    {{ number_format($metrics['restockSuggestionQuantity'], 0) }}

                                </span>
                            </td>
                            <!-- New cell for comments -->
                            <td>
                                <span class="badge badge-inline badge-{{ getColor($metrics['orderContributionRatio']) }}">
                                    @if ($metrics['orderContributionRatio'] >= 7)
                                        High Demand
                                    @elseif ($metrics['orderContributionRatio'] < 7 && $metrics['orderContributionRatio'] >= 3)
                                        Moderate Demand
                                    @elseif ($metrics['orderContributionRatio'] < 3 && $metrics['orderContributionRatio'] > 0)
                                        Low Demand
                                    @else
                                        No Demand
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection
