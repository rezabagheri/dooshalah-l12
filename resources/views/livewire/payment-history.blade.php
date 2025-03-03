<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Payment History</h2>
        </div>
        <div class="card-body">
            @if ($payments->isEmpty())
                <p class="text-muted">You have no payment history yet.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('Y-m-d H:i') }}</td>
                                <td>{{ $payment->subscription->plan->name }}</td>
                                <td>${{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $payment->payment_status === 'paid' ? 'bg-success' : ($payment->payment_status === 'failed' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#paymentDetailsModal" wire:click="showDetails({{ $payment->id }})">
                                        <i class="bi bi-eye"></i> Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- مودال جزئیات -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentDetailsModalLabel">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($selectedPayment)
                        @if ($selectedPayment->payment_status === 'paid')
                            <div class="alert alert-success">
                                <h4>Payment Successful</h4>
                            </div>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Plan</th>
                                    <td>{{ $selectedPayment->subscription->plan->name }}</td>
                                </tr>
                                <tr>
                                    <th>Duration</th>
                                    <td>{{ ucfirst(str_replace('_', ' ', $selectedPayment->subscription->planPrice->duration)) }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td>${{ number_format($selectedPayment->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Transaction ID</th>
                                    <td>{{ $selectedPayment->transaction_id }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Date</th>
                                    <td>{{ $selectedPayment->payment_date->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        @else
                            <div class="alert alert-danger">
                                <h4>Payment {{ ucfirst($selectedPayment->payment_status) }}</h4>
                                <p><strong>Reason:</strong> {{ json_decode($selectedPayment->gateway_response)->error->message ?? 'Canceled by user or unknown error' }}</p>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
