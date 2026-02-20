@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.subscribers.index') }}">{{ __('Subscribers') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Profile') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-3 col-lg-4">
        <div class="clearfix">
            <div class="card card-bx profile-card author-profile m-b30">
                <div class="card-body">
                    <div class="p-5">
                        <div class="author-profile text-center">
                            <div class="author-media">
                                <img src="{{ $user->profile_photo_url }}" alt="" class="rounded-circle shadow" width="130" height="130" style="object-fit: cover;">
                            </div>
                            <div class="author-info">
                                <h6 class="title">{{ $user->full_name }}</h6>
                                <span>{{ $user->email }}</span><br>
                                <span class="badge {{ $user->status == 'active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($user->status) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="info-list text-center">
                        <ul class="list-unstyled">
                            <li class="border-bottom p-2"><strong>{{ __('Phone') }}:</strong> {{ $user->phone }}</li>
                            <li class="border-bottom p-2"><strong>{{ __('Country') }}:</strong> {{ $user->country ?? '---' }}</li>
                            <li class="border-bottom p-2"><strong>{{ __('City') }}:</strong> {{ $user->city ?? '---' }}</li>
                            <li class="border-bottom p-2"><strong>{{ __('Joined') }}:</strong> {{ $user->created_at->format('M d, Y') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Booking History') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>ID</strong></th>
                                <th><strong>{{ __('Trip') }}</strong></th>
                                <th><strong>{{ __('Status') }}</strong></th>
                                <th><strong>{{ __('Amount') }}</strong></th>
                                <th><strong>{{ __('Date') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->bookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->trip->title ?? '---' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($booking->status) {
                                            'pending' => 'badge-warning',
                                            'confirmed' => 'badge-success',
                                            'cancelled' => 'badge-danger',
                                            default => 'badge-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                                </td>
                                <td>{{ number_format($booking->total_price, 2) }} SAR</td>
                                <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">{{ __('No bookings found for this user.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
