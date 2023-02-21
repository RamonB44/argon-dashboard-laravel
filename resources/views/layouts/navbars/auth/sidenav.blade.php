@php
    $mantenimientos = ['gerencia.index', 'sedes.index', 'area.index', 'funcion.index', 'procesos.index'];
    $sub_mantenimientos = ['areas.gestion', 'funcion.destajo.index'];
    $gestion_usuarios = ['users.index', 'groups.index'];
    $auxiliares = ['treg.index', 'offday.index'];
    $gestion_employes = ['employes.index','manageprocess.index'];
    $gestion_attendances = [];
    $gestion_reports = [];
@endphp

<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main" style="z-index: auto;">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('home') }}" target="_blank">
            <img src="{{ asset('img/logo-ct-dark.png') }}" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">EzAttend</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}" href="{{ route('home') }}"
                    target="_blank">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), array_merge($mantenimientos, $sub_mantenimientos, $auxiliares), true) ? 'active' : '' }} collapsed"
                    aria-controls="manteManagement" data-bs-toggle="collapse"
                    aria-expanded="{{ in_array(Route::currentRouteName(), array_merge($mantenimientos, $sub_mantenimientos, $auxiliares), true) ? 'true' : 'false' }}"
                    role="button" href="#manteManagement" target="_blank">
                    <span class="sidenav-mini-icon"> M </span>
                    <span class="sidenav-normal"> Mantenimiento <b class="caret"></b></span>
                </a>
                <div class="collapse {{ in_array(Route::currentRouteName(), array_merge($mantenimientos, $sub_mantenimientos, $auxiliares), true) ? 'show' : '' }}"
                    id="manteManagement">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == 'gerencia.index' ? 'active' : '' }}"
                                href="{{ route('gerencia.index') }}">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                </div>
                                <span class="nav-link-text ms-1"> Gerencia </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == 'sedes.index' ? 'active' : '' }}"
                                href="{{ route('sedes.index') }}">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                </div>
                                <span class="nav-link-text ms-1"> Sedes </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == 'procesos.index' ? 'active' : '' }}"
                                href="{{ route('procesos.index') }}">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                </div>
                                <span class="nav-link-text ms-1"> Cultivo </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ in_array(Route::currentRouteName(), array_merge(['area.index', 'funcion.index'], $sub_mantenimientos), true) ? 'active' : '' }} collapsed"
                                aria-controls="areasManagement" data-bs-toggle="collapse"
                                aria-expanded="{{ in_array(Route::currentRouteName(), array_merge(['area.index', 'funcion.index'], $sub_mantenimientos), true) ? 'true' : 'false' }}"
                                role="button" href="#areasManagement">
                                <span class="sidenav-mini-icon"> AM </span>
                                <span class="sidenav-normal"> Gestion de Areas y Funciones <b class="caret"></b></span>
                            </a>
                            <div class="collapse {{ in_array(Route::currentRouteName(), array_merge(['area.index', 'funcion.index'], $sub_mantenimientos), true) ? 'show' : '' }}"
                                id="areasManagement">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ Route::currentRouteName() == 'areas.gestion' ? 'active' : '' }}"
                                            href="{{ route('areas.gestion') }}">
                                            <div
                                                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                                <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                            </div>
                                            <span class="nav-link-text ms-1"> Areas </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ Route::currentRouteName() == 'area.index' ? 'active' : '' }}"
                                            href="{{ route('area.index') }}">
                                            <div
                                                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                                <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                            </div>
                                            <span class="nav-link-text ms-1"> Areas por Proceso </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ Route::currentRouteName() == 'funcion.destajo.index' ? 'active' : '' }}"
                                            href="{{ route('funcion.destajo.index') }}">
                                            <div
                                                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                                <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                            </div>
                                            <span class="nav-link-text ms-1"> Funciones </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ Route::currentRouteName() == 'funcion.index' ? 'active' : '' }}"
                                            href="{{ route('funcion.index') }}">
                                            <div
                                                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                                <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                            </div>
                                            <span class="nav-link-text ms-1"> Funciones por Area </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ in_array(Route::currentRouteName(), $auxiliares, true) ? 'active' : '' }} collapsed"
                                aria-controls="auxiManagement" data-bs-toggle="collapse"
                                aria-expanded="{{ in_array(Route::currentRouteName(), $auxiliares, true) ? 'true' : 'false' }}"
                                role="button" href="#auxiManagement">
                                <span class="sidenav-mini-icon"> A </span>
                                <span class="sidenav-normal"> Auxiliares <b class="caret"></b></span>
                            </a>
                            <div class="collapse {{ in_array(Route::currentRouteName(), $auxiliares, true) ? 'show' : '' }}"
                                id="auxiManagement">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ Route::currentRouteName() == 'treg.index' ? 'active' : '' }}"
                                            href="{{ route('treg.index') }}">
                                            <div
                                                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                                <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                            </div>
                                            <span class="nav-link-text ms-1"> Tipo de Registro </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ Route::currentRouteName() == 'offday.index' ? 'active' : '' }}"
                                            href="{{ route('offday.index') }}">
                                            <div
                                                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                                <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                            </div>
                                            <span class="nav-link-text ms-1"> Feriados </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), array_merge($gestion_employes), true) ? 'active' : '' }} collapsed"
                    aria-controls="employeManagement" data-bs-toggle="collapse"
                    aria-expanded="{{ in_array(Route::currentRouteName(), array_merge($gestion_employes), true) ? 'true' : 'false' }}"
                    role="button" href="#employeManagement" target="_blank">
                    <span class="sidenav-mini-icon"> M </span>
                    <span class="sidenav-normal"> Gestion de Trabajadores <b class="caret"></b></span>
                </a>
                <div class="collapse {{ in_array(Route::currentRouteName(), array_merge($gestion_employes), true) ? 'show' : '' }}"
                    id="employeManagement">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == 'employes.index' ? 'active' : '' }}"
                                href="{{ route('employes.index') }}">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                </div>
                                <span class="nav-link-text ms-1"> Trabajadores </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == 'manageprocess.index' ? 'active' : '' }}"
                                href="{{ route('manageprocess.index') }}">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                </div>
                                <span class="nav-link-text ms-1"> Cambios de Personal </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), array_merge($gestion_attendances), true) ? 'active' : '' }} collapsed"
                    aria-controls="asistenciaManagement" data-bs-toggle="collapse"
                    aria-expanded="{{ in_array(Route::currentRouteName(), array_merge($gestion_attendances), true) ? 'true' : 'false' }}"
                    role="button" href="#asistenciaManagement" target="_blank">
                    <span class="sidenav-mini-icon"> A </span>
                    <span class="sidenav-normal"> Gestion de Asistencia <b class="caret"></b></span>
                </a>
                <div class="collapse {{ in_array(Route::currentRouteName(), array_merge($gestion_attendances), true) ? 'show' : '' }}"
                    id="asistenciaManagement">
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), array_merge($gestion_reports), true) ? 'active' : '' }} collapsed"
                    aria-controls="reportManagement" data-bs-toggle="collapse"
                    aria-expanded="{{ in_array(Route::currentRouteName(), array_merge($gestion_reports), true) ? 'true' : 'false' }}"
                    role="button" href="#reportManagement" target="_blank">
                    <span class="sidenav-mini-icon"> A </span>
                    <span class="sidenav-normal"> Gestion de Reportes <b class="caret"></b></span>
                </a>
                <div class="collapse {{ in_array(Route::currentRouteName(), array_merge($gestion_reports), true) ? 'show' : '' }}"
                    id="reportManagement">
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), $gestion_usuarios, true) ? 'active' : '' }} collapsed"
                    aria-controls="userManagement" data-bs-toggle="collapse"
                    aria-expanded="{{ in_array(Route::currentRouteName(), $gestion_usuarios, true) ? 'true' : 'false' }}"
                    role="button" href="#userManagement">
                    <span class="sidenav-mini-icon"> UM </span>
                    <span class="sidenav-normal"> Gestion de Usuarios <b class="caret"></b></span>
                </a>
                <div class="collapse {{ in_array(Route::currentRouteName(), $gestion_usuarios, true) ? 'show' : '' }}"
                    id="userManagement">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == 'users.index' ? 'active' : '' }}"
                                href="{{ route('users.index') }}">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                </div>
                                <span class="nav-link-text ms-1"> Usuarios </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == 'groups.index' ? 'active' : '' }}"
                                href="{{ route('groups.index') }}">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                                </div>
                                <span class="nav-link-text ms-1"> Permisos </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        <hr class="horizontal dark mt-0">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'users.profile' ? 'active' : '' }}"
                    href="{{ route('users.profile') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Perfil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'users.config' ? 'active' : '' }}"
                    href="{{ route('users.config') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Configuraciones</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidenav-footer mx-3 ">
        {{-- <div class="card card-plain shadow-none" id="sidenavCard">
            <img class="w-50 mx-auto" src="/img/illustrations/icon-documentation-warning.svg"
                alt="sidebar_illustration">
            <div class="card-body text-center p-3 w-100 pt-0">
                <div class="docs-info">
                    <h6 class="mb-0">Need help?</h6>
                    <p class="text-xs font-weight-bold mb-0">Please check our docs</p>
                </div>
            </div>
        </div>
        <a href="/docs/bootstrap/overview/argon-dashboard/index.html" target="_blank"
            class="btn btn-dark btn-sm w-100 mb-3">Documentation</a>
        <a class="btn btn-primary btn-sm mb-0 w-100"
            href="https://www.creative-tim.com/product/argon-dashboard-pro-laravel" target="_blank"
            type="button">Upgrade to PRO</a> --}}
    </div>
</aside>

