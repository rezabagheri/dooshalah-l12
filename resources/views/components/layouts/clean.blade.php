<?php
use Livewire\Volt\Component;

new class extends Component {
    public string $bodyClass = '';
};
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['fa', 'aii', 'ar', 'he']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.gstatic.com https://*.firebaseio.com https://doosh-chat.maloons.com;
        style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
        font-src 'self' https://fonts.gstatic.com;
        worker-src 'self' https://www.gstatic.com;
        connect-src 'self' https://*.firebaseio.com wss://*.firebaseio.com;
        img-src 'self' data:;
    ">

    <title>{{ $page_title ?? 'Dooshalah' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="{{ $bodyClass ?? $attributes->get('body-class', '') }}">
    {{ $slot }}
    @livewireScripts
</body>
</html>
