@extends('layouts.app')

@section('title', __('Trips Management'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Trips') }}</a></li>
        </ol>
    </div>

    @push('styles')
    <style>
        .premium-filter-bar {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            margin-bottom: 35px;
            border: 1px solid #f0f0f0;
            display: block;
            width: 100%;
        }
        .filter-group {
            position: relative;
            margin-bottom: 0;
        }
        .filter-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #488eff;
            z-index: 10;
        }
        .filter-group .form-control {
            padding-left: 40px;
            height: 50px;
            border-radius: 10px;
            border: 1px solid #eef2f7;
            background: #fcfdfe;
            transition: all 0.3s ease;
        }
        .filter-group .form-control:focus {
            border-color: #488eff;
            box-shadow: 0 0 0 4px rgba(72, 142, 255, 0.1);
            background: #fff;
        }
        .filter-label {
            font-size: 13px;
            font-weight: 700;
            color: #4a5568;
            margin-bottom: 10px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .form-section-title {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
            margin: 20px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #edf2f7;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .modal-xl { max-width: 1200px; }
        .border-dashed { border-style: dashed !important; }

        /* Fix for RTL if needed, but assuming LTR for now as per code items */
        .ms-auto { margin-right: 0 !important; margin-left: auto !important; }
    </style>
    @endpush
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-xl-3 col-sm-6 my-2">
                    <x-stats-card
                        :label="__('Total Trips')"
                        :value="$stats['total']"
                        icon="flaticon-025-dashboard"
                        color="primary"
                    />
                </div>
                <div class="col-xl-3 col-sm-6 my-2">
                    <x-stats-card
                        :label="__('Active Trips')"
                        :value="$stats['active']"
                        icon="flaticon-381-success-2"
                        color="success"
                    />
                </div>
                <div class="col-xl-3 col-sm-6 my-2">
                    <x-stats-card
                        :label="__('Inactive Trips')"
                        :value="$stats['inactive']"
                        icon="flaticon-381-error"
                        color="warning"
                    />
                </div>
                <div class="col-xl-3 col-sm-6 my-2">
                    <x-stats-card
                        :label="__('Expired Trips')"
                        :value="$stats['expired']"
                        icon="flaticon-381-clock"
                        color="danger"
                    />
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="premium-filter-bar">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="filter-label">{{ __('Company') }}</label>
                        <div class="filter-group">
                            <i class="fas fa-building"></i>
                            <select id="company_id" class="form-control default-select">
                                <option value="">{{ __('All Companies') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label">{{ __('Departure') }}</label>
                        <div class="filter-group">
                            <i class="fas fa-plane-departure"></i>
                            <select id="from_country_id" class="form-control default-select">
                                <option value="">{{ __('From Country') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label">{{ __('Destination') }}</label>
                        <div class="filter-group">
                            <i class="fas fa-map-marker-alt"></i>
                            <select id="to_country_id" class="form-control default-select">
                                <option value="">{{ __('To Country') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label">{{ __('Expiry Date') }}</label>
                        <div class="filter-group">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="date" id="expiry_date" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('Trips List') }}</h4>
                    <a href="{{ route('admin.trips.create') }}" class="btn btn-primary btn-rounded">
                         <i class="fa fa-plus me-2"></i> {{ __('Add New Trip') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="trips-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('title') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('From') }}</th>
                                    <th>{{ __('To') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Expiry Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Image Upload Modal (Relocated from cards) --}}
    <div class="modal fade" id="tripImagesModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Upload photos of the trip') }}: <span id="target-trip-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="trip-images-upload" class="dropzone border-dashed"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('Done') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
    {{-- Renew Trip Modal --}}
    <div class="modal fade" id="renewTripModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Expiry Date Trips') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="renewTripForm">
                <input type="hidden"  id="edit_id" >
                <div class="modal-body">

                    <div class="form-group mb-3">
                        <label for="{{__('Expiry Date')}}" class="form-label">{{__('Expiry Date')}}</label>
                        <span class="text-danger">*</span>
                        <input type="date" id="new_expiry_date" name="expiry_date"  class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" onclick="submitRenewal()"> {{ __('Update Expiry Date') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    let tripsTable;
    const tripsDataUrl = "{{ route('admin.trips.data') }}";
    const updateUrl = "{{ route('admin.trips.update', ':id') }}";



    $(document).ready(function() {
        // Initialize DataTable
        // Initialize premium filters UI
        if($.fn.niceSelect) {
            $('.default-select').niceSelect();
        }

        tripsTable = $('#trips-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
            url: '{{ route("admin.trips.data") }}',
            data: function (d) {
                d.company_id      = $('#company_id').val();
                d.from_country_id = $('#from_country_id').val();
                d.to_country_id   = $('#to_country_id').val();
                d.expiry_date     = $('#expiry_date').val();
            }
        },
            columns: [
                {data: 'title'},
                {data: 'company', defaultContent: "<i>Not Available</i>"},
                {data: 'fromCountry', defaultContent: "<i>Not Available</i>"},
                {data: 'toCountry' , defaultContent: "<i>Not Available</i>" },
                {data: 'price'},
                {data: 'expiry_date' },
                {data: 'status', orderable:false, searchable:false},
                {data: 'actions', orderable:false, searchable:false},
            ],

            createdRow: function(row, data, dataIndex) {
                let today = new Date().toISOString().split('T')[0];
                if (data.expiry_date < today) {
                    $(row).css('background-color', '#ffe5e5'); // لون أحمر خفيف للمنتهي
                    $(row).attr('title', 'هذه الرحلة منتهية الصلاحية');
                }
            },
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });


        // إعادة التحميل عند تغيير الفلاتر
        $('#company_id, #from_country_id, #to_country_id, #expiry_date').change(function () {
            tripsTable.ajax.reload();
        });
    });

    // Dropzone initialization
    Dropzone.autoDiscover = false;
    let myDropzone;

    function openImageUpload(id, name) {
        $('#target-trip-name').text(name);
        $('#tripImagesModal').modal('show');

        // Initialize Dropzone if not already initialized
        if (!myDropzone) {
            myDropzone = new Dropzone("#trip-images-upload", {
                url: "{{ route('admin.trips.images-store', ':id') }}".replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                paramName: "file",
                maxFilesize: 5,
                acceptedFiles: "image/*",
                addRemoveLinks: true,
                dictDefaultMessage: "{{ __('Drag and drop photos here to upload') }}",
                init: function() {
                    this.on("success", function(file, response) {
                        toastr.success(response.message || "{{ __('Image uploaded successfully') }}");
                    });
                    this.on("error", function(file, response) {
                        toastr.error(response.error || "{{ __('Error while uploading the image') }}");
                    });
                }
            });
        } else {
            // Update URL for the new trip ID
            myDropzone.options.url = "{{ route('admin.trips.images-store', ':id') }}".replace(':id', id);
            myDropzone.removeAllFiles();
        }

        // Load existing images if needed (optional enhancement)
    }

    function toggleTripStatus(id) {
        const url = "{{ route('admin.trips.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this Trips status?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, Change it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            tripsTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
    function renewTrip(id) {
        $('#edit_id').val(id); // وضع ID الرحلة في الحقل المخفي
        $('#renewTripModal').modal('show'); // إظهار النافذة
    }
    function submitRenewal() {
        const id = $('#edit_id').val();
        let expiryDate = $('#new_expiry_date').val();
        if(!expiryDate) {
            alert("يرجى اختيار التاريخ");
            return;
        }
        const url = "{{ route('admin.trips.renew', ':id') }}".replace(':id', id);
        $.ajax({
            url:url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                expiry_date: expiryDate
            },
            success: function(response) {
                if (response.success) {
                    $('#renewTripModal').modal('hide');
                    tripsTable.ajax.reload();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('Something went wrong');
                }
            }
        });

    }

    // function renewTrip(id) {
    //     const newDate = prompt("أدخل تاريخ الانتهاء الجديد (YYYY-MM-DD):");
    //     const url = "{{ route('admin.trips.toggle-status', ':id') }}".replace(':id', id);

    //     Swal.fire({
    //         title: '{{ __("Are you sure?") }}',
    //         text: '{{ __("Do you want to toggle this Trips status?") }}',
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: '{{ __("Yes, Change it!") }}'
    //     }).then((result) => {
    //         if (result.value) {
    //             $.ajax({
    //                 url: url,
    //                 method: 'POST',
    //                 data: {
    //                     _token: $('meta[name="csrf-token"]').attr('content')
    //                 },
    //                 success: function(response) {
    //                     if (response.success) {
    //                         tripsTable.ajax.reload(null, false);
    //                         toastr.success(response.message);
    //                     }
    //                 }
    //             });
    //         }
    //     });
    // }

    // function renewTrip(id) {
    //     const newDate = prompt("أدخل تاريخ الانتهاء الجديد (YYYY-MM-DD):");
    //     const url = "{{ route('admin.trips.renew', ':id') }}".replace(':id', id);
    //     if (newDate) {
    //         $.ajax({
    //             url: url, // تأكد من إنشاء هذا المسار في الـ Routes
    //             type: 'POST',
    //             data: {
    //                 _token: '{{ csrf_token() }}',
    //                 expiry_date: newDate
    //             },
    //             success: function(response) {
    //                 alert('تم تجديد الرحلة بنجاح!');
    //                 tripsTable.ajax.reload(); // إعادة تحميل الجدول
    //             },
    //             error: function(err) {
    //                 alert('حدث خطأ، يرجى التأكد من صيغة التاريخ.');
    //             }
    //         });
    //     }
    // }

    function deleteTrip(id) {
        let url = "{{ route('admin.trips.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __("Delete Trips??") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            tripsTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }


</script>



@endsection
