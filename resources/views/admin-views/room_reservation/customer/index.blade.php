@extends('layouts.back-end.app')
@php
    $lang = Session::get('locale');
@endphp
@section('title', ui_change($route, 'room_reservation'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                {{-- <img width="60" src="{{ asset('assets/back-end/img/' . $route . '.jpg') }}" alt=""> --}}
                {{ ui_change($route, 'room_reservation') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @php
            $currentUrl = url()->current();
            $segments = explode('/', $currentUrl);
            $before_last = $segments[count($segments) - 2] ?? null;
            $facility_masters = ['room-block', 'room-floor', 'room-building', 'room-unit'];
        @endphp
        @if (in_array($before_last, $facility_masters))
            @include('admin-views.inline_menu.room_reservation.management.inline-menu')
        @else
            @include('admin-views.inline_menu.room_reservation.master.inline-menu')
        @endif

        <!-- Content Row -->
        <div class="row">


            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <button type="button" data-add_new="" data-toggle="modal" data-target="#add_new"
                                    class="btn btn--primary createButton">
                                    <i class="tio-add"></i>
                                    <span class="text">{{ ui_change('add_new', 'property_transaction') }}</span>
                                </button>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ ui_change('search_by_' . $route . '_name', 'room_reservation') }}"
                                            aria-label="Search" value="{{ $search }}" required>
                                        <button type="submit"
                                            class="btn btn--primary">{{ ui_change('search', 'room_reservation') }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>
                    <div style="text-align: {{ $lang === 'ar' ? 'right' : 'left' }};">
                        <div class="table-responsive">
                            <table id="datatable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ ui_change('sl', 'room_reservation') }}</th>
                                        <th class="text-center">{{ ui_change($route . '_name', 'room_reservation') }}
                                        </th>

                                        <th class="text-center">{{ ui_change('type', 'room_reservation') }}</th>
                                        <th class="text-center">{{ ui_change('gender', 'room_reservation') }}</th>
                                        <th class="text-center">{{ ui_change('birth_date', 'room_reservation') }}</th>
                                        <th class="text-center">{{ ui_change('age', 'room_reservation') }}</th>

                                        <th class="text-center">{{ ui_change('actions', 'room_reservation') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($main as $key => $value)
                                        <tr>
                                            <td>{{ $main->firstItem() + $key }}</td>
                                            <td class="text-center">{{ $value->name }}</td>

                                            <td class="text-center">{{ $value->type }} </td>
                                            <td class="text-center">{{ $value->gender }} </td>
                                            <td class="text-center">
                                                {{ $value->birth_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $value->birth_date)->format('d/m/Y') : '' }}
                                            </td>
                                            <td class="text-center">{{ $value->age }} </td>

                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a id="edit_{{ $route }}_item"
                                                        class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ __('general.edit') }}" data-toggle="modal"
                                                        data-target="#edit_{{ $route }}"
                                                        data-{{ $route }}_id="{{ $value->id }}">
                                                        <i class="tio-edit"></i>
                                                    </a>


                                                    <a class="btn btn-outline-danger btn-sm delete square-btn"
                                                        title="{{ ui_change('delete', 'room_reservation') }}"
                                                        id="{{ $value['id'] }}">
                                                        <i class="tio-delete"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {!! $main->links() !!}
                        </div>
                    </div>

                    @if (count($main) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ asset('assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ ui_change('no_data_to_show', 'room_reservation') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>




    <div class="modal fade" id="edit_customer" tabindex="-1" role="dialog" aria-labelledby="editCustomerLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ ui_change('edit_customer', 'property_report') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('customer.update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <input type="hidden" name="id" id="edit_customer_id">

                    <div class="modal-body">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{ ui_change('name', 'property_report') }}</label>
                                    <input type="text" class="form-control" name="name" id="edit_name"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Gender -->
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{ ui_change('gender', 'property_report') }}</label>
                                    <select name="gender" id="edit_gender" class="js-select2-custom form-control">
                                        <option value="">{{ ui_change('select', 'property_report') }}</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                                            {{ ui_change('male', 'property_report') }}</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                            {{ ui_change('female', 'property_report') }}</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Birthdate -->
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{ ui_change('birth_date', 'property_report') }}</label>
                                    <input type="text" name="birthdate" id="edit_birthdate" class="form-control date"
                                        value="{{ old('birthdate') }}">
                                    @error('birthdate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- ID Type -->
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{ ui_change('period', 'property_report') }}</label>
                                    <select name="id_type" id="edit_id_type" class="form-control">
                                        <option value="passport" {{ old('id_type') == 'passport' ? 'selected' : '' }}>
                                            {{ ui_change('passport', 'property_report') }} </option>
                                        <option value="id_card" {{ old('id_type') == 'id_card' ? 'selected' : '' }}>
                                            {{ ui_change('id_card', 'property_report') }} </option>
                                        <option value="driving_license"
                                            {{ old('id_type') == 'driving_license' ? 'selected' : '' }}>
                                            {{ ui_change('driving_license', 'property_report') }} </option>
                                    </select>
                                    @error('period_from')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Document File -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{ ui_change('Upload File', 'property_report') }}</label>
                                    <input type="file" name="document_file" id="edit_document_file"
                                        class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    <small class="text-muted">Upload Image Or PDF</small>
                                    @error('document_file')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ ui_change('cancel') }}</button>
                        <button type="submit" class="btn btn--primary">{{ ui_change('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   

    <div class="modal fade" id="add_new" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ ui_change('Add_Customer', 'property_report') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('customer.store') }}" id="checkin-form" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{ ui_change('name', 'property_report') }}</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <!-- Gender -->
                                <div class="form-group">
                                    <label>{{ ui_change('gender', 'property_report') }}</label>
                                    <select name="gender" class="js-select2-custom form-control">
                                        <option value="">{{ ui_change('select', 'property_report') }}</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                                            {{ ui_change('male', 'property_report') }}</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                            {{ ui_change('female', 'property_report') }}</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <!-- Birthdate -->
                                <div class="form-group">
                                    <label>{{ ui_change('birth_date', 'property_report') }}</label>
                                    <input type="text" name="birthdate" class="form-control date"
                                        value="{{ old('birthdate') }}">
                                    @error('birthdate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <!--   / ID type -->
                                <div class="form-group">
                                    <label>{{ ui_change('period', 'property_report') }}</label>
                                    <select name="id_type" class="form-control">
                                        <option value="passport" {{ old('id_type') == 'passport' ? 'selected' : '' }}>
                                            {{ ui_change('passport', 'property_report') }}</option>
                                        <option value="id_card" {{ old('id_type') == 'id_card' ? 'selected' : '' }}>
                                            {{ ui_change('id_card', 'property_report') }}</option>
                                        <option value="driving_license"
                                            {{ old('id_type') == 'driving_license' ? 'selected' : '' }}>
                                            {{ ui_change('driving_license', 'property_report') }}</option>
                                    </select>
                                    @error('id_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <!-- Document File -->
                                <div class="form-group">
                                    <label>{{ ui_change('Upload File', 'property_report') }}</label>
                                    <input type="file" name="document_file" class="form-control"
                                        accept=".jpg,.jpeg,.png,.pdf">
                                    <small class="text-muted">Upload Image Or PDF</small>
                                    @error('document_file')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ ui_change('Cancel', 'property_report') }}</button>
                        <button type="submit"
                            class="btn btn--primary">{{ ui_change('save', 'property_report') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            // var route_name = document.getElementById('route_name').value;
            Swal.fire({
                title: "{{ ui_change('are_you_sure_delete_this', 'room_reservation') }}",
                text: "{{ ui_change('you_will_not_be_able_to_revert_this', 'room_reservation') }}!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ ui_change('yes_delete_it', 'room_reservation') }}!',
                cancelButtonText: '{{ ui_change('cancel', 'room_reservation') }}',
                type: 'warning',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route($route . '.delete') }}",
                        method: 'get',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success(
                                '{{ ui_change('deleted_successfully', 'room_reservation') }}'
                            );
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
    <script>
        $(document).on('click', '#edit_{{ $route }}_item', function(e) {
            e.preventDefault(); 
            var item_id = $(this).data('{{ $route }}_id');
 
            $('#edit_{{ $route }}').modal('show');

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "GET",
                url: "{{ route($route . '.edit', ':id') }}".replace(':id', item_id),
                success: function(response) {

                    if (response.status === 404) {
                        $('#success_message').html("")
                            .addClass("alert alert-danger")
                            .text(response.message);
                    } else { 
                        $('#edit_customer_id').val(item_id);
                        $('#edit_name').val(response.main_info.name);
                        $('#edit_birthdate').val(response.main_info.birthdate);
                        // $('#edit_id_type').val(response.main_info.id_type);
                    
                        if ($('#gender').length) {
                            $('#gender').val(response.main_info.gender).trigger('change');
                        } 
                        if ($('#edit_id_type').length) {
                            $('#edit_id_type').val(response.main_info.id_type).trigger('change');
                        } 
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                }
            });
        });
    </script>


    <script>
        flatpickr(".date", {
            dateFormat: "d/m/Y",
        });
    </script>
@endpush
