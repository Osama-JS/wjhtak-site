{{-- Sidebar start --}}
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" aria-expanded="false">
                    <i class="flaticon-025-dashboard"></i>
                    <span class="nav-text">{{ __('Dashboard') }}</span>
                </a>
            </li>

            {{-- Content Management --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-map"></i>
                    <span class="nav-text">{{ __('Locations') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.countries.index') }}">{{ __('Countries') }}</a></li>
                    <li><a href="{{ route('admin.cities.index') }}">{{ __('Cities') }}</a></li>
                </ul>
            </li>

            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-home"></i>
                    <span class="nav-text">{{ __('Company') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.companies.index') }}">{{ __('Companies') }}</a></li>
                    <li><a href="{{ route('admin.company-codes.index') }}">{{ __('Company Codes') }}</a></li>
                </ul>
            </li>

            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa fa-plane"></i>
                    <span class="nav-text">{{ __('Trips') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.trips.index') }}">{{ __('Trips') }}</a></li>
                </ul>
            </li>

            <li>
                <a href="{{ route('admin.trip-bookings.index') }}" aria-expanded="false">
                    <i class="flaticon-381-calendar-1"></i>
                    <span class="nav-text">{{ __('Bookings') }}</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.banners.index') }}" aria-expanded="false">
                    <i class="flaticon-381-picture"></i>
                    <span class="nav-text">{{ __('Banners') }}</span>
                </a>
            </li>

            {{-- Hotel & Flight Categories --}}
            <!-- <li>
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
            </li> -->

            <!-- <li>
                <a class="has-arrow" href="javascript:void() " aria-expanded="false">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-text">{{ __('Staff & Users') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.users.index') }}">{{ __('Manage Admins') }}</a></li>
                    <li><a href="{{ route('admin.subscribers.index') }}"></a></li>
                </ul>
            </li> -->
              <li>
                <a href="{{ route('admin.subscribers.index') }}" class="" aria-expanded="false">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-text">{{ __('Manage Subscribers') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.notifications.index') }}" class="" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="nav-text">{{ __('Notifications') }}</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.settings.index') }}" aria-expanded="false">
                    <i class="flaticon-381-settings-2"></i>
                    <span class="nav-text">{{ __('Platform Settings') }}</span>
                </a>
            </li>

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

        </ul>

        <div class="copyright text-center">
            <p><strong>{{ __('My Trip Admin') }}</strong> © {{ date('Y') }} {{ __('All Rights Reserved') }}</p>
        </div>
    </div>
</div>
{{-- Sidebar end --}}
