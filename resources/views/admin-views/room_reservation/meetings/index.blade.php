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
                                <a href="{{ route('meeting_room.create') }}" class="btn btn--primary">
                                    <i class="tio-add"></i> {{ ui_change('add_' . $route, 'room_reservation') }}
                                </a>
                                {{-- <h5 class="mb-0 d-flex align-items-center gap-2">
                                    {{ ui_change($route . '_list', 'room_reservation') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12"> </span>
                                </h5> --}}
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

                                        <th class="text-center">
                                            {{ ui_change($route . '_description', 'room_reservation') }}</th>
                                        @if ($route == 'room_block' || $route == 'room_floor')
                                            <th class="text-center">{{ ui_change('building', 'room_reservation') }}</th>
                                        @endif
                                        @if ($route == 'room_floor')
                                            <th class="text-center">{{ ui_change('Block', 'room_reservation') }}</th>
                                        @endif
                                        <th class="text-center">{{ ui_change('actions', 'room_reservation') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($main as $key => $value)
                                        <tr>
                                            <td>{{ $main->firstItem() + $key }}</td>
                                            <td class="text-center">{{ $value->name }}</td>

                                            <td class="text-center">
                                                {{ $value->property_unit_management?->name . 
                                                   '-' .
                                                    $value->block_unit_management?->block?->name . 
                                                    '-' .
                                                    $value->floor_unit_management?->floor_management_main?->name .
                                                    '-' . 
                                                    $value->name 
                                                  }}
                                            </td>
                                         
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a  href="{{ route('meeting_room.edit' , $value->id) }}"
                                                        class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ ui_change('edit', 'room_reservation') }}"  >
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
@endpush
