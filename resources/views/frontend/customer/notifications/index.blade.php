@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Notifications'))
@section('page-title', __('Notifications'))

@push('styles')
<style>
.notif-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    overflow: hidden;
}

.notif-header {
    padding: 18px 22px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notif-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}

.notif-item {
    display: flex;
    gap: 16px;
    padding: 18px 22px;
    border-bottom: 1px solid #f9fafb;
    transition: background .15s;
    text-decoration: none;
    color: inherit;
    position: relative;
}

.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background: #fafafa; }
.notif-item.unread { background: #fdfaf9; }
.notif-item.unread::before {
    content: '';
    position: absolute;
    inset-inline-start: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--accent-color, #e8532e);
}

.notif-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.unread .notif-icon {
    background: #fdf2f0;
    color: var(--accent-color, #e8532e);
}

.notif-content { flex: 1; min-width: 0; }

.notif-title {
    font-weight: 700;
    font-size: .9rem;
    color: #111827;
    margin-bottom: 4px;
}

.notif-text {
    font-size: .83rem;
    color: #64748b;
    line-height: 1.5;
}

.notif-time {
    font-size: .75rem;
    color: #94a3b8;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.btn-read-all {
    font-size: .8rem;
    font-weight: 600;
    color: var(--accent-color, #e8532e);
    border: none;
    background: transparent;
    cursor: pointer;
    padding: 0;
}

.btn-read-all:hover { text-decoration: underline; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}

.empty-state i { font-size: 3rem; margin-bottom: 16px; opacity: .5; }
</style>
@endpush

@section('content')
<div class="notif-card">
    <div class="notif-header">
        <h3>{{ __('All Notifications') }}</h3>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('customer.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn-read-all">
                    <i class="fas fa-check-double"></i> {{ __('Mark all as read') }}
                </button>
            </form>
        @endif
    </div>

    @forelse($notifications as $notif)
        @php
            $isUnread = $notif->unread();
            $data = $notif->data;
            $icon = 'fa-bell';

            // Custom icons based on type/content if needed
            if (isset($data['type'])) {
                if ($data['type'] === 'booking') $icon = 'fa-ticket-alt';
                if ($data['type'] === 'payment') $icon = 'fa-credit-card';
            }
        @endphp
        <div class="notif-item {{ $isUnread ? 'unread' : '' }}">
            <div class="notif-icon">
                <i class="fas {{ $icon }}"></i>
            </div>
            <div class="notif-content">
                <div class="notif-title">{{ $data['title'] ?? __('New Notification') }}</div>
                <div class="notif-text">{{ $data['message'] ?? $data['body'] ?? '' }}</div>
                <div class="notif-time">
                    <i class="far fa-clock"></i> {{ $notif->created_at->diffForHumans() }}
                </div>
            </div>
            @if($isUnread)
                <form action="{{ route('customer.notifications.read', $notif->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-read-all" title="{{ __('Mark as read') }}" style="color:#94a3b8">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <p>{{ __('No notifications at the moment.') }}</p>
        </div>
    @endforelse
</div>

@if($notifications->hasPages())
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $notifications->links() }}
    </div>
@endif

@endsection
