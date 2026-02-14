@extends('layouts.back-end.app')

@section('title', ui_change('sale_price_list'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{ ui_change('sale_price_list') }}
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $sales_price_list->total() }}</span>
            </h2>
        </div>
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
                                            placeholder="{{ ui_change('search_by_name') }}" aria-label="Search orders"
                                            value="{{ request('search') }}">
                                        <input type="hidden" value="{{ request('status') }}" name="status">
                                        <button type="submit" class="btn btn--primary">{{ ui_change('search') }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                            <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                                <a href="{{ route('sales_price.create') }}" class="btn btn--primary">
                                    <i class="tio-add"></i>
                                    <span class="text">{{ ui_change('create_sale') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <form action="" method="get">
                        <div class="px-3 py-4">
                            <div class="row align-items-center"  >
                                <div class="col-12 d-flex justify-content-end">
                                     <button type="submit" name="bulk_action_btn" value="delete"
                                    class="btn btn--primary">
                                    <i class="tio-delete"></i> {{ ui_change('delete','property_report')  }}
                                </button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable"
                                style="text-align: {{ Session::get('locale') === 'ar' ? 'right' : 'left' }};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th><input id="bulk_check_all" class="bulk_check_all"
                                                type="checkbox" />{{ ui_change('sl') }}</th>
                                        <th class="text-center">{{ ui_change('unit') }}</th>
                                        <th class="text-center">{{ ui_change('sale_price') }}</th>
                                        <th class="text-center">{{ ui_change('applicable_from') }}</th>
                                        <th class="text-center">{{ ui_change('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales_price_list as $k => $sale_price_list_item)
                                        <tr>
                                            <th scope="row"><input class="check_bulk_item" name="bulk_ids[]"
                                                    type="checkbox" value="{{ $sale_price_list_item->id }}" />
                                                {{ $sales_price_list->firstItem() + $k }}
                                            </th>

                                            <td class="text-center">
                                                {{ optional($sale_price_list_item->unit_management->property_unit_management)->name .
                                                    '-' .
                                                    optional($sale_price_list_item->unit_management->block_unit_management)->block->name .
                                                    '-' .
                                                    optional($sale_price_list_item->unit_management->floor_unit_management)->floor_management_main->name .
                                                    '-' .
                                                    optional($sale_price_list_item->unit_management)->unit_management_main->name ??
                                                    ui_change('not_available') }}
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($sale_price_list_item->price, 3) ?? ui_change('not_available') }}
                                            </td>
                                            <td class="text-center">
                                                {{ optional($sale_price_list_item)->applicable_date ?? ui_change('not_available') }}
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">

                                                    <a class="btn btn-outline--primary btn-sm square-btn"
                                                        title="{{ ui_change('edit') }}"
                                                        href="{{ route('sales_price.edit', [$sale_price_list_item->id]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                    <a class="btn btn-outline-danger btn-sm delete square-btn"
                                                        title="{{ ui_change('delete') }}"
                                                        id="{{ $sale_price_list_item->id }}">
                                                        <i class="tio-delete"></i>
                                                    </a>
                                                </div>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{ $sales_price_list->links() }}
                        </div>
                    </div>

                    @if (count($sales_price_list) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ asset(main_path() . 'assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ ui_change('no_data_to_show') }}</p>
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
                title: "{{ ui_change('are_you_sure_delete_this') }}",
                text: "{{ ui_change('you_will_not_be_able_to_revert_this') }}!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ ui_change('yes_delete_it') }}!',
                cancelButtonText: '{{ ui_change('cancel') }}',
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
                        url: "{{ route('sales_price.delete') }}",
                        method: 'get',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success('{{ ui_change('deleted_successfully') }}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
