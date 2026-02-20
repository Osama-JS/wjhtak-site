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
                    <button type="button" class="btn btn-primary btn-rounded" data-bs-toggle="modal" data-bs-target="#addTripsModal">
                         <i class="fa fa-plus me-2"></i> {{ __('Add New Trip') }}
                    </button>
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

<!-- Add Modal -->
<div class="modal fade" id="addTripsModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fa fa-plus-circle me-2"></i>{{ __('Add New Trip') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTripsForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6 border-end">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> {{ __('General Information') }}
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <x-forms.input-text name="title" :label="__('Trip Title')" required icon="fa fa-pen" />
                                </div>
                                <div class="col-md-12">
                                    <x-forms.textarea name="description" :label="__('Description')" required rows="6" />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.select name="company_id" :label="__('Company')" :options="$companies" searchable required />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input-text name="duration" :label="__('Duration')" placeholder="e.g. 5 Days" icon="fa fa-clock" />
                                </div>
                            </div>
                        </div>

                        <!-- Logistics & Pricing -->
                        <div class="col-md-6">
                            <div class="form-section-title">
                                <i class="fas fa-map-marker-alt"></i> {{ __('Logistics & Pricing') }}
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <x-forms.select name="from_country_id" :label="__('From Country')" :options="$countries" searchable required />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.select name="to_country_id" :label="__('To Country')" :options="$countries" searchable required />
                                </div>
                                <div class="col-md-12">
                                    <x-forms.select name="from_city_id" :label="__('From City')" :options="$cities" searchable required />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.input-text name="price" :label="__('Current Price')" required icon="fa fa-dollar-sign" />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.input-text name="price_before_discount" :label="__('Old Price')" icon="fa fa-tag" />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.input-text name="tickets" :label="__('Tickets')" required icon="fa fa-ticket-alt" />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input-text name="personnel_capacity" :label="__('Capacity')" icon="fa fa-users" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Expiry Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="expiry_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-section-title">
                                <i class="fas fa-cogs"></i> {{ __('Visibility & Status') }}
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <x-forms.checkbox name="is_public" :label="__('Public')" checked type="switch" />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.checkbox name="is_ad" :label="__('Advertisement')" checked type="switch" />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.checkbox name="active" :label="__('Active')" checked type="switch" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Trip') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editTripsModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fa fa-edit me-2"></i>{{ __('Edit Trip Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTripsForm">
                @csrf
                @method('PUT')
                <input type="hidden"  id="edit_id" >
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6 border-end">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> {{ __('General Information') }}
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <x-forms.input-text id="edit_title" name="title" :label="__('Trip Title')" required icon="fa fa-pen" />
                                </div>
                                <div class="col-md-12">
                                    <x-forms.textarea id="edit_description" name="description" :label="__('Description')" required rows="6" />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.select id="edit_company_id" name="company_id" :label="__('Company')" :options="$companies" searchable required />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input-text id="edit_duration" name="duration" :label="__('Duration')" placeholder="e.g. 5 Days" icon="fa fa-clock" />
                                </div>
                            </div>
                        </div>

                        <!-- Logistics & Pricing -->
                        <div class="col-md-6">
                            <div class="form-section-title">
                                <i class="fas fa-map-marker-alt"></i> {{ __('Logistics & Pricing') }}
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <x-forms.select id="edit_from_country_id" name="from_country_id" :label="__('From Country')" :options="$countries" searchable required />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.select id="edit_to_country_id" name="to_country_id" :label="__('To Country')" :options="$countries" searchable required />
                                </div>
                                <div class="col-md-12">
                                    <x-forms.select id="edit_from_city_id" name="from_city_id" :label="__('From City')" :options="$cities" searchable required />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.input-text id="edit_price" name="price" :label="__('Current Price')" required icon="fa fa-dollar-sign" />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.input-text id="edit_price_before_discount" name="price_before_discount" :label="__('Old Price')" icon="fa fa-tag" />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.input-text id="edit_tickets" name="tickets" :label="__('Tickets')" required icon="fa fa-ticket-alt" />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input-text id="edit_personnel_capacity"  name="personnel_capacity" :label="__('Capacity')" icon="fa fa-users" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Expiry Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" id="edit_expiry_date" name="expiry_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-section-title">
                                <i class="fas fa-cogs"></i> {{ __('Visibility & Status') }}
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <x-forms.checkbox id="edit_is_public" name="is_public" :label="__('Public')" checked type="switch" />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.checkbox id="edit_is_ad" name="is_ad" :label="__('Advertisement')" checked type="switch" />
                                </div>
                                <div class="col-md-4">
                                    <x-forms.checkbox id="edit_active" name="active" :label="__('Active')" checked type="switch" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Trip') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="renewTripModal">
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


         $('#addTripsForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.trips.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        console.log(response);
                        $('#addTripsModal').modal('hide');
                        $('#addTripsForm')[0].reset();
                        tripsTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => {
                            toastr.error(err[0]);
                        });
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        });

         // Handle Edit Form Submit
        $('#editTripsForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            const url = updateUrl.replace(':id', id);
            const formData = $(this).serialize() + '&_method=PUT';

            $.ajax({
                url:url,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editTripsModal').modal('hide');
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
        });


            // بما أنك تستخدم AJAX لملء الجدول، سننتظر حتى يكتمل تحميل البيانات
        // var checkDataTable = setInterval(function() {
        //     // نبحث عن أول زر "تعديل" أو "رفع" يحتوي على الـ ID
        //     let firstTripBtn = $('.btn-info[onclick^="openImageUpload"]').first();

        //     if (firstTripBtn.length > 0) {
        //         // استخراج المعاملات من onclick="openImageUpload(ID, 'TITLE')"
        //         let onClickAttr = firstTripBtn.attr('onclick');
        //         // استخراج القيم باستخدام Regex أو ببساطة تشغيل الوظيفة
        //         firstTripBtn.click();

        //         clearInterval(checkDataTable); // توقف عن البحث بمجرد التشغيل
        //     }
        // }, 500); // يفحص كل نصف ثانية حتى يظهر الجدول

    });

    Dropzone.autoDiscover = false;
    let myDropzone = null; // تعريف المتغير خارجاً

    function openImageUpload(id, title) {
        // 1. Show the Modal and update title
        $('#tripImagesModal').modal('show');
        $('#target-trip-name').text(title);

        let storeUrl = "{{ route('admin.trips.images-store', ':id') }}".replace(':id', id);
        let getUrl   = "{{ route('admin.trips.get-images', ':id') }}".replace(':id', id);

        // 2. Destroy previous instance to prevent conflicts
        if (myDropzone !== null) {
            myDropzone.destroy();
        }

        // 3. إنشاء نسخة جديدة مخصصة لهذه الرحلة فقط
        myDropzone = new Dropzone("#trip-images-upload", {
            url: storeUrl,
            method: "post",
            paramName: "file",
            maxFilesize: 5,
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            dictRemoveFile: "{{ __('Delete') }}",
            dictDefaultMessage: "{{ __('Drop files here or click to upload') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function() {
                let dz = this;

                // 4. جلب الصور الخاصة بهذه الرحلة من السيرفر
                $.ajax({
                    url: getUrl,
                    type: 'GET',
                    cache: false, // مهم جداً لمنع تكرار صور الرحلات السابقة
                    success: function(data) {
                        $.each(data, function(key, value) {
                            let mockFile = {
                                name: value.name,
                                size: value.size,
                                serverId: value.id
                            };
                            dz.displayExistingFile(mockFile, value.url);
                            dz.emit("complete", mockFile);
                        });
                    }
                });

                // الأحداث
                this.on("success", function(file, response) {
                    file.serverId = response.id;
                });

                this.on("removedfile", function(file) {
                    if (file.serverId) {
                        let deleteUrl = "{{ route('admin.trips.images-destroy', ':id') }}".replace(':id', file.serverId);
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                        });
                    }
                });
            }
        });
    }



    function editTrip(id) {
        let url = "{{ route('admin.trips.show', ':id') }}".replace(':id', id);
        console.log('URL:', url);

        $.get(url, function(response) {
            console.log('Response:', response);

            if (response.success) {
                let c = response.Trip;
                $('#edit_id').val(c.id);
                $('#edit_title').val(c.title);
                $('#edit_tickets').val(c.tickets);
                $('#edit_description').val(c.description);

                // Set values for selects
                $('#edit_company_id').val(c.company_id);
                $('#edit_from_country_id').val(c.from_country_id);
                $('#edit_from_city_id').val(c.from_city_id);
                $('#edit_to_country_id').val(c.to_country_id);

                // Set duration, price, capacity
                $('#edit_duration').val(c.duration);
                $('#edit_price').val(c.price);
                $('#edit_price_before_discount').val(c.price_before_discount);
                $('#edit_personnel_capacity').val(c.personnel_capacity);

                // Date handling
                if(c.expiry_date) {
                    let formattedDate = c.expiry_date.split(' ')[0];
                    $('#edit_expiry_date').val(formattedDate);
                } else {
                    $('#edit_expiry_date').val('');
                }

                // Checkboxes - using !! to force boolean
                $('#edit_is_public').prop('checked', !!parseInt(c.is_public));
                $('#edit_is_ad').prop('checked', !!parseInt(c.is_ad));
                $('#edit_active').prop('checked', !!parseInt(c.active));

                // Force Update UI Libraries
                setTimeout(function() {
                    // Update Select2 (for searchable selects)
                    if ($.fn.select2) {
                        $('#edit_company_id, #edit_from_country_id, #edit_from_city_id, #edit_to_country_id').trigger('change.select2');
                    }

                    // Update nice-select (if used)
                    if ($.fn.niceSelect) {
                        $('select').niceSelect('update');
                    }
                }, 100);

                $('#editTripsModal').modal('show');
            } else {
                toastr.error('Could not load trip data');
            }
        }).fail(function(xhr) {
            console.log('AJAX Error:', xhr.responseText);
            toastr.error('Failed to fetch trip data');
        });
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
