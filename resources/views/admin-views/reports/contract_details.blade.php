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
        @include('admin-views.inline_menu.property_reports.inline-menu')
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

                                                @if ($agreement_item->booking_status == 'signed')
                                                    <a class="btn btn-outline-warning text-black "
                                                        href="{{ route('agreement.schedule', [$agreement_item->id]) }}">
                                                        {{ ui_change('Rent_List', 'property_transaction') }}
                                                    </a>
                                                @endif

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

@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
@endpush
