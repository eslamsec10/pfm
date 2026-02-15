@extends('layouts.back-end.app')

@section('title', ui_change('all_agreements', 'property_transaction'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                <img src="{{ asset(main_path() . 'back-end/img/inhouse-subscription-list.png') }}" alt="">
                {{ ui_change('all_agreements', 'property_transaction') }}
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $agreements->total() }}</span>
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_transaction.inline-menu')

        {{-- <form action="{{ url()->current() }}" method="GET"> --}}
        <form action="" method="get">
            @csrf
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
                                                placeholder="{{ ui_change('agreement_search', 'property_transaction') }}"
                                                aria-label="Search orders" value="{{ request('search') }}">
                                            <input type="hidden" value="{{ request('status') }}" name="status">
                                            <button type="submit"
                                                class="btn btn--primary">{{ ui_change('search', 'property_transaction') }}</button>
                                        </div>
                                    </form>
                                    <!-- End Search -->
                                </div>
                                <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
 
                                    <a href="{{ route('agreement.create') }}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span
                                            class="text">{{ ui_change('create_agreement', 'property_transaction') }}</span>
                                    </a>
                                    <a href="{{ route('import_contract') }}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ ui_change('import_excel', 'property_transaction') }}</span>
                                    </a>
                                    <a href="{{ route('export_units') }}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ ui_change('export_template_excel', 'property_transaction') }}</span>
                                    </a>
                                    <button type="button" data-target="#filter" data-filter="" data-toggle="modal"
                                        class="btn btn--primary btn-sm">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                    <button type="submit" name="bulk_action_btn" value="sign"
                                        class="btn btn--primary btn-sm">{{ ui_change('sign', 'property_transaction') }}
                                        <i class="fas fa-save"></i>
                                    </button> 
                                </div>
                            </div>
                        </div>

                        <div class="px-3 py-4">

                        </div>
                        <div class="table-responsive">
                            <table id="datatable"
                                style="text-align: {{ Session::get('locale') === 'ar' ? 'right' : 'left' }};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th><input id="bulk_check_all" class="bulk_check_all" type="checkbox" />
                                            {{ ui_change('sl', 'property_transaction') }}</th>
                                        <th class="text-center">
                                            {{ ui_change('agreement_no', 'property_transaction') }}</th>
                                        <th class="text-center">
                                            {{ ui_change('agreement_date', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('building', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('block', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('floor', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('unit', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('start_Date', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('end_Date', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('tenant', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('tenant_type', 'property_transaction') }}
                                        </th>
                                        <th class="text-center">
                                            {{ ui_change('booking_status', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('status', 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('Actions', 'property_transaction') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($agreements as $k => $agreement_item)
                                        @php
                                            $buildings = [];
                                            $blocks = [];
                                            $floors = [];
                                            $units = [];
                                            $commencements = [];
                                            $expiries = [];
                                            foreach ($agreement_item->agreement_units as $u) {
                                                $buildings[] =
                                                    $u->agreement_unit_main?->property_unit_management?->name ??
                                                    ui_change('not_available', 'property_transaction');
                                                $blocks[] =
                                                    $u->agreement_unit_main?->block_unit_management?->block?->name ??
                                                    ui_change('not_available', 'property_transaction');
                                                $floors[] =
                                                    $u->agreement_unit_main?->floor_unit_management
                                                        ?->floor_management_main?->name ??
                                                    ui_change('not_available', 'property_transaction');
                                                $units[] =
                                                    $u->agreement_unit_main?->unit_management_main?->name ??
                                                    ui_change('not_available', 'property_transaction');
                                                $commencements[] = $u->commencement_date
                                                    ? date('d-m-Y', strtotime($u->commencement_date))
                                                    : ui_change('not_available', 'property_transaction');
                                                $expiries[] = $u->expiry_date
                                                    ? date('d-m-Y', strtotime($u->expiry_date))
                                                    : ui_change('not_available', 'property_transaction');
                                            }

                                        @endphp
                                        <tr>
                                            <th scope="row"><input class="check_bulk_item" name="bulk_ids[]"
                                                    type="checkbox" value="{{ $agreement_item->id }}" />
                                                {{ $agreements->firstItem() + $k }}</th>

                                            <td class="text-center">
                                                {{ $agreement_item->agreement_no ?? ui_change('not_available', 'property_transaction') }}
                                            </td>

                                            <td class="text-center">
                                                @php
                                                    $formatted_date = date(
                                                        'd-m-Y',
                                                        strtotime($agreement_item->agreement_date),
                                                    );
                                                @endphp
                                                {{ $formatted_date ?? ui_change('not_available', 'property_transaction') }}
                                            </td>

                                            <td class="text-center">{!! implode('<br>', $buildings) !!}</td>
                                            <td class="text-center">{!! implode('<br>', $blocks) !!}</td>
                                            <td class="text-center">{!! implode('<br>', $floors) !!}</td>
                                            <td class="text-center">{!! implode('<br>', $units) !!}</td>
                                            {{-- Commencement Date --}}
                                            <td class="text-center">{!! implode('<br>', $commencements) !!}</td>

                                            {{-- Expiry Date --}}
                                            <td class="text-center">{!! implode('<br>', $expiries) !!}</td>
                                            <td class="text-center">
                                                {{ $agreement_item->tenant->type == 'individual' ? $agreement_item->tenant?->name ?? ui_change('not_available', 'property_transaction') : $agreement_item->tenant?->company_name ?? ui_change('not_available', 'property_transaction') }}
                                            </td>
                                            <td class="text-center">
                                                {{ ucfirst($agreement_item->tenant->type) ?? ui_change('not_available', 'property_transaction') }}
                                            </td>


                                            <td class="text-center">
                                                <span
                                                    class="{{ strtolower($agreement_item->booking_status) == 'agreement'
                                                        ? 'bg-warning p-2 text-dark border border-warning rounded'
                                                        : (strtolower($agreement_item->booking_status) == 'signed'
                                                            ? 'bg-success p-2 text-white border border-success rounded'
                                                            : (strtolower($agreement_item->booking_status) == 'canceled'
                                                                ? 'bg-danger p-2 text-white border border-danger rounded'
                                                                : '')) }}">
                                                    {{ ($agreement_item->booking_status == 'signed' ? ucfirst($agreement_item->booking_status) : ui_change('unsigned', 'property_transaction')) ?? ui_change('not_available', 'property_transaction') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="{{ strtolower($agreement_item->status) == 'pending'
                                                        ? 'bg-warning p-2 text-dark border border-warning rounded'
                                                        : (strtolower($agreement_item->status) == 'completed'
                                                            ? 'bg-success p-2 text-white border border-success rounded'
                                                            : (strtolower($agreement_item->status) == 'canceled'
                                                                ? 'bg-danger p-2 text-white border border-danger rounded'
                                                                : '')) }}">
                                                    {{ ucfirst($agreement_item->status) ?? ui_change('not_available', 'property_transaction') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-outline--primary"
                                                        data-toggle="dropdown">
                                                        &#8942;
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right">



                                                        <li>
                                                            <a class="btn btn-outline--primary dropdown-item"
                                                                href="{{ route('agreement.check_property', [$agreement_item->id]) }}">
                                                                {{ ui_change('check_property', 'property_transaction') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="btn btn-outline--primary dropdown-item"
                                                                href="{{ route('agreement.show_info', [$agreement_item->id]) }}">
                                                                {{ ui_change('show_info', 'property_transaction') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            @if ($agreement_item->booking_status != 'signed' && $agreement_item->status != 'canceled')
                                                                <a class="btn btn-outline-warning  dropdown-item "
                                                                    href="{{ route('agreement.signed', [$agreement_item->id]) }}">
                                                                    {{ ui_change('sign', 'property_transaction') }}
                                                                </a>
                                                            @endif
                                                        </li>
                                                        <li>
                                                            @if ($agreement_item->booking_status == 'signed')
                                                                <a class="btn btn-outline-warning  dropdown-item "
                                                                    href="{{ route('agreement.schedule', [$agreement_item->id]) }}">
                                                                    {{ ui_change('Rent_List', 'property_transaction') }}
                                                                </a>
                                                            @endif
                                                        </li>
                                                        <li>
                                                            <a class="btn btn-outline-danger  dropdown-item"
                                                                title="{{ ui_change('termination', 'property_transaction') }}"
                                                                href="{{ route('termination.add', [$agreement_item->id]) }}">
                                                                {{ ui_change('termination', 'property_transaction') }}
                                                            </a>
                                                        </li>

                                                        @if ($agreement_item->booking_status == 'signed')
                                                            <li>
                                                                <a class="btn btn-outline--primary  dropdown-item"
                                                                    title="{{ ui_change('renewal', 'property_transaction') }}"
                                                                    href="{{ route('renewal.create', [$agreement_item->id]) }}">
                                                                    {{ ui_change('renewal', 'property_transaction') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <a class="btn btn-outline--primary   dropdown-item"
                                                                title="{{ ui_change('edit', 'property_transaction') }}"
                                                                href="{{ route('agreement.edit', [$agreement_item->id]) }}">
                                                                {{ ui_change('edit', 'property_transaction') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="btn btn-outline--primary   dropdown-item"
                                                                title="{{ ui_change('review', 'property_transaction') }}"
                                                                href="{{ route('agreement.review', [$agreement_item->id]) }}">
                                                                {{ translate('review_Rent') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            @if ($agreement_item->status == 'pending')
                                                                <a class="btn btn-outline-danger   delete   dropdown-item"
                                                                    title="{{ ui_change('delete', 'property_transaction') }}"
                                                                    id="{{ $agreement_item->id }}">
                                                                    {{ ui_change('Delete', 'property_transaction') }}
                                                                </a>
                                                            @endif
                                                        </li>

                                                    </ul>
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
                {{ $agreements->links() }}
            </div>
        </div>

        @if (count($agreements) == 0)
            <div class="text-center p-4">
                <img class="mb-3 w-160" src="{{ asset(main_path() . 'back-end') }}/svg/illustrations/sorry.svg"
                    alt="Image Description">
                <p class="mb-0">{{ ui_change('no_data_to_show', 'property_transaction') }}</p>
            </div>
        @endif
    </div>
    </div>
    </div>
    </div>
    </form>
    <div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ ui_change('filter', 'property_transaction') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="row align-items-center">


                                <div class="col-md-12 col-lg-4 col-xl-3">
                                    <label for="">
                                        {{ ui_change('tenants', 'property_transaction') }}
                                    </label>
                                    <select name="tenant_id" class="form-control select2">

                                        <option value="-1">{{ ui_change('All_Tenants', 'property_transaction') }}
                                        </option>
                                        @foreach ($tenants as $tenant_item)
                                            <option value="{{ $tenant_item->id }}">
                                                {{ $tenant_item->name ?? $tenant_item->company_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-12 col-lg-4 col-xl-3">
                                    <label for="">
                                        {{ ui_change('status', 'property_transaction') }}
                                    </label>
                                    <select name="status" class="form-control select2">

                                        <option value="-1" selected>
                                            {{ ui_change('All_Status', 'property_transaction') }}
                                        </option>
                                        <option value="pending">
                                            {{ ui_change('Pending', 'property_transaction') }}</option>
                                        <option value="completed">{{ ui_change('Completed', 'property_transaction') }}
                                        </option>
                                        <option value="canceled">{{ ui_change('Canceled', 'property_transaction') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-12 col-lg-4 col-xl-3">
                                    <label for="">
                                        {{ ui_change('sign_status', 'property_transaction') }}
                                    </label>
                                    <select name="sign_status" class="form-control select2">

                                        <option value="-1" selected>
                                            {{ ui_change('All_Status', 'property_transaction') }}
                                        </option>
                                        <option value="signed">
                                            {{ ui_change('signed', 'property_transaction') }}</option>
                                        <option value="unsigned">{{ ui_change('unsigned', 'property_transaction') }}
                                        </option>

                                    </select>
                                </div>

                                <div class="col-md-12 col-lg-4 col-xl-3">
                                    <label for="">
                                        {{ ui_change('from', 'property_transaction') }}
                                    </label>
                                    <input type="text" id="from_search_date" name="from" class="form-control ">

                                </div>
                                <div class="col-md-12 col-lg-4 col-xl-3">
                                    <label for="">
                                        {{ ui_change('to', 'property_transaction') }}
                                    </label>
                                    <input type="text" id="to_search_date" name="to" class="form-control ">

                                </div>
                                <div class="col-md-12 col-lg-4 col-xl-3">
                                    <label for="">
                                        {{ ui_change('status', 'property_transaction') }}
                                    </label>
                                    <select class="js-select2-custom form-control" name="booking_status">
                                        <option value="-1">
                                            {{ ui_change('all', 'property_transaction') }}
                                        </option>
                                        <option value="signed">
                                            {{ ui_change('signed', 'property_transaction') }}
                                        </option>
                                        <option value="unsigned">
                                            {{ ui_change('unsigned', 'property_transaction') }}
                                        </option>
                                    </select>

                                </div>
                            </div>
                            <div class="d-flex gap-3 justify-content-end mt-4">
                                <button type="submit" class="btn btn--primary px-4 m-2" name="bulk_action_btn"
                                    value="filter"> {{ ui_change('filter', 'property_transaction') }}</button>
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
                url: "{{ route('agreement.status-update') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(data) {
                    if (data.success == true) {
                        toastr.success(
                            '{{ ui_change('updated_successfully', 'property_transaction') }}');
                    } else if (data.success == false) {
                        toastr.error(
                            '{{ ui_change('Status_updated_failed.', 'property_transaction') }} {{ ui_change('Product_must_be_approved', 'property_transaction') }}'
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
                title: "{{ ui_change('are_you_sure_delete_this', 'property_transaction') }}",
                text: "{{ ui_change('you_will_not_be_able_to_revert_this', 'property_transaction') }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ ui_change('yes_delete_it', 'property_transaction') }}!",
                cancelButtonText: "{{ ui_change('cancel', 'property_transaction') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('agreement.delete') }}",
                        method: 'get',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success(
                                "{{ ui_change('deleted_successfully', 'property_transaction') }}"
                            );
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
