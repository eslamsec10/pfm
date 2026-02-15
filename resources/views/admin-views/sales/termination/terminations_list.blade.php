@extends('layouts.back-end.app')

@section('title', ui_change('all_terminations' , 'property_transaction') )

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                <img src="{{ asset(main_path() . 'back-end/img/inhouse-subscription-list.png') }}" alt="">
                {{ ui_change('all_terminations' , 'property_transaction')  }}
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $terminations->total() }}</span>
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
                                            placeholder="{{ ui_change('agreement_search' , 'property_transaction')  }}" aria-label="Search orders"
                                            value="{{ request('search') }}">
                                        <input type="hidden" value="{{ request('status') }}" name="status">
                                        <button type="submit" class="btn btn--primary">{{ ui_change('search' , 'property_transaction') }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
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
                                        <th class="text-center">{{ ui_change('agreement_no' , 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('agreement_date' , 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('tenant' , 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('tenant_type' , 'property_transaction') }}</th> 
                                        <th class="text-center">{{ ui_change('status' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('Actions' , 'property_transaction') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($terminations as $k => $termination_item)
                                        <tr>
                                            <th scope="row"><input class="check_bulk_item" name="bulk_ids[]"
                                                    type="checkbox" value="{{ $termination_item->id }}" />
                                                {{ $terminations->firstItem() + $k }}</th>

                                            <td class="text-center">
                                                {{ $termination_item->agreement->agreement_no ?? ui_change('not_available' , 'property_transaction') }}
                                            </td>

                                            <td class="text-center">
                                                @php
                                                    $formatted_date = date(
                                                        'd-m-Y',
                                                        strtotime($termination_item->agreement->agreement_date),
                                                    );
                                                @endphp
                                                {{ $formatted_date ?? ui_change('not_available' , 'property_transaction')  }}
                                            </td>

                                            <td class="text-center">
                                                {{ (isset($termination_item->tenant->type) && $termination_item->tenant->type == 'individual') ? $termination_item->tenant->name ?? ui_change('not_available' , 'property_transaction')  : $termination_item->tenant->company_name ?? ui_change('not_available' , 'property_transaction') }}
                                            </td>
                                            <td class="text-center">
                                                {{ isset($termination_item->tenant->type) ? ucfirst($termination_item->tenant->type) : ui_change('not_available' , 'property_transaction') }}
                                            </td>


                                            
                                            <td class="text-center">
                                                <span
                                                    class="{{ strtolower($termination_item->status) == 'pending'
                                                        ? 'bg-warning p-2 text-dark border border-warning rounded'
                                                        : (strtolower($termination_item->status) == 'approved'
                                                            ? 'bg-success p-2 text-white border border-success rounded'
                                                            : (strtolower($termination_item->status) == 'rejected'
                                                                ? 'bg-danger p-2 text-white border border-danger rounded'
                                                                : '')) }}">
                                                    {{ ucfirst($termination_item->status) ?? ui_change('not_available' , 'property_transaction')  }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">

                                                    {{-- @can('edit_agreement') --}}
                                                     
                                                    @if ($termination_item->status == 'pending' )
                                                        <a class="btn btn-outline-success  "
                                                            href="{{ route('termination.approved', [$termination_item->id]) }}">
                                                            {{ ui_change('approved' , 'property_transaction') }}
                                                        </a>
                                                    @endif
                                                     
                                                    @if ($termination_item->status == 'pending' )
                                                        <a class="btn btn-outline-danger  "
                                                            href="{{ route('termination.rejected', [$termination_item->id]) }}">
                                                            {{ ui_change('rejected' , 'property_transaction') }}
                                                        </a>
                                                    @endif
                                                    <a class="btn btn-outline--primary btn-sm square-btn"
                                                        title="{{ ui_change('edit' , 'property_transaction')  }}"
                                                        href="{{ route('termination.edit', [$termination_item->id]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                    
                                                     
                                                        <a class="btn btn-outline-danger btn-sm delete square-btn"
                                                            title="{{ ui_change('delete' , 'property_transaction')  }}"
                                                            id="{{ $termination_item->id }}">
                                                            <i class="tio-delete"></i>
                                                        </a> 

                                                </div>


                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{ $terminations->links() }}
                        </div>
                    </div>

                    @if (count($terminations) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ asset(main_path() . 'back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ ui_change('no_data_to_show' , 'property_transaction')  }}</p>
                        </div>
                    @endif
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
        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
 

        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: "{{ ui_change('are_you_sure_delete_this' , 'property_transaction')  }}",
                text: "{{ ui_change('you_will_not_be_able_to_revert_this' , 'property_transaction')  }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ ui_change('yes_delete_it' , 'property_transaction')  }}!",
                cancelButtonText: "{{ ui_change('cancel' , 'property_transaction') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('termination.delete') }}",
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
