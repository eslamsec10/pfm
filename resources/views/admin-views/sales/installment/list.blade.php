@extends('layouts.back-end.app')

@section('title', ui_change('installments', 'property_transaction'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{ ui_change('installments', 'property_transaction') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.sales.inline-menu')

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">


                    <div class="table-responsive">
                        <table id="datatable" style="text-align: {{ Session::get('locale') === 'ar' ? 'right' : 'left' }};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th><input id="bulk_check_all" class="bulk_check_all" type="checkbox" />
                                        {{ ui_change('sl', 'property_transaction') }}</th>
                                     
                                    <th class="text-center">{{ ui_change('due_date', 'property_transaction') }}
                                    </th>
                                    <th class="text-center">{{ ui_change('amount', 'property_transaction') }}</th>
                                    <th class="text-center">{{ ui_change('unit', 'property_transaction') }}
                                    </th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($installments as $k => $installments_item)
                                    <tr>
                                         <th scope="row"><input class="check_bulk_item" name="bulk_ids[]"
                                                    type="checkbox" value="{{ $installments_item->id }}" />
                                                {{ $loop->index + 1 }}</th>

                                            <td class="text-center">
                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d' , $installments_item->due_date )->format('d/m/Y') ?? ui_change('not_available', 'property_transaction') }}
                                            </td>
                                            <td class="text-center">
                                                {{  number_format($installments_item->amount,$company->decimals)  ?? ui_change('not_available', 'property_transaction') }}
                                            </td>
                                            <td class="text-center">
                                                {{  $installments_item->unit_management?->property_unit_management?->name.'-'.
                                                    $installments_item->unit_management?->block_unit_management?->block?->name.'-'.
                                                    $installments_item->unit_management?->floor_unit_management?->floor_management_main?->name.'-'.
                                                    $installments_item->unit_management?->unit_management_main?->name    }}
                                            </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>




    </div>




@endsection

@push('script')
@endpush
