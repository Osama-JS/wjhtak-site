{{-- Header start --}}
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        @yield('page-title', 'Dashboard')
                    </div>
                </div>
                <div class="nav-item d-flex align-items-center">
                    <div class="input-group search-area">
                        <input type="text" class="form-control" placeholder="">
                        <span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    {{-- Language Switcher --}}
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            @if(app()->getLocale() == 'ar')
                                <img src="{{ asset('images/flags/sa.png') }}" width="20" alt=""/>
                                <span class="ms-1 d-none d-sm-inline-block">العربية</span>
                            @else
                                <img src="{{ asset('images/flags/gb.png') }}" width="20" alt=""/>
                                <span class="ms-1 d-none d-sm-inline-block">English</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('lang.switch', 'en') }}" class="dropdown-item ai-icon">
                                <img src="{{ asset('images/flags/gb.png') }}" width="15" alt=""/>
                                <span class="ms-2">{{ __('English') }}</span>
                            </a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="dropdown-item ai-icon">
                                <img src="{{ asset('images/flags/sa.png') }}" width="15" alt=""/>
                                <span class="ms-2">{{ __('Arabic') }}</span>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link" href="javascript:void(0);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26.309" height="23.678" viewBox="0 0 26.309 23.678">
                          <path id="Path_1955" data-name="Path 1955" d="M163.217,78.043a7.409,7.409,0,0,1,10.5-10.454l.506.506.507-.506a7.409,7.409,0,0,1,10.5,10.454L175.181,88.686a1.316,1.316,0,0,1-1.912,0Zm11.008,7.823,9.1-9.632.027-.027a4.779,4.779,0,1,0-6.759-6.757l-1.435,1.437a1.317,1.317,0,0,1-1.861,0l-1.437-1.437a4.778,4.778,0,0,0-6.758,6.757l.026.027Z" transform="translate(-161.07 -65.42)" fill="#135846" fill-rule="evenodd"/>
                        </svg>
                        </a>
                    </li>
                    {{-- Profile Dropdown --}}
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar ?? asset('images/profile/pic1.jpg') }}" width="20" alt=""/>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item ai-icon">
                                <svg id="icon-user2" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span class="ms-2">{{ __('Profile') }} </span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item ai-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    <span class="ms-2">{{ __('Logout') }} </span>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
{{-- Header end --}}
