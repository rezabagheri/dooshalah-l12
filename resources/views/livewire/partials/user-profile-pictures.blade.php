<div class="card card-primary card-outline mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="card-title">Profile Pictures</h5>
        <div class="btn-group" role="group" aria-label="Media Filter">
            <input type="radio" class="btn-check" name="mediaFilter" id="filterAll" value="all" wire:model.live="mediaFilter" wire:click="setMediaFilter('all')" autocomplete="off" {{ $mediaFilter === 'all' ? 'checked' : '' }}>
            <label class="btn btn-outline-primary btn-sm" for="filterAll">All</label>

            <input type="radio" class="btn-check" name="mediaFilter" id="filterApproved" value="approved" wire:model.live="mediaFilter" wire:click="setMediaFilter('approved')" autocomplete="off" {{ $mediaFilter === 'approved' ? 'checked' : '' }}>
            <label class="btn btn-outline-primary btn-sm" for="filterApproved">Approved</label>

            <input type="radio" class="btn-check" name="mediaFilter" id="filterNotApproved" value="not_approved" wire:model.live="mediaFilter" wire:click="setMediaFilter('not_approved')" autocomplete="off" {{ $mediaFilter === 'not_approved' ? 'checked' : '' }}>
            <label class="btn btn-outline-primary btn-sm" for="filterNotApproved">Not Approved</label>
        </div>
    </div>
    <div class="card-body">
        @if ($this->getFilteredMedia()->isEmpty())
            <p class="text-muted">No profile pictures match the selected filter.</p>
        @else
            <div class="row">
                @foreach ($this->getFilteredMedia() as $media)
                    <div class="col-md-4 mb-3">
                        <div class="card position-relative card-hover-effect {{ !$media->is_approved ? 'border-warning card-not-approved' : '' }}">
                            <div class="position-absolute top-0 start-0 p-2 d-flex flex-column gap-1">
                                @if ($media->is_profile)
                                    <span class="badge badge-transparent bg-success">Profile</span>
                                @endif
                                @if ($media->is_approved)
                                    <span class="badge badge-transparent bg-primary">Approved</span>
                                @else
                                    <span class="badge badge-transparent bg-warning text-dark">Not Approved</span>
                                @endif
                            </div>
                            <div class="card-img-top overflow-hidden" style="max-height: 200px; cursor: pointer;" wire:click="$dispatch('open-image-modal', [{{ $media->id }}])">
                                <img src="{{ asset('storage/' . $media->media->path) }}" class="img-fluid w-100 h-100 object-fit-contain" alt="{{ $media->media->original_name }}">
                            </div>
                            <div class="card-body text-center">
                                <small class="text-muted">Uploaded: {{ $media->created_at->format('d M Y') }}</small>
                            </div>
                            <div class="card-footer d-flex justify-content-center gap-2 align-items-center">
                                @if ($media->is_approved)
                                    <button wire:click="unapproveMedia({{ $media->id }})" class="btn btn-warning btn-sm">
                                        <i class="bi bi-x-circle me-1"></i> Unapprove
                                    </button>
                                @else
                                    <button wire:click="approveMedia({{ $media->id }})" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle me-1"></i> Approve
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal با Carousel -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($user->media as $index => $media)
                            <div class="carousel-item {{ $index === $currentIndex ? 'active' : '' }}" data-media-id="{{ $media->id }}">
                                <img src="{{ asset('storage/' . $media->media->path) }}" class="d-block w-100" style="max-height: 60vh; object-fit: contain;" alt="{{ $media->media->original_name }}">
                                <div class="carousel-caption d-none d-md-block">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @if ($media->is_profile)
                                            <span class="badge badge-transparent bg-success">Profile</span>
                                        @endif
                                        @if ($media->is_approved)
                                            <span class="badge badge-transparent bg-primary">Approved</span>
                                        @else
                                            <span class="badge badge-transparent bg-warning text-dark">Not Approved</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <span wire:loading wire:target="approveMedia, unapproveMedia" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    @if ($selectedMedia)
                        @if ($selectedMedia->is_approved)
                            <button wire:click="unapproveMedia({{ $selectedMedia->id }})" class="btn btn-warning">
                                <i class="bi bi-x-circle me-1"></i> Unapprove
                            </button>
                        @else
                            <button wire:click="approveMedia({{ $selectedMedia->id }})" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Approve
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-group {
        margin-left: auto; /* انتقال به سمت راست */
    }
    .btn-outline-primary:checked + .btn-outline-primary {
        background-color: #0d6efd; /* رنگ آبی پررنگ وقتی انتخاب شده */
        color: white;
        border-color: #0d6efd;
    }
    .btn-outline-primary:hover:not(:checked) {
        background-color: #e7f1ff; /* رنگ روشن‌تر برای هاور */
        color: #0d6efd;
    }
</style>

@script
<script>
    document.addEventListener('livewire:init', () => {
        const carousel = document.getElementById('imageCarousel');
        if (carousel) {
            carousel.addEventListener('slid.bs.carousel', (event) => {
                const activeItem = event.relatedTarget;
                const mediaId = activeItem.getAttribute('data-media-id');
                Livewire.dispatch('carousel-changed', [mediaId]);
            });
        }

        Livewire.on('media-updated', (event) => {
            const mediaId = event.mediaId;
            const isApproved = event.isApproved;
            const targetItem = document.querySelector(`.carousel-item[data-media-id="${mediaId}"]`);
            if (targetItem) {
                const badges = targetItem.querySelector('.carousel-caption .d-flex');
                badges.innerHTML = '';
                if (isApproved) {
                    badges.innerHTML += '<span class="badge badge-transparent bg-primary">Approved</span>';
                } else {
                    badges.innerHTML += '<span class="badge badge-transparent bg-warning text-dark">Not Approved</span>';
                }
                if (targetItem.dataset.isProfile === 'true') {
                    badges.innerHTML += '<span class="badge badge-transparent bg-success">Profile</span>';
                }
            }
            updateButtons(mediaId, isApproved);
        });

        Livewire.on('update-buttons', (event) => {
            const mediaId = event.mediaId;
            const isApproved = event.isApproved;
            updateButtons(mediaId, isApproved);
        });

        function updateButtons(mediaId, isApproved) {
            const footer = document.querySelector('.modal-footer .d-flex');
            const approveBtn = footer.querySelector('.btn-success');
            const unapproveBtn = footer.querySelector('.btn-warning');
            const spinner = footer.querySelector('.spinner-border');
            if (spinner) spinner.remove(); // حذف اسپینر بعد از اتمام لود
            if (isApproved) {
                if (approveBtn) approveBtn.remove();
                if (!unapproveBtn) {
                    footer.insertAdjacentHTML('beforeend', `<button wire:click="unapproveMedia(${mediaId})" class="btn btn-warning"><i class="bi bi-x-circle me-1"></i> Unapprove</button>`);
                }
            } else {
                if (unapproveBtn) unapproveBtn.remove();
                if (!approveBtn) {
                    footer.insertAdjacentHTML('beforeend', `<button wire:click="approveMedia(${mediaId})" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Approve</button>`);
                }
            }
        }
    });
</script>
@endscript
