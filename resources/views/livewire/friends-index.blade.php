<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Friends Management</h3>
                </div>
                <div class="card-body">
                    <!-- تب‌ها -->
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach ($tabs as $tabKey => $tab)
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === $tabKey ? 'active' : '' }}"
                                   wire:click="setTab('{{ $tabKey }}')"
                                   href="{{ route('friends.' . ($tabKey === 'my-friends' ? 'my-friends' : $tabKey)) }}"
                                   role="tab">{{ $tab['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- محتوای تب‌ها -->
                    <div class="tab-content mt-3">
                        @foreach ($tabs as $tabKey => $tab)
                            <div class="tab-pane {{ $activeTab === $tabKey ? 'active' : '' }}" role="tabpanel">
                                @if ($tab['users']->isEmpty())
                                    <p class="text-muted">No {{ strtolower($tab['label']) }} found.</p>
                                @else
                                    <div class="row">
                                        @foreach ($tab['users'] as $user)
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                @livewire('user-card', ['user' => $user], key($user->id))
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
