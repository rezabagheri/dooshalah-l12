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
                        <ul class="list-group mb-3">
                            @foreach ($plan->prices as $price)
                                <li class="list-group-item">
                                    {{ ucfirst(str_replace('_', ' ', $price->duration)) }}: ${{ $price->price }}
                                </li>
                            @endforeach
                        </ul>
                        <button class="btn btn-primary">Upgrade to {{ $plan->name }}</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
