{{-- Sidebar start --}}
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            <li class="nav-label">{{ __('Main Menu') }}</li>
            <li>
                <a href="{{ route('admin.dashboard') }}" aria-expanded="false">
                    <i class="flaticon-025-dashboard"></i>
                    <span class="nav-text">{{ __('Dashboard') }}</span>
                </a>
            </li>

            <li class="nav-label">{{ __('Settings') }}</li>
            <li>
                <a href="{{ route('admin.settings.index') }}" aria-expanded="false">
                    <i class="flaticon-381-settings-2"></i>
                    <span class="nav-text">{{ __('Platform Settings') }}</span>
                </a>
            </li>
            <li class="nav-label">{{ __('Security & Access') }}</li>
            @can('view users')
            <li>
                <a href="{{ route('admin.users.index') }}" aria-expanded="false">
                    <i class="flaticon-050-info"></i>
                    <span class="nav-text">{{ __('User Management') }}</span>
                </a>
            </li>
            @endcan

            @can('view roles')
            <li>
                <a href="{{ route('admin.roles.index') }}" aria-expanded="false">
                    <i class="flaticon-381-settings"></i>
                    <span class="nav-text">{{ __('Roles') }}</span>
                </a>
            </li>
            @endcan

            @can('view permissions')
            <li>
                <a href="{{ route('admin.permissions.index') }}" aria-expanded="false">
                    <i class="flaticon-381-lock"></i>
                    <span class="nav-text">{{ __('Permissions') }}</span>
                </a>
            </li>
            @endcan

            {{-- Content Management --}}
            <li class="nav-label">{{ __('Content Management') }}</li>
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-globe"></i>
                    <span class="nav-text">{{ __('Locations') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.countries.index') }}">{{ __('Countries') }}</a></li>
                    <li><a href="{{ route('admin.cities.index') }}">{{ __('Cities') }}</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-globe"></i>
                    <span class="nav-text">{{ __('Company') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.companies.index') }}">{{ __('companies') }}</a></li>
                    <li><a href="{{ route('admin.company-codes.index') }}">{{ __('Company Codes') }}</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.banners.index') }}" aria-expanded="false">
                    <i class="flaticon-381-picture"></i>
                    <span class="nav-text">{{ __('Banners') }}</span>
                </a>
            </li>

            {{-- Hotel & Flight Categories --}}
            <li class="nav-label">{{ __('Booking Management') }}</li>
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-045-heart"></i>
                    <span class="nav-text">{{ __('Hotels') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.bookings.hotels.index') }}">{{ __('Hotels List & Map') }}</a></li>
                    <li><a href="{{ route('admin.bookings.hotels.requests') }}">{{ __('Hotel Booking Requests') }}</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-041-graph"></i>
                    <span class="nav-text">{{ __('Flights') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.bookings.flights.available') }}">{{ __('Available Flights') }}</a></li>
                    <li><a href="{{ route('admin.bookings.flights.requests') }}">{{ __('Flight Booking Requests') }}</a></li>
                    <li><a href="{{ route('admin.bookings.flights.ongoing') }}">{{ __('Tickets & Ongoing Flights') }}</a></li>
                </ul>
            </li>
        </ul>

        <div class="copyright text-center">
            <p><strong>My Trip Admin</strong> © {{ date('Y') }} All Rights Reserved</p>
        </div>
    </div>
</div>
{{-- Sidebar end --}}
