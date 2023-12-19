<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  @vite(['resources/assets/css/admin/app.scss', 'resources/assets/css/reset.scss','resources/js/admin/app.js'])

  @isset($css) @vite($css) @endisset
  @isset($js) @vite($js) @endisset

  @inertiaHead
</head>
<body>
@inertia
</body>
</html>