<x-settings.layout heading="Appearance" subheading="Customize the look of your dashboard">
    <form class="form-horizontal">
        <div class="row mb-3">
            <label for="theme" class="col-sm-3 col-form-label">{{ __('Theme') }}</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-palette"></i></span>
                    <select class="form-control" id="theme">
                        <option value="light">{{ __('Light') }}</option>
                        <option value="dark">{{ __('Dark') }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-9 offset-sm-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> {{ __('Save') }}
                </button>
            </div>
        </div>
    </form>
</x-settings.layout>
