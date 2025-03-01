<?php
use Livewire\Volt\Component;

new class extends Component {};
?>

<x-settings.layout heading="Appearance" subheading="Customize the look of your dashboard">
    <form class="form-horizontal">
        <div class="row mb-3">
            <label for="theme" class="col-sm-3 col-form-label">Theme</label>
            <div class="col-sm-9">
                <select class="form-control" id="theme">
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-9 offset-sm-3">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
</x-settings.layout>
