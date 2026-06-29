<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Keneyasso')</title>

    <link rel="stylesheet" href="/assets/css/vendors_css.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/skin_color.css">
</head>
<body class="hold-transition theme-primary bg-img" style="background-image: url(/assets/images/auth-bg/bg-1.jpg)">

    @yield('content')

    <script src="/assets/js/vendors.min.js"></script>
    <script src="/assets/js/template.js"></script>
</body>
</html>
