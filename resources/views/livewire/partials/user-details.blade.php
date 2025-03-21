<div class="col-md-3 mb-4">
    <div class="card card-primary card-outline">
        <img src="{{ $user->profilePicture()?->media->path ? asset('storage/' . $user->profilePicture()->media->path) : '/dist/assets/img/user2-160x160.jpg' }}"
             alt="{{ $user->display_name }}'s Profile Image" class="img-fluid card-img-top mb-3">
        <div class="card-body text-center">
            <h5 class="card-title">
                {{ $user->display_name }}
                <span class="ms-2" style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: {{ $user->isOnline() ? '#28a745' : '#6c757d' }};"
                      title="{{ $user->isOnline() ? 'Online' : 'Offline' }}"></span>
            </h5>
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
                <strong>Born Country:</strong>
                @if($user->bornCountry)
                    @if($user->bornCountry->flag_image)
                        <img src="{{ $user->bornCountry->flag_image }}" alt="{{ $user->bornCountry->name }}" style="width: 20px; height: 15px; margin-right: 5px;">
                    @endif
                    {{ $user->bornCountry->name }}
                @else
                    N/A
                @endif
            </li>
            <li class="list-group-item">
                <strong>Living Country:</strong>
                @if($user->livingCountry)

                    @if($user->livingCountry->flag_image)
                        <img src="{{ $user->livingCountry->flag_image }}" alt="{{ $user->livingCountry->name }}" style="width: 20px; height: 15px; margin-right: 5px;">
                    @endif
                    {{ $user->livingCountry->name }}
                @else
                    N/A
                @endif
            </li>
            <li class="list-group-item">
                <strong>Email:</strong>
                {{ $user->email }}
                <button class="btn btn-sm btn-outline-primary ms-2"
                        wire:click="$dispatch('open-contact-modal')">
                    <i class="bi bi-envelope"></i>
                </button>
            </li>
            <li class="list-group-item">
                <strong>Phone Number:</strong> {{ $user->phone_number }}
            </li>
            <li class="list-group-item">
                <strong>Birth Date:</strong> {{ $user->birth_date->format('Y-M-d') }}
            </li>
            <li class="list-group-item">
                <strong>Last Seen:</strong> {{ $user->last_seen?->format('Y-M-d H:i') ?? 'Never' }}
            </li>
            <li class="list-group-item">
                <strong>Role:</strong>
                <div class="btn-group mt-2 w-100" role="group" aria-label="User Role">
                    @foreach ($roles as $roleOption)
                        <input type="radio" class="btn-check"
                               wire:model.live="role"
                               value="{{ $roleOption->value }}"
                               id="role-{{ $roleOption->value }}"
                               autocomplete="off">
                        <label class="btn btn-outline-primary" for="role-{{ $roleOption->value }}">
                            {{ $roleOption->label() }}
                        </label>
                    @endforeach
                </div>
            </li>
            <li class="list-group-item">
                <strong>Status:</strong>
                <div class="btn-group mt-2 w-100" role="group" aria-label="User Status">
                    @foreach ($statuses as $statusOption)
                        <input type="radio" class="btn-check"
                               wire:model.live="status"
                               value="{{ $statusOption->value }}"
                               id="status-{{ $statusOption->value }}"
                               autocomplete="off">
                        <label class="btn btn-outline-primary" for="status-{{ $statusOption->value }}">
                            {{ $statusOption->label() }}
                        </label>
                    @endforeach
                </div>
            </li>
            <li class="list-group-item">
                <strong>Plan:</strong> {{ $user->activePlan()?->name ?? 'No Active Subscription' }}
            </li>
            <li class="list-group-item">
                <strong>Plan Start Date:</strong> {{ $user->latestSubscription()?->start_date?->format('Y-M-d') ?? 'N/A' }}
            </li>
            <li class="list-group-item">
                <strong>Plan End Date:</strong> {{ $user->latestSubscription()?->end_date?->format('Y-M-d') ?? 'N/A' }}
            </li>
        </ul>
        <div class="card-footer">
            <button wire:click="updateUser" class="btn btn-success btn-sm w-100 mb-2"
                    @if(!$this->isDirty()) disabled @endif>
                Save Changes
            </button>
        </div>
    </div>

    <!-- مودال ارسال پیام -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Contact {{ $user->display_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="contactTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab" aria-controls="email" aria-selected="true">Send Email</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sms-tab" data-bs-toggle="tab" data-bs-target="#sms" type="button" role="tab" aria-controls="sms" aria-selected="false" disabled>Send SMS</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="contactTabContent">
                        <div class="tab-pane fade show active" id="email" role="tabpanel" aria-labelledby="email-tab">
                            <div class="mb-3 mt-3">
                                <label for="emailSubject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="emailSubject" wire:model="emailSubject">
                                @error('emailSubject') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="emailBody" class="form-label">Body</label>
                                <textarea class="form-control" id="emailBody" rows="5" wire:model="emailBody"></textarea>
                                @error('emailBody') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="sms-tab">
                            <p>SMS functionality coming soon!</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="sendEmail">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>
