<div class="container-fluid mt-4">
    <div class="row">
        <!-- سایدبار دوم -->
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mailbox</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="{{ route('messages.compose') }}" class="nav-link {{ request()->routeIs('messages.compose') ? 'active' : '' }}">
                                <i class="bi bi-plus-square me-2"></i> Compose
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('messages.inbox') }}" class="nav-link {{ request()->routeIs('messages.inbox') && $viewMode == 'inbox' ? 'active' : '' }}">
                                <i class="bi bi-inbox me-2"></i> Inbox
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('messages.inbox', ['mode' => 'sent']) }}" class="nav-link {{ $viewMode == 'sent' ? 'active' : '' }}">
                                <i class="bi bi-send me-2"></i> Sent
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('messages.inbox', ['mode' => 'draft']) }}" class="nav-link {{ $viewMode == 'draft' ? 'active' : '' }}">
                                <i class="bi bi-pencil me-2"></i> Draft
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- محتوای اصلی -->
        <div class="col-md-10">
            {{ $slot }}
        </div>
    </div>

    <style>
        .nav-link {
            border-radius: 0;
        }
        .nav-link.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</div>
