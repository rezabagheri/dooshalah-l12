<div class="container mt-4">
    <h1 class="mb-4">Upgrade Your Plan</h1>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        @foreach ($plans as $plan)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3>{{ $plan->name }}</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ $plan->description ?? 'No description available.' }}</p>
                        <select wire:model.live="selectedDuration" class="form-select mb-3">
                            <option value="1_month">1 Month</option>
                            <option value="3_months">3 Months</option>
                            <option value="6_months">6 Months</option>
                            <option value="1_year">1 Year</option>
                        </select>
                        <p wire:live>Price: ${{ $prices[$plan->id] }}</p>
                        <button wire:click="$set('selectedPlanId', {{ $plan->id }})" class="btn btn-primary">Select {{ $plan->name }}</button>
                        @if ($selectedPlanId == $plan->id)
                            <button wire:click="checkout" class="btn btn-success mt-2">Proceed to PayPal</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('payment-success', (message) => {
        alert(message);
    });
    Livewire.on('error', (message) => {
        alert(message);
    });
});
</script>
