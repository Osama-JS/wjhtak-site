@extends('layouts.app')

@section('title', 'Customer Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="fs-20">Welcome to My Trip, {{ auth()->user()->name }}!</h4>
            </div>
            <div class="card-body">
                <p>Enjoy your travel planning with us. Here you can view your bookings and manage your profile.</p>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-info-light">
                            <div class="card-body text-center">
                                <i class="flaticon-045-heart fs-30 text-info"></i>
                                <h5 class="mt-2">My Bookings</h5>
                                <p class="fs-12">View and manage your hotel and flight reservations.</p>
                                <a href="javascript:void(0);" class="btn btn-info btn-sm">Go to Bookings</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
