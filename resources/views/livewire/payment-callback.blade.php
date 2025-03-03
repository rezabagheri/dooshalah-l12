<div class="container mt-4 invoice-page">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Payment Invoice</h2>
            <div class="card-tools">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Invoice
                </button>
            </div>
        </div>
        <div class="card-body">
            @if ($paymentDetails['status'] === 'success')
                <div class="row mb-4">
                    <div class="col-12 col-md-6">
                        <h4>From:</h4>
                        <p>
                            Your App Name<br>
                            123 App Street<br>
                            City, State, ZIP<br>
                            Email: support@yourapp.com
                        </p>
                    </div>
                    <div class="col-12 col-md-6 text-md-end">
                        <h4>To:</h4>
                        <p>
                            {{ auth()->user()->display_name }}<br>
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h3 class="text-success">Payment Successful</h3>
                        <p class="text-muted">{{ $paymentDetails['message'] }}</p>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Plan</th>
                                    <td>{{ $paymentDetails['plan'] }}</td>
                                </tr>
                                <tr>
                                    <th>Duration</th>
                                    <td>{{ ucfirst(str_replace('_', ' ', $paymentDetails['duration'])) }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td>${{ number_format($paymentDetails['amount'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Transaction ID</th>
                                    <td>{{ $paymentDetails['transaction_id'] }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Date</th>
                                    <td>{{ $paymentDetails['payment_date'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="{{ route('friends.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Return to Friends Page
                        </a>
                    </div>
                </div>
            @else
                <div class="alert alert-danger">
                    <h4>{{ $paymentDetails['message'] }}</h4>
                    <p>Something went wrong with your payment. Please try again or contact support.</p>
                    <div class="text-center mt-3">
                        <a href="{{ route('plans.upgrade') }}" class="btn btn-primary">Try Again</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media print {
            .btn, .card-tools {
                display: none;
            }
            .invoice-page {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</div>
