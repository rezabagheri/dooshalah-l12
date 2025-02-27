<li class="nav-item dropdown">
    <a class="nav-link" data-bs-toggle="dropdown" href="#">
        <i class="bi bi-chat-text"></i>
        <span class="navbar-badge badge text-bg-danger">{{ $messageCount }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
        @foreach ($messages as $message)
            <a href="#" class="dropdown-item">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <img src="{{ $message['avatar'] }}" alt="User Avatar" class="img-size-50 rounded-circle me-3">
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="dropdown-item-title">
                            {{ $message['name'] }}
                            <span class="float-end fs-7 {{ $message['star_class'] }}">
                                <i class="bi bi-star-fill"></i>
                            </span>
                        </h3>
                        <p class="fs-7">{{ $message['message'] }}</p>
                        <p class="fs-7 text-secondary">
                            <i class="bi bi-clock-fill me-1"></i> {{ $message['time'] }}
                        </p>
                    </div>
                </div>
            </a>
            <div class="dropdown-divider"></div>
        @endforeach
        <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
    </div>
</li>
