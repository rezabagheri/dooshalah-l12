<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">{{ $page_title ?? 'Dashboard' }}</h3>
                </div>
                <livewire:layouts.breadcrumb />
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            {{ $slot }}
        </div>
    </div>
</main>
