<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link type="text/css" rel="stylesheet" href="{{ mix('css/reset.css') }}"/>
  <link type="text/css" rel="stylesheet" href="{{ mix('css/dashboard/main.css') }}"/>

  <script src="/js/jquery.min.js"></script>
  <script src="/js/dashboard.js" defer></script>
  <title>Test</title>
</head>
<body>
<div id="app" class="h-full" data-page="{{ json_encode($page) }}"></div>
</body>
</html>
