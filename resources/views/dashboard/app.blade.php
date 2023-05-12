<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $settings['main_language']) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link type="text/css" rel="stylesheet" href="{{ asset('css/reset.css') }}"/>
  <link type="text/css" rel="stylesheet" href="{{ asset('css/dashboard/libs.min.css') }}"/>
  <link type="text/css" rel="stylesheet" href="{{ asset('css/dashboard/main.css') }}"/>

  <title>{{ $settings['site_title'] }}</title>
</head>
<body>

<div id="app" class="h-full" data-page="{{ json_encode($page) }}"></div>

<script src="/js/manifest.js"></script>
<script src="/js/vendor.js"></script>
<script src="/js/dashboard.js" defer></script>

@isset($page['props']['scripts'])
  @foreach($page['props']['scripts'] as $script)
    <script src="{{ $script }}"></script>
  @endforeach
@endisset
</body>
</html>
