<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.clean')] class extends Component {};
?>

<div class="card">
    <div class="card-header">Dashboard</div>
    <div class="card-body">
        Welcome to the Dashboard, {{ auth()->user()->display_name ?? 'User' }}!
    </div>
</div>
