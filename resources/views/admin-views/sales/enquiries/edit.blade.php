@extends('layouts.back-end.app')

@section('title', ui_change('edit_enquiry', 'property_transaction'))
@php
    $lang = Session::get('locale');
    $company = App\Models\Company::where('id', auth()->user()?->company_id)->first() ?? App\Models\User::first();
@endphp
@push('css_or_js')
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #efa520;
            color: white;
        }

        input[type="text"] {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }

        .unit-label {
            font-weight: bold;
            margin-bottom: 10px;
        }


        /* enquiry search details */


        .form-container {
            background-color: #efa520;
            /* background-color: var(--secondary); #2b368f */
            padding: 20px;
            border-radius: 10px;
            max-width: 1200px;
            margin: auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {

            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: none;
        }

        .form-group input[type="date"] {
            padding-right: 30px;
        }

        .trash-icon {
            display: flex;
            justify-content: flex-end;
            margin-top: -10px;
        }

        .trash-icon button {
            background-color: #d9534f;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
        }

        .trash-icon button:hover {
            background-color: #c9302c;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{ ui_change('edit_enquiry', 'property_transaction') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_transaction.inline-menu')
        <input type="hidden" id="decimals" name="decimals" value="{{ $company->decimals }}">

        <!-- Form -->
        <form class="product-form text-start" action="{{ route('enquiry.update', $enquiry->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('patch')
            <!-- general setup -->


            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <h4 class="mb-0">{{ ui_change('general_info', 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('enquiry_no', 'property_transaction') }}</label>
                                <input readonly type="text" name="enquiry_no" class="form-control"
                                    value="{{ $enquiry->enquiry_no }}">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('enquiry_date', 'property_transaction') }}</label>
                                <input type="text" class="form-control" id="enquiry_date_edit" name="enquiry_date"
                                    value="{{ \Carbon\Carbon::parse($enquiry->enquiry_date)->format('d-m-Y') }}"
                                    class="form-control">
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('tenant', 'property_transaction') }}
                                    <button type="button" data-target="#add_tenant" data-add_tenant="" data-toggle="modal"
                                        class="btn btn--primary btn-sm">
                                        <i class="fa fa-plus-square"></i>
                                    </button>
                                </label>
                                <select class="js-select2-custom form-control" id="tenant_id" name="tenant_id" required>
                                    <option selected>{{ ui_change('select', 'property_transaction') }}</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}"
                                            {{ $enquiry->tenant_id == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->name ?? $tenant->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-4 col-xl-6 mt-2">
                            <div class="form-group">
                                <label for="">{{ ui_change('tenant_type', 'property_transaction') }}</label>
                                <input type="text" class="form-control" value="{{ $enquiry->tenant->type }}"
                                    name="tenant_type" readonly class="form-control">
                            </div>
                            @error('tenant_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>
                    <div class="row">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ ui_change('Type', 'property_transaction') }}</th>
                                    <th>{{ ui_change('No._of_unit(s)', 'property_transaction') }}</th>
                                    <th>{{ ui_change('Period_from', 'property_transaction') }}</th>
                                    <th>{{ ui_change('Period_to', 'property_transaction') }}</th>
                                    {{-- <th>Rent Per Month</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                <table>
                                    @php
                                        $formatted_amount = sprintf("%.{$company->decimals}f", 0);
                                    @endphp
                                    {{-- {{ dd($enquiry_unit_details->where('unit_description_id', 4)->first()->rent_amount) }} --}}
                                    @foreach ($unit_descriptions as $unit_desc)
                                        {{-- {{ dd( $enquiry_unit_details->where('unit_description_id', $unit_desc->id)->first()->rent_amount) }} --}}
                                        <tr>
                                            <td class="unit-label">{{ $unit_desc->name }}</td>
                                            <td>
                                                <input type="text" id="no_of_unit-{{ $unit_desc->id }}"
                                                    class="form-control no-of-units" placeholder="No. of unit(s)"
                                                    data-id="{{ $unit_desc->id }}"
                                                    onkeyup="unit_desc_func({{ $unit_desc->id }})"
                                                    name="no_of_unit-{{ $unit_desc->id }}"
                                                    value="{{ $enquiry_unit_details->where('unit_description_id', $unit_desc->id)->count() ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="text" id="date-from-{{ $unit_desc->id }}"
                                                    class="form-control enquiry_unit_date general_period_date"
                                                    onchange="enquiry_unit_date_clc({{ $unit_desc->id }})"
                                                    name="date-from-{{ $unit_desc->id }}" placeholder="DD/MM/YYYY"
                                                    value="{{ isset($enquiry_unit_details->where('unit_description_id', $unit_desc->id)->first()->period_from) ? \Carbon\Carbon::createFromFormat('Y-m-d', $enquiry_unit_details->where('unit_description_id', $unit_desc->id)->first()->period_from)->format('d/m/Y') : '' }}">

                                            </td>
                                            <td>
                                                <input type="text" id="date-to-{{ $unit_desc->id }}"
                                                    class="form-control enquiry_unit_date general_period_date_to    "
                                                    name="date-to-{{ $unit_desc->id }}" placeholder="DD/MM/YYYY"
                                                    value="{{ isset($enquiry_unit_details->where('unit_description_id', $unit_desc->id)->first()->period_to) ? \Carbon\Carbon::createFromFormat('Y-m-d', $enquiry_unit_details->where('unit_description_id', $unit_desc->id)->first()->period_to)->format('d/m/Y') : '' }}">

                                            </td>
                                            {{-- <td>
                                                <input type="number" class="form-control" placeholder="0.000"
                                                    name="rent_amount-{{ $unit_desc->id }}"
                                                    value="{{ isset($enquiry_unit_details->where('unit_description_id', $unit_desc->id)->first()->rent_amount) ? sprintf("%.{$company->decimals}f", $enquiry_unit_details->where('unit_description_id', $unit_desc->id)->first()->rent_amount) : '' }}">
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                </table>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <h4 class="mb-0">{{ ui_change('tenant_details', 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 tenant_form @if ($enquiry->tenant->type == 'individual') d-none @endif company-form"
                            id="company-form">

                            @include('admin-views.property_transactions.enquiries.company_form')

                        </div>
                        <div class="col-md-12 tenant_form @if ($enquiry->tenant->type == 'company') d-none @endif personal-form"
                            id="personal-form">
                            @include('admin-views.property_transactions.enquiries.personal_form')
                        </div>
                    </div>

                </div>

            </div>

            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <h4 class="mb-0">{{ ui_change('enquiry_details', 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('leasing_executive', 'property_transaction') }}
                                </label>
                                <select class="js-select2-custom form-control" name="employee_id">
                                    <option value="-1" selected>
                                        {{ ui_change('not_applicable', 'property_transaction') }}
                                    </option>
                                    @foreach ($employees as $employee_item)
                                        <option value="{{ $employee_item->id }}"
                                            {{ isset($enquiry->enquiry_details->employee) && $enquiry->enquiry_details->employee->id == $employee_item->id ? 'selected' : '' }}>
                                            {{ $employee_item->name ?? $employee_item->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('agent', 'property_transaction') }}
                                </label>
                                <select class="js-select2-custom form-control" name="agent_id">
                                    <option value="-1" selected>
                                        {{ ui_change('not_applicable', 'property_transaction') }}
                                    </option>
                                    @foreach ($agents as $agent_item)
                                        <option value="{{ $agent_item->id }}"
                                            {{ isset($enquiry->enquiry_details->agent) && $enquiry->enquiry_details->agent->id == $agent_item->id ? 'selected' : '' }}>
                                            {{ $agent_item->name ?? $agent_item->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('decision_maker', 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="decision_maker"
                                    value="{{ $enquiry->enquiry_details->decision_maker }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('decision_maker_designation', 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="decision_maker_designation"
                                    value="{{ $enquiry->enquiry_details->decision_maker_designation }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('current_office_location', 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="current_office_location"
                                    value="{{ $enquiry->enquiry_details->current_office_location }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('reason_of_relocation', 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="reason_of_relocation"
                                    value="{{ $enquiry->enquiry_details->reason_of_relocation }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('budget_for_relocation', 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="budget_for_relocation_start"
                                    value="{{ $enquiry->enquiry_details->budget_for_relocation_start }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price" class="title-color"> </label>

                                <input type="text" class="form-control mt-2" name="budget_for_relocation_end"
                                    value="{{ $enquiry->enquiry_details->budget_for_relocation_end }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('no_of_emp_staff_strength', 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="no_of_emp_staff_strength"
                                    value="{{ $enquiry->enquiry_details->no_of_emp_staff_strength }}">
                            </div>
                        </div>

                        {{-- <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('property_transactions.time_frame_for_relocation' , 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="time_frame_for_relocation">
                            </div>
                        </div> --}}

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('time_frame_for_relocation', 'property_transaction') }}</label>
                                <input type="text" name="relocation_date" class="relocation_date form-control"
                                    value="{{ isset($enquiry->enquiry_details->relocation_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', $enquiry->enquiry_details->relocation_date)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}"
                                    placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('enquiry_status', 'property_transaction') }}
                                </label>
                                <select class="js-select2-custom form-control" name="enquiry_status_id" required>

                                    @foreach ($enquiry_statuses as $enquiry_status_item)
                                        <option value="{{ $enquiry_status_item->id }}"
                                            {{ $enquiry->enquiry_details->enquiry_status_id == $enquiry_status_item->id ? 'selected' : '' }}>
                                            {{ $enquiry_status_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('enquiry_status_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('enquiry_status', 'property_transaction') }}
                                </label>
                                <select class="js-select2-custom form-control" name="enquiry_request_status_id" required>

                                    @foreach ($enquiry_request_statuses as $enquiry_request_status_item)
                                        <option value="{{ $enquiry_request_status_item->id }} "
                                            {{ $enquiry->enquiry_details->enquiry_request_status_id == $enquiry_request_status_item->id ? 'selected' : '' }}>
                                            {{ $enquiry_request_status_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('enquiry_request_status_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('period_from_to', 'property_transaction') }}</label>
                                <input type="text" name="period_from" onchange="period_date(),unit_change_main_date()"
                                    value="{{ isset($enquiry->enquiry_details->period_from) ? Carbon\Carbon::parse($enquiry->enquiry_details->period_from)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}"
                                    class="period_from form-control" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 mt-2">
                            <div class="form-group">
                                <label for="price" class="title-color"> </label>
                                <input type="text" name="period_to" class="period_to form-control mt-2"
                                    placeholder="DD/MM/YYYY"
                                    value="{{ isset($enquiry->enquiry_details->period_to) ? Carbon\Carbon::parse($enquiry->enquiry_details->period_to)->format('d/m/Y') : \Carbon\Carbon::now()->addYear()->subDay()->format('d/m/Y') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3 rest-part d-none" id="main_content">

                <div class="card-body mt-3">

                    <div id="units-container">
                        @php
                            $unitCount = count($enquiry_unit_details);
                        @endphp
                        @foreach ($enquiry_unit_details as $enquiry_unit_details_item)
                            <div class="card-header">
                                <div class="d-flex gap-2">
                                    <h4 class="mb-0">{{ ui_change('unit_search_details', 'property_transaction') }}
                                    </h4>
                                </div>
                                @if ($unitCount > 1)
                                    <a type="button" class="btn btn-danger btn-sm" {{-- onclick="deleteItem({{ $enquiry_unit_details_item->id }})" --}}
                                        href="{{ route('enquiry.empty_unit_from_enquiry_unit_search', $enquiry_unit_details_item->id) }}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                @endif
                            </div>


                            <div class="form-container mt-3">
                                <div class="form-row">

                                    <div class="form-group">
                                        <label for="building">{{ ui_change('Building', 'property_transaction') }}</label>
                                        <select id="building" name="property_id_old[]"
                                            class="js-select2-custom form-control">
                                            <option value="">
                                                {{ ui_change('Select_building', 'property_transaction') }}</option>
                                            @foreach ($buildings as $building)
                                                <option value="{{ $building->id }}"
                                                    {{ $enquiry_unit_details_item->property_management_id == $building->id ? 'selected' : '' }}>
                                                    {{ $building->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if (isset($enquiry_unit_details_item->unit_management_id))
                                        <div class="form-group">
                                            <label for="building">{{ ui_change('Unit', 'property_transaction') }}</label>
                                            <select id="building" name="unit_management_id_old[]"
                                                class="js-select2-custom form-control">
                                                <option value="">
                                                    {{ ui_change('Select_Unit', 'property_transaction') }}</option>
                                                @foreach ($unit_management_all as $unit_management_all_item)
                                                    <option value="{{ $unit_management_all_item->id }}"
                                                        {{ $enquiry_unit_details_item->unit_management_id == $unit_management_all_item->id ? 'selected' : '' }}>
                                                        {{ $unit_management_all_item->property_unit_management->name .
                                                            '-' .
                                                            $unit_management_all_item->block_unit_management->block->name .
                                                            '-' .
                                                            $unit_management_all_item->floor_unit_management->floor_management_main->name .
                                                            '-' .
                                                            $unit_management_all_item->unit_management_main->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                        @endif
                        <div class="form-row">
                            <div class="form-group">
                                <label for="building">{{ ui_change('Rent_Amount', 'property_transaction') }}</label>
                                <input type="number"
                                    value="{{ number_format($enquiry_unit_details_item->rent_amount, $company->decimals) ?? 0 }}"
                                    class="from-control " name="rent_amount[]">
                            </div>
                            <div class="form-group">
                                <label
                                    for="unit-description">{{ ui_change('Unit_Description', 'property_transaction') }}</label>
                                <select id="unit-description" name="unit_description_id_old[]"
                                    class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                                    @foreach ($unit_descriptions as $unit_description)
                                        <option value="{{ $unit_description->id }}"
                                            {{ $enquiry_unit_details_item->unit_description_id == $unit_description->id ? 'selected' : '' }}>
                                            {{ $unit_description->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="unit-type">{{ ui_change('Unit_Type', 'property_transaction') }}</label>
                                <select id="unit-type" name="unit_type_id_old[]" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                                    @foreach ($unit_types as $unit_type)
                                        <option value="{{ $unit_type->id }}"
                                            {{ $enquiry_unit_details_item->unit_type_id == $unit_type->id ? 'selected' : '' }}>
                                            {{ $unit_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label
                                    for="unit-condition">{{ ui_change('Unit_Condition', 'property_transaction') }}</label>
                                <select id="unit-condition" name="unit_condition_id_old[]"
                                    class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                                    @foreach ($unit_conditions as $unit_condition)
                                        <option value="{{ $unit_condition->id }}"
                                            {{ $enquiry_unit_details_item->unit_condition_id == $unit_condition->id ? 'selected' : '' }}>
                                            {{ $unit_condition->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="preferred-view">{{ ui_change('Preferred_View', 'property_transaction') }}
                                    <span style="color: red;">*</span></label>
                                <select id="preferred-view" name="view_id_old[]" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                                    @foreach ($views as $view)
                                        <option value="{{ $view->id }}"
                                            {{ $enquiry_unit_details_item->view_id == $view->id ? 'selected' : '' }}>
                                            {{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-row">

                                <div class="form-group">
                                    <label for="period-from">{{ ui_change('Period_From-_To', 'property_transaction') }}
                                        <span style="color: red;">*</span></label>
                                    <div style="display: flex; gap: 10px;">
                                        <input type="text"
                                            value="{{ $enquiry_unit_details_item->period_from ? \Carbon\Carbon::createFromFormat('Y-m-d', $enquiry_unit_details_item->period_from)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}"
                                            onchange="(search_unit_edit_date({{ $enquiry_unit_details_item->id }}))"
                                            name="period_from_old[]"
                                            id="period-from-{{ $enquiry_unit_details_item->id }}"
                                            style="background-color:white;"
                                            class="enquiry_unit_main_date form-control general_period_date period-from-{{ isset($enquiry_unit_details_item->unit_management) ? $enquiry_unit_details_item->unit_management->unit_description_id : $enquiry_unit_details_item->unit_description_id }}">
                                        <input type="text"
                                            value="{{ $enquiry_unit_details_item->period_to ? \Carbon\Carbon::createFromFormat('Y-m-d', $enquiry_unit_details_item->period_to)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}"
                                            name="period_to_old[]" style="background-color:white;" {{-- id="period-to-{{ isset($enquiry_unit_details_item->unit_management) ? $enquiry_unit_details_item->unit_management->unit_description_id : $enquiry_unit_details_item->unit_description_id }}" --}}
                                            id="period-to-{{ $enquiry_unit_details_item->id }}"
                                            class="enquiry_unit_main_date form-control general_period_date_to period-to-{{ isset($enquiry_unit_details_item->unit_management) ? $enquiry_unit_details_item->unit_management->unit_description_id : $enquiry_unit_details_item->unit_description_id }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        for="property-type">{{ ui_change('Property_Type', 'property_transaction') }}</label>
                                    <select id="property-type" name="property_type_old[]"
                                        class="js-select2-custom form-control">
                                        <option>{{ ui_change('Any', 'property_transaction') }}</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="city">{{ ui_change('City', 'property_transaction') }}</label>
                                    <select id="city" name="city_old[]" class="js-select2-custom form-control">
                                        <option>{{ ui_change('Any', 'property_transaction') }}</option>
                                    </select>
                                </div>
                            </div>
                           
                                <div class="form-group">
                                    <label
                                        for="total-area">{{ ui_change('Total_Area_Required', 'property_transaction') }}</label>
                                    <input type="number" class=" form-control" id="total-area" step="0.001"
                                        value="{{ $enquiry_unit_details_item->total_area_required }}"
                                        name="total_area_required_old[]">
                                </div>
                                <div class="form-group">
                                    <label
                                        for="area-measurement">{{ ui_change('Area_Measurement', 'property_transaction') }}</label>
                                    <select id="area-measurement" name="area_measurement_old[]"
                                        class="js-select2-custom form-control">
                                        <option value="">
                                            {{ ui_change('Select_Unit_Type', 'property_transaction') }}</option>
                                        <option value="1"
                                            {{ $enquiry_unit_details_item->area_measurement == 1 ? 'selected' : '' }}>
                                            {{ ui_change('Sq. Mtr.', 'property_transaction') }}</option>
                                        <option value="2"
                                            {{ $enquiry_unit_details_item->area_measurement == 2 ? 'selected' : '' }}>
                                            {{ ui_change('Sq. Ft.', 'property_transaction') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label
                                        for="notes">{{ ui_change('Notes_/_Comments', 'property_transaction') }}</label>
                                    <textarea id="notes" name="comment_old[]" class=" form-control" rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
    </div>
    <div class="row justify-content-end gap-3 mt-3 mx-1">
        <button type="reset" class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
        <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit', 'property_transaction') }}</button>
    </div>



    </form>



    </div>
    <div class="modal fade" id="add_tenant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ ui_change('create_tenant', 'property_transaction') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            <li class="nav-item">
                                <a class="nav-link type_link_create active" href="#"
                                    id="personal-link_create">{{ ui_change('personal', 'property_transaction') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link type_link_create " href="#"
                                    id="company-link_create">{{ ui_change('company', 'property_transaction') }}</a>
                            </li>
                        </ul>
                        <div class="col-md-12 tenant_form_create personal-form_create" id="personal-form_create">
                            <form id="tenantForm_personal" action="{{ route('tenant.store_for_anything') }}"
                                method="post" class="tenantForm">
                                @csrf
                                @method('post')
                                @include('admin-views.property_transactions.tenants.personal_form')
                                <div class="row justify-content-end gap-3 mt-3 mx-1">
                                    <button type="reset"
                                        class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                                    <button type="submit" id="saveTenantPersonal"
                                        class="btn btn--primary px-5 saveTenant">{{ ui_change('submit', 'property_transaction') }}</button>
                                </div>

                            </form>
                        </div>
                        <div class="col-md-12 tenant_form_create d-none company-form_create" id="company-form_create">
                            <form id="tenantForm_company" action="{{ route('tenant.store_for_anything') }}"
                                method="post" class="tenantForm">
                                @csrf
                                @method('post')

                                @include('admin-views.property_transactions.tenants.company_form')
                                <div class="row justify-content-end gap-3 mt-3 mx-1">
                                    <button type="reset"
                                        class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                                    <button type="submit" id="saveTenantCompany"
                                        class="btn btn--primary px-5 saveTenant">{{ ui_change('submit', 'property_transaction') }}</button>
                                </div>

                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (Session::has('error'))
        <script>
            swal("Message", "{{ Session::get('error') }}", 'error', {
                button: true,
                button: "Ok",
                timer: 3000,
            })
        </script>
    @endif
@endsection
@push('script')
    <script>
        function unit_change_main_date() {
            var form_unit_date = $(`input[name=period_from]`).val();
            if (form_unit_date) {
                $(`.general_period_date`).val(form_unit_date)
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;

                    $(`.general_period_date_to`).val(formattedDate)

                }
            }
        }

        flatpickr("#enquiry_date_edit", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ $enquiry->enquiry_date ? Carbon\Carbon::createFromFormat('Y-m-d', $enquiry->enquiry_date)->format('d/m/Y') : '' }}",

        });
        flatpickr(".enquiry_unit_date", {
            dateFormat: "d/m/Y",

        });
        flatpickr(".relocation_date", {
            dateFormat: "d/m/Y",

        });
        flatpickr(".period_from", {
            dateFormat: "d/m/Y",

        });
        flatpickr(".period_to", {
            dateFormat: "d/m/Y",

        });
        flatpickr(".enquiry_unit_main_date", {
            dateFormat: "d/m/Y",
            minDate: "today"
        });
    </script>
    <script>
        function period_date() {
            var form_unit_date = $(`input[name=period_from]`).val();

            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`input[name=period_to]`).val(formattedDate);


                }
            }
        }
    </script>
    <script>
        function enquiry_unit_date_clc(i) {
            var form_unit_date = $(`input[name=date-from-${i}]`).val();
            var unit_from_date = $(`.period-from-${i}`).val(form_unit_date);
            var unit_to_date = $(`.period-to-${i}`);
            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`input[name=date-to-${i}]`).val(formattedDate);
                    unit_to_date.val(formattedDate);

                }
            }
        }
    </script>
    <script>
        function unit_desc_func(idd) {
            var input_val = $(`input[name="no_of_unit-${idd}"]`).val();
            var rent_amount = $(`input[name="rent_amount-${idd}"]`).val();
            document.getElementById('main_content').classList.remove('d-none');
            const container = document.getElementById('units-container');

            $(`.unit-group-${idd}`).remove();

            for (let i = 1; i <= input_val; i++) {
                const unitHtml = `
            <div class="form-container mt-3 unit-group-${idd}">
                <div class="form-row">
                    <div class="form-group">
                        <label for="building">{{ ui_change('Building', 'property_transaction') }}</label>
                        <select id="building" name="property_id[]" class="js-select2-custom form-control">
                            <option value="">{{ ui_change('Select_building', 'property_transaction') }}</option>
                            @foreach ($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="unit-description">{{ ui_change('Unit_Description', 'property_transaction') }}</label>
                        <select id="unit-description" name="unit_description_id[]" class="js-select2-custom form-control">
                            <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                            @foreach ($unit_descriptions as $unit_description)
                                <option value="{{ $unit_description->id }}" ${`{{ $unit_description->id }}` == idd ? 'selected' : ''}>
                                    {{ $unit_description->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="unit-type[]">{{ ui_change('Unit_Type', 'property_transaction') }}</label>
                        <select id="unit-type[]" name="unit_type_id[]" class="js-select2-custom form-control">
                            <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                            @foreach ($unit_types as $unit_type)
                                <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="unit-condition[]">{{ ui_change('Unit_Condition', 'property_transaction') }}</label>
                        <select id="unit-condition[]" name="unit_condition_id[]" class="js-select2-custom form-control">
                            <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                            @foreach ($unit_conditions as $unit_condition)
                                <option value="{{ $unit_condition->id }}">{{ $unit_condition->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="preferred-view[]">{{ ui_change('Preferred_View', 'property_transaction') }}</label>
                        <select id="preferred-view[]" name="view_id[]" class="js-select2-custom form-control">
                            <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                            @foreach ($views as $view)
                                <option value="{{ $view->id }}">{{ $view->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="period-from[]">{{ ui_change('Period_From-_To', 'property_transaction') }}</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" name="period_from_unit_desc[]" onchange="(search_unit_date(${idd}))"   value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" id="enquiry_unit_date_from_search_details-${idd}" class="form-control main_date_for_search">
                            <input type="text" name="period_to_unit_desc[]" value="{{ \Carbon\Carbon::now()->addYear()->subDay()->format('Y-m-d') }}"  id="enquiry_unit_date_to_search_details-${idd}" class="form-control main_date_for_search">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="property-type[]">{{ ui_change('Property_Type', 'property_transaction') }}</label>
                        <select id="property-type[]" name="property_type[]" class="js-select2-custom form-control">
                            <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                            @foreach ($property_types as $property_type)
                                <option value="{{ $property_type->id }}">{{ $property_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city_unit_desc[]">{{ ui_change('City', 'property_transaction') }}</label>
                        <input type="text" id="city_unit_desc[]" name="city_unit_desc[]" class="form-control">
                    </div>

                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="total-area[]">{{ ui_change('Total_Area_Required', 'property_transaction') }}</label>
                        <input type="number" id="total-area[]" name="total_area_required[]" class="form-control" step="0.001" value="0.000">
                    </div>
                    <div class="form-group">
                        <label for="area-measurement[]">{{ ui_change('Area_Measurement', 'property_transaction') }}</label>
                        <select id="area-measurement[]" name="area_measurement[]" class="js-select2-custom form-control">
                            <option>{{ ui_change('Select_Area_Measurement', 'property_transaction') }}</option>
                            <option>{{ ui_change('Sq._Mtr.', 'property_transaction') }}</option>
                            <option>{{ ui_change('Sq._Ft.', 'property_transaction') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes[]">{{ ui_change('Notes_/_Comments', 'property_transaction') }}</label>
                        <textarea id="notes[]" name="comment[]" class="form-control" rows="1"></textarea>
                    </div>
                </div>

            </div>
        `;
                container.insertAdjacentHTML('beforeend', unitHtml);
                flatpickr(`.main_date_for_search`, {
                    dateFormat: "d/m/Y",
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inputField = document.getElementById('total-no-units');
            const container = document.getElementById('units-container');

            // d-none
            document.getElementById('main_content').classList.remove('d-none');
            inputField.addEventListener('input', function() {
                const value = parseInt(inputField.value) || 0;

                // Clear existing units
                container.innerHTML = '';

                for (let i = 0; i < value; i++) {
                    const unitHtml = `
                    <div class="form-container mt-3">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="building">{{ ui_change('Building', 'property_transaction') }}</label>
                                <select id="building" name="property_id-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Select_building', 'property_transaction') }}</option>
                                    @foreach ($buildings as $building)
                                        <option value="{{ $building->id }}">{{ $building->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="unit-description">{{ ui_change('Unit_Description', 'property_transaction') }}</label>
                                <select id="unit-description" name="unit_description_id-${i}"  class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                                    @foreach ($unit_descriptions as $unit_description)
                                        <option value="{{ $unit_description->id }}">{{ $unit_description->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="unit-type">{{ ui_change('Unit_Type', 'property_transaction') }}</label>
                                <select id="unit-type" name="unit_type_id-${i}"  class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                                      @foreach ($unit_types as $unit_type)
                                        <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="unit-condition">{{ ui_change('Unit_Condition', 'property_transaction') }}</label>
                                <select id="unit-condition" name="unit_condition_id-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                                    @foreach ($unit_conditions as $unit_condition)
                                        <option value="{{ $unit_condition->id }}" >{{ $unit_condition->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="preferred-view">{{ ui_change('Preferred_View', 'property_transaction') }} <span style="color: red;">*</span></label>
                                <select id="preferred-view" name="view_id-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Any', 'property_transaction') }}</option>
                                    @foreach ($views as $view)
                                        <option value="{{ $view->id }}" >{{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="period-from">{{ ui_change('Period_From-_To', 'property_transaction') }} <span style="color: red;">*</span></label>
                                <div style="display: flex; gap: 10px;"  >
                                    <input type="date" style="background-color:white;" value="{{ \Carbon\Carbon::today() }}" name="period_from-${i}"  id="period-from" class=" form- general_period_date">
                                    <input type="date" style="background-color:white;" value="{{ \Carbon\Carbon::today()->addYear() }}" name="period_to-${i}"  id="period-to" class=" form-control general_period_date_to">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="property-type">{{ ui_change('Property_Type', 'property_transaction') }}</label>
                                <select id="property-type"  class="js-select2-custom form-control">
                                    <option>Any</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city"  >{{ ui_change('City', 'property_transaction') }}</label>
                                <select id="city" class="js-select2-custom form-control">
                                    <option>{{ ui_change('Any', 'property_transaction') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="total-area">{{ ui_change('Total_Area_Required', 'property_transaction') }}</label>
                                <input type="number"  class=" form-control" id="total-area" step="0.001" value="0.000">
                            </div>
                            <div class="form-group">
                                <label for="area-measurement">{{ ui_change('Area_Measurement', 'property_transaction') }}</label>
                                <select id="area-measurement"  class="js-select2-custom form-control"   >
                                    <option>{{ ui_change('Select_Unit_Type', 'property_transaction') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="notes">{{ ui_change('Notes_/_Comments', 'property_transaction') }}</label>
                                <textarea id="notes"  class=" form-control" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                `;
                    container.insertAdjacentHTML('beforeend', unitHtml);
                }
            });
        });

        function search_unit_edit_date(i) {
            var form_unit_date = $(`#period-from-${i}`).val();

            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`#period-to-${i}`).val(formattedDate);


                }
            }
        }

        function search_unit_date(i) {
            var form_unit_date = $(`#enquiry_unit_date_from_search_details-${i}`).val();

            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`#enquiry_unit_date_to_search_details-${i}`).val(formattedDate);


                }
            }
        }
    </script>
    <script>
        flatpickr(`.enquiry_unit_date`, {
            dateFormat: "d/m/Y",
        });
        flatpickr(`.enquiry_unit_date`, {
            dateFormat: "d/m/Y",
        });
    </script>
    <script>
        $('select[name=tenant_id]').on('change', function() {
            var tenant_id = $(this).val();
            if (tenant_id) {
                $.ajax({
                    url: "{{ URL::to('enquiry/get_tenant') }}/" + tenant_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            $('input[name="tenant_type"]').empty();
                            $('input[name="tenant_type"]').val(data.type);
                            if (data.type == 'company') {
                                $("#company-form").removeClass('d-none').addClass('active');
                                $("#personal-form").removeClass('active').addClass('d-none');
                                $("#docs_company_id").addClass('d-none');
                            } else {
                                $("#personal-form").removeClass('d-none').addClass('active');
                                $("#company-form").removeClass('active').addClass('d-none');
                                $("#docs_personal_id").addClass('d-none');
                            }
                            $('input[name="name"]').empty();
                            $('input[name="name"]').empty();
                            $('input[name="name"]').val(data.name);
                            $('input[name="id_number"]').empty();
                            $('input[name="id_number"]').val(data.id_number);
                            $('input[name="nick_name"]').empty();
                            $('input[name="nick_name"]').val(data.nick_name);
                            $('input[name="contact_person"]').empty();
                            $('input[name="contact_person"]').val(data.contact_person);
                            $('input[name="designation"]').empty();
                            $('input[name="designation"]').val(data.designation);
                            $('input[name="contact_no"]').empty();
                            $('input[name="contact_no"]').val(data.contact_no);
                            $('input[name="whatsapp_no"]').empty();
                            $('input[name="whatsapp_no"]').val(data.whatsapp_no);
                            $('input[name="company_name"]').empty();
                            $('input[name="company_name"]').val(data.company_name);
                            $('input[name="fax_no"]').empty();
                            $('input[name="fax_no"]').val(data.fax_no);
                            $('input[name="telephone_no"]').empty();
                            $('input[name="telephone_no"]').val(data.telephone_no);
                            $('input[name="other_contact_no"]').empty();
                            $('input[name="other_contact_no"]').val(data.other_contact_no);
                            $('input[name="address1"]').empty();
                            $('input[name="address1"]').val(data.address1);
                            $('input[name="address2"]').empty();
                            $('input[name="address2"]').val(data.address2);
                            $('input[name="address3"]').empty();
                            $('input[name="address3"]').val(data.address3);
                            $('input[name="city"]').empty();
                            $('input[name="city"]').val(data.city);
                            $('input[name="state"]').empty();
                            $('input[name="state"]').val(data.state);
                            $('input[name="passport_no"]').empty();
                            $('input[name="passport_no"]').val(data.passport_no);
                            $('input[name="email1"]').empty();
                            $('input[name="email1"]').val(data.email1);
                            $('input[name="email2"]').empty();
                            $('input[name="email2"]').val(data.email2);
                            $('input[name="company_name"]').empty();
                            $('input[name="company_name"]').val(data.company_name);
                            $('input[name="registration_no"]').empty();
                            $('input[name="registration_no"]').val(data.registration_no);
                            $('input[name="group_company_name"]').empty();
                            $('input[name="group_company_name"]').val(data.group_company_name);

                            var genderValue = data.gender;
                            var live_with_id_value = data.live_with_id;
                            var nationality_id_value = data.nationality_id;
                            var country_id_value = data.country_id;
                            var business_activity_id_value = data.business_activity_id;
                            if ($('select[name="gender"] option[value="' + genderValue + '"]')
                                .length === 0) {
                                $('select[name="gender"]').append('<option value="' + genderValue +
                                    '">' + genderValue + '</option>');
                            }
                            $('select[name="gender"]').val(genderValue).change();
                            if ($('select[name="live_with_id"] option[value="' +
                                    live_with_id_value + '"]')
                                .length === 0) {
                                $('select[name="live_with_id"]').append('<option value="' +
                                    live_with_id_value +
                                    '">' + live_with_id_value + '</option>');
                            }
                            $('select[name="live_with_id"]').val(live_with_id_value).change();

                            if ($('select[name="country_id"] option[value="' +
                                    country_id_value + '"]')
                                .length === 0) {
                                $('select[name="country_id"]').append('<option value="' +
                                    country_id_value +
                                    '">' + country_id_value + '</option>');
                            }
                            $('select[name="country_id"]').val(country_id_value).change();

                            if ($('select[name="nationality_id"] option[value="' +
                                    nationality_id_value + '"]')
                                .length === 0) {
                                $('select[name="nationality_id"]').append('<option value="' +
                                    nationality_id_value +
                                    '">' + nationality_id_value + '</option>');
                            }
                            $('select[name="nationality_id"]').val(nationality_id_value).change();

                            if ($('select[name="business_activity_id"] option[value="' +
                                    business_activity_id_value + '"]')
                                .length === 0) {
                                $('select[name="business_activity_id"]').append('<option value="' +
                                    business_activity_id_value +
                                    '">' + business_activity_id_value + '</option>');
                            }
                            $('select[name="business_activity_id"]').val(business_activity_id_value)
                                .change();


                        } else {}
                    },
                    error: function(xhr, status, error) {
                        console.error('Error occurred:', error);
                    }
                });
            }
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all inputs for number of units
            const unitInputs = document.querySelectorAll('.no-of-units');

            unitInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const unitId = this.dataset.id; // Get the unit ID from the data attribute
                    const dateFromInput = document.getElementById(`date-from-${unitId}`);
                    const dateToInput = document.getElementById(`date-to-${unitId}`);

                    const today = new Date();
                    const nextYear = new Date(today);
                    nextYear.setFullYear(today.getFullYear() + 1);
                    nextYear.setDate(nextYear.getDate() - 1);

                    const formatDate = (date) => {
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return `${day}/${month}/${year}`;
                    };

                    dateFromInput.value = formatDate(today);
                    dateToInput.value = formatDate(nextYear);
                });
            });
        });
    </script>
    <script>
        function deleteItem(itemId) {

            $.ajax({
                url: "{{ route('enquiry.empty_unit_from_enquiry_unit', ':id') }}".replace(':id', itemId),
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        document.getElementById("item-" + itemId).remove();
                    }
                    Swal.fire({
                        icon: 'success',
                        title: "{{ __('general.deleted_successfully') }}",
                        showConfirmButton: true,
                        timer: 300
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error occurred:", error);

                }
            });
        }
    </script>
@endpush
