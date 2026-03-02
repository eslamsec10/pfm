@extends('layouts.back-end.app')
@php
    $lang = Session::get('locale');
@endphp
@section('title', __('collections.groups'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <style>
        /* ====== RESET BOOTSTRAP ACCORDION ====== */
        .accordion-item {
            border: none !important;
            background: transparent !important;
            margin-bottom: 6px;
        }

        .accordion-header {
            margin: 0;
        }

        /* ====== BOX (EACH ITEM SEPARATE) ====== */
        .box {
            background: #ffffff !important;
            border: 1px solid #e0e0e0 !important;
            border-radius: 4px;
            box-shadow: none !important;
            overflow: hidden;
        }

        /* ====== ACCORDION BUTTON ====== */
        .accordion-button {
            padding: 10px 14px !important;
            font-size: 15px;
            font-weight: 600;
            color: #0033ff !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        /* remove bootstrap arrow */
        .accordion-button::after {
            display: none !important;
        }

        /* ====== + / - ICON ====== */
        /* .accordion-button::before {
                                content: "+";
                                font-weight: bold;
                                color: #000;
                                margin-right: 8px;
                            } */

        /* .accordion-button:not(.collapsed)::before {
                                content: "-";
                            } */

        /* ====== SMALL TEXT (CODES, IDS) ====== */
        .accordion-button span {
            /* font-weight: normal;
                                font-size: 13px; */
        }

        .accordion-button span:first-of-type {
            color: #ff00aa;
        }

        /* ====== LINKS ====== */
        .accordion-button a {
            color: #3399ff;
            font-size: 13px;
            text-decoration: none;
        }

        .accordion-button a:hover {
            text-decoration: underline;
        }

        /* ====== ACCORDION BODY ====== */
        .accordion-body {
            padding: 8px 14px;
            border-top: 1px solid #e0e0e0;
        }

        /* ====== NESTED ACCORDION (LEVELS) ====== */
        .accordion .accordion {
            margin-left: 25px;
        }

        /* ====== LEDGERS / LIST ITEMS ====== */
        .accordion-body ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .accordion-body li {
            margin: 6px 0;
        }

        .accordion-body li h4 {
            margin: 0;
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
            background: #ffffff;
            font-size: 14px;
        }

        .accordion-body li a {
            color: #0033ff;
            font-weight: 600;
            text-decoration: none;
        }

        .accordion-body li a:hover {
            text-decoration: underline;
        }

        .box:hover {
            border-color: #0033ff;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <h2 class="h1 mb-0 d-flex gap-2 align-items-center">

                {{ __('collections.chart_of_account') }}
            </h2>

        </div>

        @include('admin-views.inline_menu.accounts_master.inline-menu')

        <div class="accordion" id="accordionExample">
            @foreach ($groups as $group)
                @if ($group->ledgers->isNotEmpty() || $group->sub_groups->isNotEmpty())
                    @php $groupId = 'group-' . $group->id; @endphp
                    <div class="accordion-item box">
                        <h4 class="accordion-header" id="heading-{{ $groupId }}">
                            <a href="{{ route('groups.show', $group->id) }}" class="accordion-button collapsed"
                                type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $groupId }}"
                                aria-expanded="false" aria-controls="collapse-{{ $groupId }}">+ {{ $group->name }}
                                <span style="color: gray;"> ({{ str_pad($group->code, 3, '0', STR_PAD_LEFT) }}) | <a
                                        href="{{ route('groups.show', $group->id) }}">{{ ui_change('show_group') }}</a>
                                    | <a
                                        href="{{ route('groups.edit', $group->id) }}">{{ ui_change('edit_group') }}</a></span>
                                | <a class=" mr-2" data-add_new_group="" data-toggle="modal"
                                    data-group_id="{{ $group->id }}"
                                    data-target="#add_new_group">{{ ui_change('add_group') }}</a></span>
                            </a>
                        </h4>
                        <div id="collapse-{{ $groupId }}" class="accordion-collapse collapse"
                            aria-labelledby="heading-{{ $groupId }}" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                @if ($group->ledgers->isNotEmpty())
                                    <ul>
                                        @foreach ($group->ledgers as $ledger)
                                            <li>
                                                <h4><a href="{{ route('ledgers.show', $ledger->id) }}">{{ $ledger->name }}
                                                        <span style="color: red;">
                                                            ({{ str_pad($ledger->code, 3, '0', STR_PAD_LEFT) }})
                                                            | <a
                                                                href="{{ route('ledgers.show', $ledger->id) }}">{{ __('collections.show_ledger') }}</a>
                                                            | <a
                                                                href="{{ route('ledgers.edit', $ledger->id) }}">{{ __('collections.edit_ledger') }}</a></span></a>
                                                </h4>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @foreach ($group->sub_groups as $sub_group)
                                    @php $subGroupId = 'subgroup-' . $sub_group->id; @endphp
                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="heading-{{ $subGroupId }}">
                                            <a href="{{ route('groups.show', $sub_group->id) }}"
                                                class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse-{{ $subGroupId }}" aria-expanded="false"
                                                aria-controls="collapse-{{ $subGroupId }}">&nbsp;&nbsp;&nbsp;+
                                                {{ $sub_group->name }} <span style="color: gray;">
                                                    ({{ str_pad($sub_group->code, 3, '0', STR_PAD_LEFT) }})
                                                    | <a
                                                        href="{{ route('groups.show', $sub_group->id) }}">{{ __('collections.show_group') }}</a>
                                                    | <a
                                                        href="{{ route('groups.edit', $sub_group->id) }}">{{ __('collections.edit_group') }}</a>
                                                    | <a class=" mr-2" data-add_new_group="" data-toggle="modal"
                                                        data-group_id="{{ $sub_group->id }}"
                                                        data-target="#add_new_group">{{ ui_change('add_group') }}</a>
                                                </span></a>
                                        </h4>
                                        <div id="collapse-{{ $subGroupId }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading-{{ $subGroupId }}">
                                            <div class="accordion-body">

                                                @if ($sub_group->ledgers->isNotEmpty())
                                                    <ul>
                                                        @foreach ($sub_group->ledgers as $ledger)
                                                            <li>
                                                                <h4><a href="{{ route('ledgers.show', $ledger->id) }}">{{ $ledger->name }}
                                                                        <span style="color: red;">
                                                                            ({{ str_pad($ledger->code, 3, '0', STR_PAD_LEFT) }})
                                                                            | <a
                                                                                href="{{ route('ledgers.show', $ledger->id) }}">{{ __('collections.show_ledger') }}</a>
                                                                            | <a
                                                                                href="{{ route('ledgers.edit', $ledger->id) }}">{{ __('collections.edit_ledger') }}</a></span></a>
                                                                </h4>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                                @foreach ($sub_group->sub_groups as $sub_sub_group)
                                                    @php $subGroupId = 'subgroup-' . $sub_sub_group->id; @endphp
                                                    <div class="accordion-item">
                                                        <h4 class="accordion-header" id="heading-{{ $subGroupId }}">
                                                            <a class="accordion-button collapsed"
                                                                href="{{ route('groups.show', $sub_sub_group->id) }}"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapse-{{ $subGroupId }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse-{{ $subGroupId }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+
                                                                {{ $sub_sub_group->name }} <span style="color: gray;">
                                                                    ({{ str_pad($sub_sub_group->code, 3, '0', STR_PAD_LEFT) }})
                                                                    | <a
                                                                        href="{{ route('groups.show', $sub_sub_group->id) }}">{{ __('collections.show_group') }}</a>
                                                                    | <a
                                                                        href="{{ route('groups.edit', $sub_sub_group->id) }}">{{ __('collections.edit_group') }}</a>

                                                                    | <a class=" mr-2" data-add_new_group=""
                                                                        data-toggle="modal"
                                                                        data-group_id="{{ $sub_sub_group->id }}"
                                                                        data-target="#add_new_group">{{ ui_change('add_group') }}</a>
                                                                </span></a>
                                                        </h4>
                                                        <div id="collapse-{{ $subGroupId }}"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="heading-{{ $subGroupId }}">
                                                            <div class="accordion-body">
                                                                @if ($sub_sub_group->ledgers->isNotEmpty())
                                                                    <ul>
                                                                        @foreach ($sub_sub_group->ledgers as $ledger)
                                                                            <li>
                                                                                <h4><a
                                                                                        href="{{ route('ledgers.show', $ledger->id) }}">
                                                                                        {{ $ledger->name }} <span
                                                                                            style="color: red;">
                                                                                            {{--  ({{ str_pad($ledger->code, 3, '0', STR_PAD_LEFT) }}) --}} <a
                                                                                                href="{{ route('ledgers.show', $ledger->id) }}">
                                                                                                |
                                                                                                {{ __('collections.show_ledger') }}</a>
                                                                                            | <a
                                                                                                href="{{ route('ledgers.edit', $ledger->id) }}">{{ __('collections.edit_ledger') }}</a></span></a>
                                                                                </h4>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                @else
                    <div class="row mt-2 accordion-item ">
                        <div class="col-lg-12 box pt-2">
                            <h4><a href="{{ route('groups.show', $group->id) }}" class=""
                                    type="button">{{ $group->name }} <span style="color: gray;">
                                        ({{ str_pad($group->code, 3, '0', STR_PAD_LEFT) }}) | <a
                                            href="{{ route('groups.show', $group->id) }}">{{ __('collections.show_group') }}</a>
                                        | <a
                                            href="{{ route('groups.edit', $group->id) }}">{{ __('collections.edit_group') }}</a>
                                        | <a class=" mr-2" data-add_new_group="" data-toggle="modal"
                                            data-group_id="{{ $group->id }}"
                                            data-target="#add_new_group">{{ ui_change('add_group') }}</a>


                                    </span>
                                </a></h4>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>





    </div>

    <div class="modal fade " id="add_new_group" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('collections.add_new_group') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('groups.store') }}" method="post">
                    @csrf
                    <div class="modal-body" style="text-align: {{ $lang === 'ar' ? 'right' : 'left' }};">
                        <div class="row">
                            <div class="col-md-12 col-lg-4 col-xl-12">
                                <div class="form-group">
                                    <label for="">{{ __('roles.name') }} <span class="text-danger">
                                            *</span></label>
                                    <input type="text" name="name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-12">
                                <div class="form-group">
                                    <label for="">{{ __('property_master.code') }} <span class="text-danger">
                                            *</span></label>
                                    <input type="text" name="code" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-12">
                                <div class="form-group">
                                    <label for="">{{ __('collections.display_name') }} <span class="text-danger">
                                            *</span></label>
                                    <input type="text" name="display_name" class="form-control">
                                </div>
                            </div>


                            <div class="col-md-12 col-lg-4 col-xl-12">
                                <div class="form-group">
                                    <label for="">{{ __('collections.under_group') }} <span class="text-danger">
                                            *</span></label>
                                    <select name="group_id" class="form-control js-select2-custom ">
                                        <option value="0">{{ __('collections.leave_as_parent') }}</option>
                                        @foreach ($all_groups as $all_groups_item)
                                            <option value="{{ $all_groups_item->id }}">{{ $all_groups_item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-12">
                                <div class="form-group">
                                    <label for="">{{ __('collections.nature') }} <span class="text-danger">
                                            *</span></label>
                                    <input type="text" name="nature" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-4 col-xl-12">
                                <div class="form-group form-check">
                                    <input class="form-check-input" type="checkbox" name="tax_applicable"
                                        value="1">
                                    <label class="form-check-label"
                                        for="tax_applicable">{{ __('collections.tax_applicable') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2 d-none" id="tax_applicable_info">
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="form-group">
                                    <label for="vat_applicable_from"
                                        class="title-color">{{ __('collections.applicable_from') }}</label>
                                    <input type="text" class="form-control" name="vat_applicable_from"
                                        id="vat_applicable_from">
                                </div>
                                @error('vat_applicable_from')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="form-group">
                                    <label for="is_taxable" class="title-color">{{ __('collections.tax_type') }}
                                    </label>
                                    <select class="js-select2-custom form-control" name="is_taxable">
                                        <option value="0" {{ $company->tax_rate == 0 ? 'selected' : '' }}>
                                            {{ __('companies.zero_rated') }}</option>
                                        <option value="1" {{ $company->tax_rate > 0 ? 'selected' : '' }}>
                                            {{ __('collections.taxability') }}</option>
                                        <option value="2">{{ __('companies.exempted') }}</option>
                                        <option value="3">{{ __('companies.non_taxable') }}</option>
                                    </select>
                                </div>
                                @error('is_taxable')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="form-group">
                                    <label for="tax_rate" class="title-color">{{ __('companies.tax_rate') }}</label>
                                    <input type="number" class="form-control" name="tax_rate"
                                        value="{{ $company->tax_rate }}" readonly>
                                </div>
                                @error('tax_rate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12 col-lg-4 col-xl-12">
                                <label for="">{{ __('roles.status') }}</label>
                                <div class="form-group">
                                    <input type="radio" name="main_status" checked value="active">
                                    <label for="name" class="title-color">{{ __('general.active') }}
                                    </label>
                                    <input type="radio" name="main_status" value="inactive"
                                        class="{{ $lang == 'ar' ? 'mr-3' : 'ml-3' }}">
                                    <label for="name" class="title-color">{{ __('general.inactive') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-12">
                                <label for="">{{ __('collections.enable_auto_code') }}</label>
                                <div class="form-group">
                                    <input type="radio" name="status" value="yes">
                                    <label for="name" class="title-color">{{ __('general.yes') }}
                                    </label>
                                    <input type="radio" name="status" value="no"
                                        class="{{ $lang == 'ar' ? 'mr-3' : 'ml-3' }}" checked>
                                    <label for="name" class="title-color">{{ __('general.no') }}
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="row d-none" id="prefix_group">
                            <div class="col-md-12 col-lg-4 col-xl-4">
                                <div class="form-group ">
                                    <label for="">{{ __('property_reports.prefix') }}</label>
                                    <input type="text" name="prefix" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-4">
                                <div class="form-group ">
                                    <label for="">{{ __('collections.start_number') }}</label>
                                    <input type="number" name="start_number" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-4">
                                <div class="form-group ">
                                    <label for="">{{ __('collections.prefix_with_zero') }}</label>
                                    {{-- <input type="text" name="prefix_with_zero" class="form-control"> --}}
                                    <select name="prefix_with_zero" class="form-control">
                                        <option value="yes">{{ __('general.yes') }}</option>
                                        <option value="no" selected>{{ __('general.no') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-4">
                                <div class="form-group ">
                                    <label for="">{{ __('collections.total_digit') }}</label>
                                    <input type="number" name="total_digit" class="form-control">

                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-4">
                                <div class="form-group ">
                                    <label for="">{{ __('general.result') }}</label>
                                    <input type="text" readonly name="result" class="form-control">

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('general.cancel') }}</button>
                        <button type="submit" class="btn btn--primary">{{ __('general.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#add_new_group').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget);
            let groupId = button.data('group_id');

            let modal = $(this);
            modal.find('select[name="group_id"]').val(groupId).trigger('change');

        });
    </script>
    <script>
        flatpickr("#vat_applicable_from", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
        });
    </script>
    <script>
        $('select[name="group_id"]').on('change', function() {
            var group_id = $(this).val();

            if (group_id) {
                $.ajax({
                    url: "{{ route('get_group_by_id', ':id') }}".replace(':id', group_id),
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        var tax_applicable_info = $('#tax_applicable_info');
                        var tax_rate_input = $('input[name="tax_rate"]');
                        var is_taxable = $('select[name="is_taxable"]');
                        var vat_applicable_from = $('input[name="vat_applicable_from"]');
                        if (data.group.tax_applicable == 1) {
                            $('input[name="tax_applicable"]').prop('checked', true);
                            tax_applicable_info.removeClass('d-none');
                            tax_rate_input.empty();
                            tax_rate_input.val(data.group.tax_rate);

                            if (data.group.tax_rate > 0) {
                                tax_rate_input.removeAttr('readonly')
                            }
                            is_taxable.val(data.group.is_taxable).change();
                            vat_applicable_from.empty();
                            vat_applicable_from.val(data.date);
                        } else {
                            $('input[name="tax_applicable"]').prop('checked', false);
                            tax_rate_input.empty();
                            tax_rate_input.attr('disabled', 'disabled').val('0');
                            tax_applicable_info.addClass('d-none');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error occurred:', error);
                    }
                });
            }

        });
    </script>
    <script>
        $(document).ready(function() {
            $('input[name="tax_applicable"]').on('change', function() {
                var tax_applicable_info = $('#tax_applicable_info');

                if ($(this).is(':checked')) {
                    tax_applicable_info.removeClass('d-none');
                } else {
                    tax_applicable_info.addClass('d-none');
                }
            });
            $('input[name="name"]').on('keyup', function() {
                var name = $(this).val();
                $('input[name="display_name"]').val(name);

            });
        });
    </script>
    <script>
        $('select[name="is_taxable"]').on('change', function() {
            var is_taxable = $(this).val();
            var $tax_rate_input = $('input[name="tax_rate"]');

            if (is_taxable == '2' || is_taxable == '1') {
                $tax_rate_input.removeAttr('readonly').val('0');
            } else if (is_taxable == '0' || is_taxable == '3') {
                $tax_rate_input.attr('disabled', 'disabled').val('0');
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('input[name="prefix"]').on('keyup', function() {
                let prefix = $(this).val();
                let prefix_with_zero = $('select[name="prefix_with_zero"]').val();
                let total_digit = $('input[name="total_digit"]').val();
                let start_number = $('input[name="start_number"]').val();
                if (prefix_with_zero == 'yes') {
                    let paddedNumber = start_number.toString().padStart(total_digit, '0');

                    let result = $('input[name="result"]').val(prefix + paddedNumber);

                } else {
                    let paddedNumber = start_number.toString().padStart(0, '0');
                    let result = $('input[name="result"]').val(prefix + paddedNumber);
                }
            });
            $('input[name="start_number"]').on('keyup', function() {
                let start_number = $(this).val();
                let prefix_with_zero = $('select[name="prefix_with_zero"]').val();
                let total_digit = $('input[name="total_digit"]').val();
                let prefix = $('input[name="prefix"]').val();
                if (prefix_with_zero == 'yes') {
                    let paddedNumber = start_number.toString().padStart(total_digit, '0');

                    let result = $('input[name="result"]').val(prefix + paddedNumber);

                } else {
                    let paddedNumber = start_number.toString().padStart(0, '0');
                    let result = $('input[name="result"]').val(prefix + paddedNumber);
                }
            });
            $('input[name="total_digit"]').on('keyup', function() {
                let total_digit = $(this).val();
                let prefix_with_zero = $('select[name="prefix_with_zero"]').val();
                let start_number = $('input[name="start_number"]').val();
                let prefix = $('input[name="prefix"]').val();
                if (prefix_with_zero == 'yes') {
                    let paddedNumber = start_number.toString().padStart(total_digit, '0');

                    let result = $('input[name="result"]').val(prefix + paddedNumber);

                } else {
                    let paddedNumber = start_number.toString().padStart(0, '0');
                    let result = $('input[name="result"]').val(prefix + paddedNumber);
                }
            });
            $('select[name="prefix_with_zero"]').on('change', function() {
                let prefix_with_zero = $(this).val();
                let total_digit = $('input[name="total_digit"]').val();
                let start_number = $('input[name="start_number"]').val();
                let prefix = $('input[name="prefix"]').val();
                if (prefix_with_zero == 'yes') {
                    let paddedNumber = start_number.toString().padStart(total_digit, '0');

                    let result = $('input[name="result"]').val(prefix + paddedNumber);

                } else {
                    let paddedNumber = start_number.toString().padStart(0, '0');
                    let result = $('input[name="result"]').val(prefix + paddedNumber);
                }
            });

            $('input[name="status"]').on('change', function() {
                var status = $(this).val();
                var prefix_group = $('#prefix_group');

                if (status == 'yes') {
                    prefix_group.removeClass('d-none');

                } else if (status == 'no') {
                    prefix_group.addClass('d-none');
                }
            });
        });
    </script>
@endpush
