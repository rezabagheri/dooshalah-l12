@php
use App\Enums\UserStatus;
use App\Enums\UserRole;
@endphp

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">User Management</h3>
    </div>
    <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center mb-3 p-3">
            <h2 class="mb-0">Users List</h2>
            <div class="input-group" style="width: 300px;">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search users...">
                <button class="btn btn-primary" wire:click="$refresh">Search</button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success mx-3">{{ session('message') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger mx-3">{{ session('error') }}</div>
        @endif

        <table class="table table-striped table-hover align-middle">
            <thead class="bg-secondary text-white">
                <tr>
                    <th></th>
                    <th wire:click="sort('first_name')">First Name {{ $sortBy === 'first_name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</th>
                    <th wire:click="sort('last_name')">Last Name {{ $sortBy === 'last_name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</th>
                    <th wire:click="sort('display_name')">Display Name {{ $sortBy === 'display_name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</th>
                    <th wire:click="sort('gender')">Gender {{ $sortBy === 'gender' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</th>
                    <th wire:click="sort('email')">Email {{ $sortBy === 'email' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</th>
                    <th wire:click="sort('phone_number')">Phone Number {{ $sortBy === 'phone_number' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</th>
                    <th wire:click="sort('status')">Status {{ $sortBy === 'status' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</th>
                    <th wire:click="sort('role')">Role {{ $sortBy === 'role' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</th>
                    <th>Plan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <img src="{{ $user->profilePicture()?->media->path ? asset('storage/' . $user->profilePicture()->media->path) : '/dist/assets/img/user2-160x160.jpg' }}"
                                 alt="{{ $user->display_name }}'s Profile Image" style="width: 40px; height: 40px;">
                        </td>
                        <td>{{ $user->first_name }}</td>
                        <td>{{ $user->last_name }}</td>
                        <td>{{ $user->display_name }}</td>
                        <td>{{ $user->gender }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_number }}</td>
                        <td>
                            @switch($user->status)
                                @case(UserStatus::Active)
                                    <span class="badge text-bg-success">{{ $user->status->label() }}</span>
                                    @break
                                @case(UserStatus::Pending)
                                    <span class="badge text-bg-warning">{{ $user->status->label() }}</span>
                                    @break
                                @case(UserStatus::Suspended)
                                    <span class="badge text-bg-danger">{{ $user->status->label() }}</span>
                                    @break
                                @case(UserStatus::Blocked)
                                    <span class="badge text-bg-secondary">{{ $user->status->label() }}</span>
                                    @break
                                @default
                                    <span class="badge text-bg-info">{{ $user->status->value }}</span>
                            @endswitch
                        </td>
                        <td>
                            @switch($user->role)
                                @case(UserRole::Normal)
                                    <span class="badge text-bg-secondary">{{ $user->role->label() }}</span>
                                    @break
                                @case(UserRole::Admin)
                                    <span class="badge text-bg-primary">{{ $user->role->label() }}</span>
                                    @break
                                @case(UserRole::SuperAdmin)
                                    <span class="badge text-bg-dark">{{ $user->role->label() }}</span>
                                    @break
                                @default
                                    <span class="badge text-bg-info">{{ $user->role->value }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if ($user->activePlan())
                                @switch($user->activePlan()->name)
                                    @case('Plan A')
                                        <span class="badge text-bg-primary">{{ $user->activePlan()->name }}</span>
                                        @break
                                    @case('Plan B')
                                        <span class="badge text-bg-info">{{ $user->activePlan()->name }}</span>
                                        @break
                                    @case('Plan C')
                                        <span class="badge text-bg-success">{{ $user->activePlan()->name }}</span>
                                        @break
                                    @default
                                        <span class="badge text-bg-secondary">{{ $user->activePlan()->name }}</span>
                                @endswitch
                            @else
                                <span class="badge text-bg-warning">No Active Subscription</span>
                            @endif
                        </td>
                        <td>
                            <!-- دسکتاپ: آیکون‌ها -->
                            <div class="d-none d-md-inline">
                                <a href="{{ route('admin.user.show', $user->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button wire:click="openResetPasswordModal({{ $user->id }})" class="btn btn-sm btn-warning" title="Reset Password">
                                    <i class="bi bi-key"></i>
                                </button>
                                <a href="https://www.google.com/search?q={{ urlencode($user->first_name . ' ' . $user->last_name) }}" class="btn btn-sm btn-secondary" target="_blank" title="Search Google">
                                    <i class="bi bi-search"></i>
                                </a>
                                <button wire:click="sendResetPasswordLink({{ $user->id }})" class="btn btn-sm btn-primary" title="Send Reset Password Link">
                                    <i class="bi bi-envelope"></i>
                                </button>
                            </div>

                            <!-- موبایل: دراپ‌داون -->
                            <div class="d-md-none">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-dark dropdown-toggle" type="button" id="dropdownMenuButton{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $user->id }}">
                                        <li><a href="{{ route('admin.user.show', $user->id) }}" class="dropdown-item"><i class="bi bi-eye me-1"></i> View</a></li>
                                        <li><button wire:click="openResetPasswordModal({{ $user->id }})" class="dropdown-item"><i class="bi bi-key me-1"></i> Reset Password</button></li>
                                        <li><a href="https://www.google.com/search?q={{ urlencode($user->first_name . ' ' . $user->last_name) }}" target="_blank" class="dropdown-item"><i class="bi bi-search me-1"></i> Search Google</a></li>
                                        <li><button wire:click="sendResetPasswordLink({{ $user->id }})" class="dropdown-item"><i class="bi bi-envelope me-1"></i> Send Reset Password Link</button></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>

    <!-- مودال ریست پسورد -->
    <div wire:ignore.self class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="resetPassword">
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" wire:model="newPassword">
                            @error('newPassword') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" wire:model="newPassword_confirmation">
                            @error('newPassword_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
