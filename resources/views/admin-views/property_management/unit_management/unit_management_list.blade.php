@extends('layouts.back-end.app')

@section('title', ui_change('units_list' , 'property_config'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{-- <img src="{{ asset(main_path() . 'back-end/img/inhouse-subscription-list.png') }}" alt=""> --}}
                {{ ui_change('units_list' , 'property_config') }}
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $unit_management->total() }}</span>
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_config.inline-menu')


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
                                            placeholder="{{ ui_change('search_by_name' , 'property_config') }}" aria-label="Search orders"
                                            value="{{ request('search') }}">
                                        <input type="hidden" value="{{ request('status') }}" name="status">
                                        <button type="submit" class="btn btn--primary">{{ ui_change('search' , 'property_config') }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                            <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">

                                {{-- @can('create_unit_management') --}}
                                    <a href="{{ route('unit_management.create_new') }}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ ui_change('unit_configuration' , 'property_config') }}</span>
                                    </a>
                                    <a href="{{ route('import_property_master') }}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ ui_change('import_excel' , 'property_config') }}</span>
                                    </a>
                                    <a href="{{ route('export_units') }}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ ui_change('export_excel' , 'property_config') }}</span>
                                    </a>
                                {{-- @endcan --}}
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable" style="text-align: {{ Session::get('locale') === 'ar' ? 'right' : 'left' }};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ ui_change('sl' , 'property_config') }}</th>
                                    <th class="text-center">{{ ui_change('property' , 'property_config') }}</th>
                                    <th class="text-center">{{ ui_change('block' , 'property_config') }}</th>
                                    <th class="text-center">{{ ui_change('floor' , 'property_config') }}</th>
                                    <th class="text-center">{{ ui_change('unit' , 'property_config') }}</th>
                                    <th class="text-center">{{ ui_change('unit_description' , 'property_config') }}</th>
                                    <th class="text-center">{{ ui_change('unit_type' , 'property_config') }}</th>
                                    <th class="text-center">{{ ui_change('status' , 'property_config') }}</th>
                                    <th class="text-center">{{ ui_change('Actions' , 'property_config') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unit_management as $k => $unit_management_item)
                                    <tr>
                                        <th scope="row">{{ $unit_management->firstItem() + $k }}</th>

                                        <td class="text-center">
                                            {{ $unit_management_item->property_unit_management->name ?? ui_change('not_available' , 'property_config')}}
                                        </td>
                                        <td class="text-center">
                                            {{ $unit_management_item->block_unit_management->block->name ?? ui_change('not_available' , 'property_config')}}
                                        </td>
                                        <td class="text-center">
                                            {{ $unit_management_item->floor_unit_management->floor_management_main->name ?? ui_change('not_available' , 'property_config')}}
                                        </td>

                                        <td class="text-center">
                                            {{ $unit_management_item->unit_management_main->name ?? ui_change('not_available' , 'property_config')}}
                                        </td>

                                        <td class="text-center">
                                            {{ $unit_management_item->unit_description->name ?? ui_change('not_available' , 'property_config')}}
                                        </td>

                                        <td class="text-center">
                                            {{ $unit_management_item->unit_type->name ?? ui_change('not_available' , 'property_config')}}
                                        </td>

                                        <td class="text-center">
                                            @can('change_unit_management_status')
                                                <form action="{{ route('unit_management.status-update') }}" method="post"
                                                    id="subscription_status{{ $unit_management_item->id }}_form"
                                                    class="subscription_status_form">
                                                    @csrf
                                                @endcan
                                                <input type="hidden" name="id"
                                                    value="{{ $unit_management_item->id }}">
                                                <label class="switcher mx-auto">
                                                    <input type="checkbox" class="switcher_input"
                                                        id="subscription_status{{ $unit_management_item->id }}"
                                                        name="status" value="1"
                                                        {{ $unit_management_item->status == 'active' ? 'checked' : '' }}
                                                        onclick="toogleStatusModal(event,'subscription_status{{ $unit_management_item->id }}',
                                                'subscription-status-on.png','subscription-status-off.png',
                                                '{{ ui_change('Want_to_Turn_ON' , 'property_config') }} {{ $unit_management_item->name }} ',
                                                '{{ ui_change('Want_to_Turn_OFF' , 'property_config') }} {{ $unit_management_item->name }} ',
                                                `<p>{{ ui_change('if_enabled_this_subscription_will_be_available' , 'property_config') }}</p>`,
                                                `<p>{{ ui_change('if_disabled_this_subscription_will_be_hidden' , 'property_config') }}</p>`)">
                                                    <span class="switcher_control"></span>
                                                </label>
                                                @can('change_unit_management_status')
                                                </form>
                                            @endcan

                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2"> 
                                                    <a class="btn btn-outline--primary btn-sm square-btn"
                                                        title="{{ ui_change('edit' , 'property_config') }}"
                                                        href="{{ route('unit_management.edit', [$unit_management_item->id]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                    <a class="btn btn-outline--primary  "
                                                        title="{{ ui_change('invoices' , 'property_config') }}"
                                                        href="{{ route('unit_management.invoices', [$unit_management_item->id]) }}">
                                                         {{ ui_change('invoices' , 'property_config') }}
                                                    </a>
                                                {{-- @endcan --}}
                                                @can('delete_unit_management')
                                                <a class="btn btn-outline-danger btn-sm delete square-btn"
                                                    title="{{ ui_change('delete' , 'property_config') }}"
                                                    id="{{ $unit_management_item->id }}">
                                                    <i class="tio-delete"></i>
                                                </a>
                                                @endcan

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
                            {{ $unit_management->links() }}
                        </div>
                    </div>

                    @if (count($unit_management) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ asset(main_path() . 'assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ ui_change('no_data_to_show' , 'property_config') }}</p>
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
        // Call the dataTables jQuery plugin
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
                url: "{{ route('unit_management.status-update') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(data) {
                    if (data.success == true) {
                        toastr.success('{{ ui_change('updated_successfully' , 'property_config') }}');
                    } else if (data.success == false) {
                        toastr.error(
                            '{{ ui_change('Status_updated_failed.'  , 'property_config')}} {{ ui_change('Product_must_be_approved' , 'property_config') }}'
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
                title: "{{ ui_change('are_you_sure_delete_this' , 'property_config') }}",
                text: "{{ ui_change('you_will_not_be_able_to_revert_this' , 'property_config') }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ ui_change('yes_delete_it' , 'property_config') }}!",
                cancelButtonText: "{{ ui_change('cancel' , 'property_config') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('unit_management.delete') }}",
                        method: 'get',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success("{{ ui_change('deleted_successfully' , 'property_config') }}");
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
