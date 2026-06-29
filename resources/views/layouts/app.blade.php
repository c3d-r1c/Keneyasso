<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/assets/images/favicon.ico">
    <title>@yield('title', 'Keneyasso') - Keneyasso</title>

    <link rel="stylesheet" href="/assets/css/vendors_css.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/skin_color.css">

    @livewireStyles
    @stack('styles')
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
<div class="wrapper">
    <div id="loader"></div>

    {{-- ═══ HEADER ═══ --}}
    <header class="main-header">
        <div class="d-flex align-items-center logo-box justify-content-start">
            <a href="{{ route('home') }}" class="logo">
                <div class="logo-mini w-50">
                    <span class="light-logo"><img src="/assets/images/logo-letter.png" alt="logo"></span>
                    <span class="dark-logo"><img src="/assets/images/logo-letter.png" alt="logo"></span>
                </div>
                <div class="logo-lg">
                    <span class="light-logo"><img src="/assets/images/logo-dark-text.png" alt="logo"></span>
                    <span class="dark-logo"><img src="/assets/images/logo-light-text.png" alt="logo"></span>
                </div>
            </a>
        </div>
        <nav class="navbar navbar-static-top">
            <div class="app-menu">
                <ul class="header-megamenu nav">
                    <li class="btn-group nav-item">
                        <a href="#" class="waves-effect waves-light nav-link push-btn btn-primary-light" data-toggle="push-menu" role="button">
                            <i class="icon-Menu"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                    </li>
                    <li class="btn-group d-lg-inline-flex d-none">
                        <div class="app-menu">
                            <div class="search-bx mx-5">
                                <form>
                                    <div class="input-group">
                                        <input type="search" class="form-control" placeholder="Search">
                                        <div class="input-group-append">
                                            <button class="btn" type="submit"><i class="icon-Search"><span class="path1"></span><span class="path2"></span></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="navbar-custom-menu r-side">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="waves-effect waves-light dropdown-toggle w-auto l-h-12 bg-transparent p-0 no-shadow" data-bs-toggle="dropdown" title="User">
                            <div class="d-flex pt-1">
                                <div class="text-end me-10">
                                    <p class="pt-5 fs-14 mb-0 fw-700 text-primary">{{ auth()->user()?->name ?? 'Admin' }}</p>
                                    <small class="fs-10 mb-0 text-uppercase text-mute">Admin</small>
                                </div>
                                <img src="/assets/images/avatar/avatar-1.png" class="avatar rounded-10 bg-primary-light h-40 w-40" alt="">
                            </div>
                        </a>
                        <ul class="dropdown-menu animated flipInX">
                            <li class="user-body">
                                <a class="dropdown-item" href="#"><i class="ti-user text-muted me-2"></i> Profil</a>
                                <a class="dropdown-item" href="#"><i class="ti-lock text-muted me-2"></i> Déconnexion</a>
                            </li>
                        </ul>
                    </li>
                    <li class="btn-group nav-item d-lg-inline-flex d-none">
                        <a href="#" data-provide="fullscreen" class="waves-effect waves-light nav-link full-screen btn-warning-light" title="Full Screen">
                            <i class="icon-Position"></i>
                        </a>
                    </li>
                    <li class="dropdown notifications-menu">
                        <a href="#" class="waves-effect waves-light dropdown-toggle btn-info-light" data-bs-toggle="dropdown" title="Notifications">
                            <i class="icon-Notification"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                        <ul class="dropdown-menu animated bounceIn">
                            <li class="header">
                                <div class="p-20">
                                    <div class="flexbox">
                                        <div><h4 class="mb-0 mt-0">Notifications</h4></div>
                                        <div><a href="#" class="text-danger">Clear All</a></div>
                                    </div>
                                </div>
                            </li>
                            <li><ul class="menu sm-scrol">
                                <li><a href="#"><i class="fa fa-users text-info"></i> No new notifications</a></li>
                            </ul></li>
                            <li class="footer"><a href="#">View all</a></li>
                        </ul>
                    </li>
                    <li class="btn-group nav-item">
                        <a href="#" data-toggle="control-sidebar" title="Setting" class="waves-effect full-screen waves-light btn-danger-light">
                            <i class="icon-Settings1"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="main-sidebar">
        <section class="sidebar position-relative">
            <div class="multinav">
                <div class="multinav-scroll" style="height: 100%;">
                    <ul class="sidebar-menu" data-widget="tree">

                        @foreach(app(\App\Services\SidebarRegistry::class)->items() as $item)
                            @if($item->hasChildren())
                                <li class="treeview">
                                    <a href="#">
                                        <i class="{{ $item->icon }}"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        <span>{{ $item->label }}</span>
                                        <i class="ti-angle-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        @foreach($item->children as $child)
                                            <li>
                                                <a href="{{ route($child->route) }}">
                                                    <i class="{{ $child->icon }}"><span class="path1"></span><span class="path2"></span></i>
                                                    {{ $child->label }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route($item->route) }}">
                                        <i class="{{ $item->icon }}"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        <span>{{ $item->label }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach

                    </ul>

                    <div class="sidebar-widgets">
                        <div class="copyright text-center m-25">
                            <p><strong class="d-block">Keneyasso</strong> &copy; {{ date('Y') }} All Rights Reserved</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </aside>

    {{-- ═══ CONTENT ═══ --}}
    <div class="content-wrapper">
        @yield('content')
    </div>

    {{-- ═══ CONTROL SIDEBAR ═══ --}}
    <aside class="control-sidebar control-sidebar-dark">
        <div class="rpanel-title">
            <span data-toggle="control-sidebar"><i class="ion ion-close text-danger hand bg-danger-light"></i></span>
        </div>
        <div class="slimScrollDiv">
            <ul class="nav nav-tabs customtab" id="customtab">
                <li class="nav-item"><a class="nav-link active" href="#setting" data-bs-toggle="tab"><span class="hidden-sm-up"><i class="ti-settings"></i></span><span class="hidden-xs-down">Setting</span></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active p-20" id="setting">
                    <h6 class="font-medium m-b-10">Select Layout</h6>
                    <div class="demo-radio-button">
                        <input name="group1" class="radio-col-primary chk-col-primary" id="radio1" type="radio" value="light-skin" checked>
                        <label for="radio1">Light skin</label>
                        <input name="group1" class="radio-col-primary chk-col-primary" id="radio2" type="radio" value="dark-skin">
                        <label for="radio2">Dark skin</label>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    <div class="control-sidebar-bg"></div>
</div>

@livewireScripts
<script src="/assets/js/vendors.min.js"></script>
<script src="/assets/js/pages/chat-popup.js"></script>
<script src="/assets/js/app.js"></script>
@stack('scripts')
</body>
</html>
