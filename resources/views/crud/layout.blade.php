<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ config('app.name') }}@hasSection('title') - @yield('title')@endif</title>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="description" content="@yield('description')">
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ url()->current() }}" />

        <!-- STYLES -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" integrity="sha256-hHKA3d/HttC8OW3Sl093W8DoZudhHJDj++kZYo6MLzA=" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.min.css" integrity="sha256-qpXwccFYncH5gTqXFe9EXZ8QeDw1Re68bbNVfFZzIzg=" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ route('crud-policies.httpFileSend.asset', [
            'type' => 'css',
            'fileUri' => 'crud.css'
        ]) }}">

        @stack('styles')
    </head>
    <body>
        <div id="app">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
                <div class="container-fluid">
                    <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarScroll">
                        <ul class="navbar-nav ms-auto my-2 my-lg-0 navbar-nav-scroll " style="--bs-scroll-height: 100px;">
                            @auth()
                            <li class="nav-item"><span class="nav-link text-success">{{ auth()->user()->name }}</span></li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="container-fluid">
                <div class="row">
                    @if(isset($modelsClasses))
                    <nav id="sidebarMenu" class="col-md-3 col-lg-2 min-height-100vh d-md-block bg-light sidebar collapse">
                        <div class="sidebar-sticky pt-3 d-flex flex-column justify-content-between align-items-center">
                            <div class="col-12 mt-4">
                                @foreach($modelsClasses as $crudModel)
                                @if(
                                    auth()->user()->can('viewAny', $crudModel) or
                                    auth()->user()->can('create', $crudModel)
                                )
                                <h6 class="sidebar-heading px-3 mb-1 text-muted">
                                    <span>{{ __(ucfirst(collect(explode('\\', $crudModel))->last())) }}</span>
                                </h6>
                                @php
                                $crudModel = new $crudModel();
                                @endphp
                                <ul class="nav flex-column mb-2">
                                    @can('viewAny', $crudModel)
                                    <li class="nav-item"><a class="nav-link" href="{{ CrudController::getRoutePrefixed(sprintf('%s.index', $crudModel->getTable())) }}">{{ __('crud-policies::crud.viewAny') }}</a></li>
                                    @endcan
                                    @can('create', $crudModel)
                                    <li class="nav-item"><a class="nav-link" href="{{ CrudController::getRoutePrefixed(sprintf('%s.create', $crudModel->getTable())) }}">{{ __('crud-policies::crud.create') }}</a></li>
                                    @endcan
                                </ul>
                                <hr>
                                @endcan
                                @endforeach
                            </div>
                        </div>
                    </nav>

                    <main role="main" class="col-md-9 mt-5 mb-5 ml-sm-auto col-lg-10 px-md-4">
                    @else
                    <main role="main" class="mt-5 mb-5">
                    @endif
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
        @stack('scriptsConstants')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha256-edRDsV9ULIqKzKjpN/KjyQ7Lp4vUn9usbJuHjH8Sk+k=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.all.min.js" integrity="sha256-551HBsteMvKOSqjUXSmRy/EOF0bBNbWB96b5L3Deh8A=" crossorigin="anonymous"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="{{ route('crud-policies.httpFileSend.asset', [
            'type' => 'js',
            'fileUri' => 'crud.js'
        ]) }}"></script>
        @stack('scripts')
    </body>
</html>
