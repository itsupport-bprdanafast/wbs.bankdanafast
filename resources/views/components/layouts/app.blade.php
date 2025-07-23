<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Whistleblowing Bank Danafast' }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon-dark.png') }}"
        media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32-dark.png') }}"
        media="(prefers-color-scheme: dark)">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" media="(prefers-color-scheme: dark)">

    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    <tallstackui:script />
    @livewireStyles
    @if (app()->environment('local'))
        @vite(['resources/css/app.css'])
    @elseif (app()->environment('production'))
        <link href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}" rel="stylesheet">
    @endif
</head>

<body>
    <x-dialog />
    <x-banner wire />
    <x-toast class="z-20" />

    {{ $slot }}

    @if (app()->environment('local'))
        @vite(['resources/js/app.js'])
    @elseif (app()->environment('production'))
        <script src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}" defer></script>
    @endif
</body>

</html>
