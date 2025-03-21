<div class="card card-primary card-outline">
    <div class="card-header">
        <h5 class="card-title">Interests</h5>
    </div>
    <div class="card-body">
        @if (empty($this->getFormattedAnswers()))
            <p class="text-muted">No interests provided yet.</p>
        @else
            <ul class="list-group list-group-flush">
                @foreach ($this->getFormattedAnswers() as $answer)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $answer['label'] }}</span>
                        <span class="text-muted">{{ $answer['value'] }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
