{{-- Nav Header --}}
<div class="nav-header">
    <a href="{{ route('admin.dashboard') }}" class="brand-logo">
        <img class="logo-abbr" src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="" style="max-height: 50px;">
        <span class="brand-title" style="font-size: 18px; font-weight: 600; margin-left: 10px; color: #3b4bd3;">{{ app()->getLocale() == 'ar' ? \App\Models\Setting::get('site_name_ar', 'My Trip') : \App\Models\Setting::get('site_name_en', 'My Trip') }}</span>
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>
