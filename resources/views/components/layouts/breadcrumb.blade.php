<div class="col-sm-6">
    @if (!empty($breadcrumbs))
        <ol class="breadcrumb float-sm-end">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item {{ $breadcrumb['active'] ? 'active' : '' }}"
                    @if ($breadcrumb['active']) aria-current="page" @endif>
                    @if (!$breadcrumb['active'])
                        <a href="{{ route($breadcrumb['route']) }}">{{ $breadcrumb['label'] }}</a>
                    @else
                        {{ $breadcrumb['label'] }}
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</div>
