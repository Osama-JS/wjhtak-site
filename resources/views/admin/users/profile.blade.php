@extends('layouts.app')

@section('title', $user->full_name)

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('Users') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $user->full_name }}</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ $user->profile_photo_url }}" class="rounded-circle" width="100" height="100" alt="">
                        <h4 class="mt-3 mb-1">{{ $user->full_name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        @if($user->status == 'active')
                            <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                        @endif
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('Phone') }}
                            <span>{{ $user->country_code }} {{ $user->phone }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('Country') }}
                            <span>{{ $user->country ?? '---' }}</span>
                        </li>
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('City') }}
                            <span>{{ $user->city ?? '---' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('Joined At') }}
                            <span>{{ $user->created_at->format('Y-m-d') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Trip Bookings') }} ({{ $user->bookings->count() }})</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>{{ __('Trip') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Tickets') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->bookings as $booking)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($booking->trip && $booking->trip->images->first())
                                                    <img src="{{ asset('storage/' . $booking->trip->images->first()->image_path) }}" class="rounded-lg me-2" width="40" alt="">
                                                @endif
                                                <span class="w-space-no">{{ $booking->trip->title ?? __('Deleted Trip') }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $booking->tickets_count }}</td>
                                        <td>${{ number_format($booking->total_price, 2) }}</td>
                                        <td>
                                            @if($booking->status == 'confirmed')
                                                <span class="badge badge-success">{{ __('Confirmed') }}</span>
                                            @elseif($booking->status == 'pending')
                                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Cancelled') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('No bookings found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
