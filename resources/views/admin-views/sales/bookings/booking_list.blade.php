@extends('layouts.back-end.app')

@section('title', ui_change('all_bookings' , 'property_transaction') )

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                <img src="{{ asset(main_path() . 'back-end/img/inhouse-subscription-list.png') }}" alt="">
                {{ ui_change('all_bookings' , 'property_transaction')  }}
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $bookings->total() }}</span>
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_transaction.inline-menu')


        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-lg-4">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ ui_change('booking_search' , 'property_transaction')  }}" aria-label="Search orders"
                                            value="{{ request('search') }}">
                                        <input type="hidden" value="{{ request('status') }}" name="status">
                                        <button type="submit" class="btn btn--primary">{{ ui_change('search' , 'property_transaction')  }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                            <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">

                                {{-- @can('create_booking') --}}
                                <a href="{{ route('booking.create') }}" class="btn btn--primary">
                                    <i class="tio-add"></i>
                                    <span class="text">{{ ui_change('create_booking' , 'property_transaction') }}</span>
                                </a>
                                <button type="button" data-target="#filter" data-filter="" data-toggle="modal"
                                    class="btn btn--primary btn-sm">
                                    <i class="fas fa-filter"></i>
                                </button>
                                {{-- @endcan --}}
                            </div>
                        </div>
                    </div>
                    <form action="" method="get">

                        <div class="table-responsive">
                            <table id="datatable"
                                style="text-align: {{ Session::get('locale') === 'ar' ? 'right' : 'left' }};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th><input id="bulk_check_all" class="bulk_check_all" type="checkbox" />
                                            {{ ui_change('sl' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('booking_no' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('booking_date' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('tenant' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('tenant_type' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('status' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('Actions' , 'property_transaction')  }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $k => $booking_item)
                                        <tr>
                                            <th scope="row"><input class="check_bulk_item" name="bulk_ids[]"
                                                    type="checkbox" value="{{ $booking_item->id }}" />
                                                {{ $bookings->firstItem() + $k }}</th>

                                            <td class="text-center">
                                                {{ $booking_item->booking_no ?? ui_change('not_available' , 'property_transaction')  }}
                                            </td>

                                            <td class="text-center">
                                                @php
                                                    $formatted_date = date(
                                                        'd-m-Y',
                                                        strtotime($booking_item->booking_date),
                                                    );
                                                @endphp
                                                {{ $formatted_date ?? ui_change('not_available' , 'property_transaction')  }}
                                            </td>

                                            <td class="text-center">
                                                {{ $booking_item->tenant->type == 'individual' ? $booking_item->name ?? ui_change('not_available' , 'property_transaction')  : $booking_item->company_name ?? ui_change('not_available' , 'property_transaction') }}
                                            </td>
                                            <td class="text-center">
                                                {{ ucfirst($booking_item->tenant->type) ?? ui_change('not_available' , 'property_transaction')  }}
                                            </td>


                                            <td class="text-center">
                                                <span
                                                    class="{{ strtolower($booking_item->status) == 'pending'
                                                        ? 'bg-warning p-2 text-dark border border-warning rounded'
                                                        : (strtolower($booking_item->status) == 'completed'
                                                            ? 'bg-success p-2 text-white border border-success rounded'
                                                            : (strtolower($booking_item->status) == 'canceled'
                                                                ? 'bg-danger p-2 text-white border border-danger rounded'
                                                                : '')) }}">
                                                    {{ ucfirst($booking_item->status) ?? ui_change('not_available' , 'property_transaction') }}
                                                </span>
                                            </td>



                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if ($booking_item->booking_status == 'booking')
                                                        <a class="btn btn-outline--primary "
                                                            title="{{ ui_change('edit' , 'property_transaction') }}"
                                                            href="{{ route('booking.add_to_agreement', [$booking_item->id]) }}">
                                                            {{ ui_change('agreement' , 'property_transaction')  }}
                                                        </a>
                                                    @endif
                                                    <a class="btn btn-outline--primary "
                                                        href="{{ route('booking.check_property', [$booking_item->id]) }}">
                                                        {{ ui_change('check_property' , 'property_transaction') }}
                                                    </a>
                                                    <a class="btn btn-outline--primary btn-sm square-btn"
                                                        title="{{ ui_change('edit' , 'property_transaction')  }}"
                                                        href="{{ route('booking.edit', [$booking_item->id]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                    {{-- @endcan
                                                @can('delete_booking') --}}
                                                    <a class="btn btn-outline-danger btn-sm delete square-btn"
                                                        title="{{ ui_change('delete' , 'property_transaction')  }}" id="{{ $booking_item->id }}">
                                                        <i class="tio-delete"></i>
                                                    </a>
                                                    {{-- @endcan --}}

                                                </div>


                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive mt-4">
                            <div class="px-4 d-flex justify-content-lg-end">
                                <!-- Pagination -->
                                {{ $bookings->links() }}
                            </div>
                        </div>
                    </form>
                    @if (count($bookings) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ asset(main_path() . 'back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ ui_change('no_data_to_show' , 'property_transaction') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ ui_change('filter' , 'property_transaction') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="px-3 py-4">
                                <div class="row align-items-center">
                                    <div class="col-md-12 col-lg-4 col-xl-3">
                                        <label for="">
                                            {{ ui_change('booking_status' , 'property_transaction')  }}
                                        </label>
                                        <select name="booking_status" class="form-control select2">
                                            <option value="-1">{{ ui_change('All_Booking_Status' , 'property_transaction') }}</option>
                                            <option value="proposal">{{ ui_change('proposal' , 'property_transaction') }}</option>
                                            <option value="booking">{{ ui_change('booking' , 'property_transaction')  }}</option>
                                            <option value="agreement">{{ ui_change('agreement' , 'property_transaction')  }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 col-lg-4 col-xl-3">
                                        <label for="">
                                            {{ ui_change('status' , 'property_transaction')  }}
                                        </label>
                                        <select name="status" class="form-control select2">

                                            <option value="-1">{{ ui_change('All_Status' , 'property_transaction')  }}</option>
                                            <option value="pending" selected>{{ ui_change('Pending' , 'property_transaction')  }}</option>
                                            <option value="completed">{{ ui_change('Completed' , 'property_transaction')  }}</option>
                                            <option value="canceled">{{ ui_change('Canceled' , 'property_transaction')  }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 col-lg-4 col-xl-3">
                                        <label for="">
                                            {{ ui_change('from' , 'property_transaction') }}
                                        </label>
                                        <input type="text" id="from_search_date" name="from"
                                            class="form-control ">

                                    </div>
                                    <div class="col-md-12 col-lg-4 col-xl-3">
                                        <label for="">
                                            {{ ui_change('to' , 'property_transaction') }}
                                        </label>
                                        <input type="text" id="to_search_date" name="to" class="form-control ">

                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 justify-content-end mt-4">
                                <button type="submit" class="btn btn--primary px-4 m-2" name="bulk_action_btn"
                                    value="filter"> {{ ui_change('filter' , 'property_transaction')  }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>
        flatpickr("#from_search_date", {
            dateFormat: "d/m/Y",
        });
        flatpickr("#to_search_date", {
            dateFormat: "d/m/Y",

        });
    </script>
    <script>
        // defaultDate:
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        $('.subscription_status_form').on('submit', function(event) {
            event.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('booking.status-update') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(data) {
                    if (data.success == true) {
                        toastr.success('{{ ui_change('updated_successfully' , 'property_transaction')  }}');
                    } else if (data.success == false) {
                        toastr.error(
                            '{{ ui_change('Status_updated_failed.' , 'property_transaction')  }} {{ ui_change('Product_must_be_approved' , 'property_transaction')  }}'
                        );
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                }
            });
        });

        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: "{{ ui_change('are_you_sure_delete_this' , 'property_transaction') }}",
                text: "{{ ui_change('you_will_not_be_able_to_revert_this' , 'property_transaction') }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ ui_change('yes_delete_it' , 'property_transaction')  }}!",
                cancelButtonText: "{{ ui_change('cancel' , 'property_transaction')  }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('booking.delete') }}",
                        method: 'get',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success("{{ ui_change('deleted_successfully' , 'property_transaction')  }}");
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
