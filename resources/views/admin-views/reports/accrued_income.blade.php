@extends('layouts.back-end.app')

@section('title', ui_change('accrued_income_report'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{ ui_change('accrued_income_report') }}
             </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_reports.inline-menu')

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            {{-- <div class="col-lg-4">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ ui_change('search_by_name') }}" aria-label="Search orders"
                                            value="{{ request('search') }}">
                                        <input type="hidden" value="{{ request('status') }}" name="status">
                                        <button type="submit" class="btn btn--primary">{{ ui_change('search') }}</button>
                                    </div>
                                </form>

                            </div>
                            <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                                <button type="button" data-target="#add_tenant" data-add_tenant="" data-toggle="modal"
                                    class="btn btn--primary btn-sm">
                                    <i class="fas fa-filter"></i>
                                </button>


                            </div> --}}
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
                                            {{ ui_change('sl') }}</th>
                                        <th class="text-center">{{ ui_change('Accrued_Income_ledger') }}</th> 
                                        <th class="text-center">{{ ui_change('Income_ledger') }}</th> 
                                        <th class="text-center">{{ ui_change('Voucher_Date') }}</th> 
                                        <th class="text-center">{{ ui_change('Applicable_Date') }}</th> 
                                        <th class="text-center">{{ ui_change('Receivable_Upto') }}</th> 
                                        <th class="text-center">{{ ui_change('Accrued_Amt') }}</th> 
                                        <th class="text-center">{{ ui_change('Received_Amt') }}</th> 
                                        <th class="text-center">{{ ui_change('Balance_Amt') }}</th> 
                                        <th class="text-center">{{ ui_change('Balance_for') }}</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($agreements as $agreements_item)  
                                        <tr>
                                            <th scope="row"><input class="check_bulk_item" name="bulk_ids[]"
                                                    type="checkbox" value="" />
                                                 {{ $loop->index + 1 }}  
                                            </th>
                                            <td class="text-center">
                                                 
                                            </td> 
                                        </tr>
                               @endforeach --}}

                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive mt-4">
                            <div class="px-4 d-flex justify-content-lg-end">
                                <!-- Pagination -->
                                {{-- {{ $agreements->links() }} --}}
                            </div>
                        </div>
                    </form>
                    {{-- @if (count($agreements) == 0) --}}
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ asset(main_path() . 'back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ ui_change('no_data_to_show') }}</p>
                        </div>
                    {{-- @endif --}}
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
@endpush
