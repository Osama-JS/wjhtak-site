@extends('layouts.app')

@section('title', __('Dashboard'))
@section('page-title', __('Admin Dashboard'))


@section('content')
	<div class="container-fluid" style="padding: 10px;">
				<div class="row">
					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-12">
								<div class="row">
									<div class="col-xl-3 col-sm-6 my-2">
                                        <x-stats-card
                                            :label="__('Total Users')"
                                            :value="$stats['users']"
                                            icon="fas fa-users"
                                        />
									</div>
									<div class="col-xl-3 col-sm-6 my-2">
                                        <x-stats-card
                                            :label="__('Countries')"
                                            :value="$stats['countries']"
                                            icon="fas fa-globe"
                                        />
									</div>
									<div class="col-xl-3 col-sm-6 my-2">
                                        <x-stats-card
                                            :label="__('Cities')"
                                            :value="$stats['cities']"
                                            icon="fas fa-city"
                                        />
									</div>
									<div class="col-xl-3 col-sm-6 my-2">
                                        <x-stats-card
                                            :label="__('Active Banners')"
                                            :value="$stats['banners']"
                                            icon="fas fa-image"
                                        />
									</div>
									<div class="col-xl-3 col-sm-6 my-2">
                                        <x-stats-card
                                            :label="__('Companies')"
                                            :value="$stats['companies']"
                                            icon="fas fa-building"
                                        />
									</div>
									<div class="col-xl-3 col-sm-6 my-2">
                                        <x-stats-card
                                            :label="__('Company Codes')"
                                            :value="$stats['company_codes']"
                                            icon="fas fa-ticket-alt"
                                        />
									</div>
								</div>
							</div>
						</div>

                        {{-- Under Development Section --}}
                        <div class="row mt-4">
                            <div class="col-xl-12">
                                <div class="card bg-primary-light h-auto">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div class="d-flex align-items-center mb-3 mb-sm-0">
                                                <div class="dev-icon me-4">
                                                    <i class="fas fa-tools fa-3x text-primary"></i>
                                                </div>
                                                <div>
                                                    <h3 class="fs-24 font-w700 mb-1 text-black">{{ __('Platform is Under Development') }}</h3>
                                                    <p class="mb-0 text-black">{{ __('We are working hard to bring you more features soon. Stay tuned!') }}</p>
                                                </div>
                                            </div>
                                            <div class="progress-box text-center">
                                                <h4 class="font-w600 mb-2 text-primary">75% {{ __('Completed') }}</h4>
                                                <div class="progress default-progress" style="width: 200px;">
                                                    <div class="progress-bar bg-primary progress-animated" style="width: 75%; height:10px;" role="progressbar">
                                                        <span class="sr-only">75% Complete</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

						</div>
					</div>
				</div>
			</div>
@endsection

@section('scripts')

@endsection
