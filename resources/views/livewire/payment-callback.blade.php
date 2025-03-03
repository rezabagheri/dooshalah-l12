<div class="container mt-4 invoice-page">
    <div class="card">
        @if ($paymentDetails['status'] === 'success')
            <!-- حالت موفقیت -->
            <div class="card-header">
                <h2 class="card-title">Payment Invoice</h2>
                <div class="card-tools">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print Invoice
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="invoice-print">
                    <div class="invoice-header mb-4">
                        <img src="/dist/assets/img/your-logo.png" alt="Your App Logo" class="img-fluid" style="max-width: 150px;">
                        <div class="mt-2">
                            <h4>Your App Name</h4>
                            <p>123 App Street, City, State, ZIP<br>Email: support@yourapp.com</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-12 col-md-6">
                            <h5>Billed To:</h5>
                            <p>
                                {{ auth()->user()->display_name }}<br>
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                        <div class="col-12 col-md-6 text-md-end">
                            <h5>Invoice Details:</h5>
                            <p>
                                Transaction ID: {{ $paymentDetails['transaction_id'] }}<br>
                                Date: {{ $paymentDetails['payment_date'] }}
                            </p>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Plan</td>
                                <td>{{ $paymentDetails['plan'] }}</td>
                            </tr>
                            <tr>
                                <td>Duration</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $paymentDetails['duration'])) }}</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>${{ number_format($paymentDetails['amount'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-success mt-4 non-printable">
                    <h4>{{ $paymentDetails['message'] }}</h4>
                    <p>Thank you for your purchase! Your invoice is ready above.</p>
                </div>

                <div class="row mt-4 non-printable">
                    <div class="col-12 text-center">
                        <a href="{{ route('friends.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Return to Friends Page
                        </a>
                    </div>
                </div>
            </div>

        @elseif ($paymentDetails['status'] === 'canceled')
            <!-- حالت لغو پرداخت -->
            <div class="card-header">
                <h2 class="card-title">Payment Canceled</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h4>{{ $paymentDetails['message'] }}</h4>
                    <p>{{ $paymentDetails['reason'] }}</p>
                    <p>It looks like you canceled your payment. Don’t worry, you can try again anytime or reach out to us if you need help!</p>
                </div>

                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="{{ route('plans.upgrade') }}" class="btn btn-primary mr-2">
                            <i class="bi bi-arrow-repeat"></i> Try Again
                        </a>
                        <a href="mailto:support@yourapp.com" class="btn btn-secondary">
                            <i class="bi bi-envelope"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>

        @else
            <!-- حالت شکست پرداخت -->
            <div class="card-header">
                <h2 class="card-title">Payment Failed</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <h4>{{ $paymentDetails['message'] }}</h4>
                    <p><strong>Reason:</strong> {{ $paymentDetails['reason'] }}</p>
                    <p>We’re sorry, something went wrong with your payment. Please try again or contact our support team for assistance.</p>
                </div>

                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="{{ route('plans.upgrade') }}" class="btn btn-primary mr-2">
                            <i class="bi bi-arrow-repeat"></i> Try Again
                        </a>
                        <a href="mailto:support@yourapp.com" class="btn btn-secondary">
                            <i class="bi bi-envelope"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .invoice-print {
            padding: 20px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media print {
            .non-printable, .card-header, .card-tools, .btn {
                display: none !important;
            }
            .invoice-page, .card, .card-body {
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
            }
            .invoice-print {
                display: block;
            }
        }
    </style>
</div>
