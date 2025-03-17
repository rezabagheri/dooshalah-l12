<div class="container-fluid">
    <div class="row">
        <!-- ستون اول: عکس پروفایل و مشخصات -->
        <div class="col-md-3 mb-4">
            <div class="card card-primary card-outline">
                <img src="{{ $user->profilePicture()?->media->path ? asset('storage/' . $user->profilePicture()->media->path) : '/dist/assets/img/user2-160x160.jpg' }}"
                     alt="{{ $user->display_name }}'s Profile Image" class="img-fluid card-img-top mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $user->display_name }}</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>First Name:</strong> {{ $user->first_name }}
                    </li>
                    <li class="list-group-item">
                        <strong>Middle Name:</strong> {{ $user->middle_name ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Last Name:</strong> {{ $user->last_name }}
                    </li>
                    <li class="list-group-item">
                        <strong>Gender:</strong> {{ $user->gender ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Father Name:</strong> {{ $user->father_name ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Mother Name:</strong> {{ $user->mother_name ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Born Country:</strong> {{ $user->born_country ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Living Country:</strong> {{ $user->living_country ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong> {{ $user->email }}
                    </li>
                    <li class="list-group-item">
                        <strong>Phone Number:</strong> {{ $user->phone_number }}
                    </li>
                    <li class="list-group-item">
                        <strong>Birth Date:</strong> {{ $user->birth_date->format('Y-m-d') }}
                    </li>
                    <li class="list-group-item">
                        <strong>Role:</strong> {{ $user->role?->label() ?? $user->role ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Status:</strong> {{ $user->role?->label() ?? $user->status ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Plan:</strong> {{ $user->latestSubscription()?->plan->name ?? 'No Active Subscription' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Plan Start Date:</strong> {{ $user->latestSubscription()?->start_date?->format('Y-m-d') ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Plan End Date:</strong> {{ $user->latestSubscription()?->end_date?->format('Y-m-d') ?? 'N/A' }}
                    </li>
                </ul>
                <div class="card-footer">
                    <a href="{{ route('admin.user') }}" class="btn btn-secondary btn-sm w-100">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>

        <!-- ستون دوم: کارت‌های جدا شده -->
        <div class="col-md-9">
            @include('livewire.partials.user-profile-pictures')
            @include('livewire.partials.user-interests')
        </div>
    </div>
</div>
