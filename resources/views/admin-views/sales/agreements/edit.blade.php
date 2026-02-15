@extends('layouts.back-end.app')

@section('title', ui_change('edit_agreement' , 'property_transaction') )
@php
    $lang = Session::get('locale');
@endphp
@push('css_or_js')
    <link href="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('public/assets/back-end/css/croppie.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2"> 
                {{ ui_change('edit_agreement' , 'property_transaction')  }}
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_transaction.inline-menu')

        <!-- Form -->
        <form class="product-form text-start" action="{{ route('agreement.update', $agreement->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('patch')
            <!-- general setup -->


            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2"> 
                        <h4 class="mb-0">{{ ui_change('general_info' , 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('agreement_no' , 'property_transaction') }}</label>
                                <input readonly type="text" name="agreement_no" class="form-control"
                                    value="{{ $agreement->agreement_no }}">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('agreement_date' , 'property_transaction')  }}</label>
                                <input type="text" class="form-control" id="agreement_date" name="agreement_date"
                                    class="form-control"
                                    value="{{ \Carbon\Carbon::parse($agreement->agreement_date)->format('d-m-Y') }}">
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('tenant' , 'property_transaction')  }}
                                </label>
                                <select class="js-select2-custom form-control" id="tenant_id" name="tenant_id" required>
                                    <option>{{ ui_change('select' , 'property_transaction') }}</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}"
                                            {{ $tenant->id == $agreement->tenant_id ? 'selected' : '' }}>
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


                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ ui_change('tenant_type' , 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="tenant_type" readonly class="form-control"
                                    value="{{ $agreement->tenant->type }}">
                            </div>
                            @error('tenant_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ ui_change('total_no_of_required_units' , 'property_transaction') }}</label>
                                <input type="number" id="total-no-units" class="form-control"
                                    name="total_no_of_required_units" value="{{ $agreement->total_no_of_required_units }}">
                            </div>
                            @error('total_no_of_required_units')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>


                </div>

            </div>
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2"> 
                        <h4 class="mb-0">{{ ui_change('tenant_details' , 'property_transaction')  }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 tenant_form  @if ($agreement->tenant->type == 'individual') d-none @endif  company-form"
                            id="company-form">

                            @include('admin-views.property_transactions.agreements.company_form')

                        </div>
                        <div class="col-md-12 tenant_form @if ($agreement->tenant->type == 'company') d-none @endif personal-form "
                            id="personal-form">
                            @include('admin-views.property_transactions.agreements.personal_form')
                        </div>
                    </div>

                </div>

            </div>

            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2"> 
                        <h4 class="mb-0">{{ ui_change('agreement_details' , 'property_transaction')  }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('leasing_executive' , 'property_transaction') }}
                                </label>
                                <select class="js-select2-custom form-control" name="employee_id">
                                    <option value="">{{ ui_change('not_applicable' , 'property_transaction') }}</option>
                                    @foreach ($employees as $employee_item)
                                        <option value="{{ $employee_item->id }}"
                                            {{ $employee_item->id == $agreement_details->employee_id ? 'selected' : '' }}>
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
                                <label for="name" class="title-color">{{ ui_change('agent' , 'property_transaction')  }}
                                </label>
                                <select class="js-select2-custom form-control" name="agent_id">
                                    <option value="">{{ ui_change('not_applicable' , 'property_transaction')  }}</option>
                                    @foreach ($agents as $agent_item)
                                        <option value="{{ $agent_item->id }}"
                                            {{ $agent_item->id == $agreement_details->agent_id ? 'selected' : '' }}>
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
                                    class="title-color">{{ ui_change('decision_maker' , 'property_transaction')  }}</label>
                                <input type="text" class="form-control" name="decision_maker" value="{{ $agreement_details->decision_maker }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('decision_maker_designation' , 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="decision_maker_designation" value="{{ $agreement_details->decision_maker_designation }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('current_office_location' , 'property_transaction')  }}</label>
                                <input type="text" class="form-control" name="current_office_location" value="{{ $agreement_details->current_office_location }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('reason_of_relocation' , 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="reason_of_relocation" value="{{ $agreement_details->reason_of_relocation }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('budget_for_relocation' , 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="budget_for_relocation_start" value="{{ $agreement_details->budget_for_relocation_start }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price" class="title-color"> </label>

                                <input type="text" class="form-control mt-2" name="budget_for_relocation_end" value="{{ $agreement_details->budget_for_relocation_end }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('no_of_emp_staff_strength' , 'property_transaction')  }}</label>
                                <input type="text" class="form-control" name="no_of_emp_staff_strength" value="{{ $agreement_details->no_of_emp_staff_strength }}">
                            </div>
                        </div>

                         
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('time_frame_for_relocation' , 'property_transaction') }}</label>
                                <input type="text" name="relocation_date" class="relocation_date form-control"
                                    placeholder="DD/MM/YYYY" value="{{ (isset($agreement_details->relocation_date) ) ? \Carbon\Carbon::createFromFormat('Y-m-d',$agreement_details->relocation_date )->format('d/m/Y') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('enquiry_status' , 'property_transaction')  }}
                                </label>
                                <select class="js-select2-custom form-control" name="agreement_status_id" required>

                                    @foreach ($enquiry_statuses as $enquiry_status_item)
                                        <option value="{{ $enquiry_status_item->id }}"
                                            {{ $enquiry_status_item->id == $agreement_details->agreement_status_id ? 'selected' : '' }}>
                                            {{ $enquiry_status_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agreement_status_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('enquiry_status' , 'property_transaction') }}
                                </label>
                                <select class="js-select2-custom form-control" name="agreement_request_status_id"
                                    required>

                                    @foreach ($enquiry_request_statuses as $enquiry_request_status_item)
                                        <option value="{{ $enquiry_request_status_item->id }}"
                                            {{ $enquiry_request_status_item->id == $agreement_details->agreement_request_status_id ? 'selected' : '' }}>
                                            {{ $enquiry_request_status_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agreement_request_status_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="price"
                                    class="title-color">{{ ui_change('period_from_to' , 'property_transaction') }}</label>
                                <input type="text" name="period_from"
                                    class="period_from form-control period_from_date" value="{{ (isset($agreement_details->period_from) ) ? \Carbon\Carbon::createFromFormat('Y-m-d',$agreement_details->period_from )->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="price" class="title-color"> </label>
                                <input type="text" name="period_to" class="period_to form-control period_to_date mt-2"
                                    placeholder="DD/MM/YYYY"  value="{{ (isset($agreement_details->period_to) ) ? \Carbon\Carbon::createFromFormat('Y-m-d',$agreement_details->period_to )->format('d/m/Y') : \Carbon\Carbon::now()->addYear()->subDay(  )->format('d/m/Y') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="main-content">
                @foreach ($agreement_units as $item)
                    <div class="card mt-3 rest-part" id="main_content" style="background-color: #2b368f;color:white">
                        <div class="card-header">
                            <div class="d-flex gap-2"> 
                                <h4 class="mb-0">{{ ui_change('unit_search_details' , 'property_transaction') }}</h4>
                            </div>
                        </div>
                        <div class="card-body mt-3">
                            <div class="form-container mt-3">
                                <div class="form-row">
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label for="building">{{ ui_change('property' , 'property_transaction') }}</label>
                                            <select id="building" name="property_id-{{ $item->id }}"
                                                onchange="unitFunc({{ $item->id }})"
                                                class="js-select2-custom form-control">
                                                <option value="0">{{ ui_change('select' , 'property_transaction')  }}</option>
                                                @foreach ($buildings as $building)
                                                    <option value="{{ $building->id }}"
                                                        {{ $building->id == $item->property_id ? 'selected' : '' }}>
                                                        {{ $building->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="unit-description">{{ ui_change('unit_description' , 'property_transaction') }}</label>
                                            <select id="unit-description" name="unit_description_id-{{ $item->id }}"
                                                class="js-select2-custom form-control"
                                                onchange="unitFunc({{ $item->id }})">
                                                <option value="0">{{ ui_change('any' , 'property_transaction')  }}</option>
                                                @foreach ($unit_descriptions as $unit_description)
                                                    <option value="{{ $unit_description->id }}"
                                                        {{ $unit_description->id == $item->unit_description_id ? 'selected' : '' }}>
                                                        {{ $unit_description->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label for="unit-type">{{ ui_change('unit_type' , 'property_transaction') }}</label>
                                            <select id="unit-type" name="unit_type_id-{{ $item->id }}"
                                                class="js-select2-custom form-control"
                                                onchange="unitFunc({{ $item->id }})">
                                                <option value="0">{{ ui_change('any' , 'property_transaction')  }}</option>
                                                @foreach ($unit_types as $unit_type)
                                                    <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="unit-condition">{{ ui_change('unit_condition' , 'property_transaction')  }}</label>
                                            <select id="unit-condition" name="unit_condition_id-{{ $item->id }}"
                                                class="js-select2-custom form-control"
                                                onchange="unitFunc({{ $item->id }})">
                                                <option value="">{{ ui_change('any' , 'property_transaction')  }}</option>
                                                @foreach ($unit_conditions as $unit_condition)
                                                    <option value="{{ $unit_condition->id }}">{{ $unit_condition->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="preferred-view">{{ ui_change('preferred_view' , 'property_transaction')  }}</label>
                                            <select id="preferred-view" name="view_id-{{ $item->id }}"
                                                onchange="unitFunc({{ $item->id }})"
                                                class="js-select2-custom form-control">
                                                <option value="">{{ ui_change('any' , 'property_transaction') }}</option>
                                                @foreach ($views as $view)
                                                    <option value="{{ $view->id }}">{{ $view->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label for="property-type">{{ ui_change('property_type' , 'property_transaction') }}</label>
                                            <select id="property-type" name="property_type-{{ $item->id }}"
                                                class="js-select2-custom form-control"
                                                onchange="unitFunc({{ $item->id }})">
                                                <option value="">{{ ui_change('any' , 'property_transaction')  }}</option>
                                                @foreach ($property_types as $property_type)
                                                    <option value="{{ $property_type->id }}">{{ $property_type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label
                                                for="period-from">{{ ui_change('period_from_to' , 'property_transaction')  }}</label>
                                            <div style="display: flex; gap: 10px;">
                                                <input type="text" name="period_from-{{ $item->id }}"
                                                    onchange="agreement_unit_date_clc({{ $item->id }})"
                                                    id="period_from_date"
                                                    class="form-control main_data period_from_date text-white"
                                                    value="{{ isset($item->commencement_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', $item->commencement_date)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label for="price" class="title-color"> </label>
                                            <div style="display: flex; gap: 10px;">
                                                <input type="text" name="period_to-{{ $item->id }}"
                                                    id="period_to_date"
                                                    class="form-control mt-2 main_data period_to_date text-white"
                                                    value="{{ isset($item->expiry_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', $item->expiry_date)->format('d/m/Y') : \Carbon\Carbon::now()->subDay()->addYear()->format('d/m/Y') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label for="city">{{ ui_change('city' , 'property_transaction') }}</label>
                                            <input type="text" id="city" name="city_id[{{ $item->id }}]"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('total_area_required' , 'property_transaction') }}</label>
                                            <input type="number" id="total-area" name="total_area-{{ $item->id }}"
                                                class="form-control" step="0.001" placeholder="0.000">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="area-measurement">{{ ui_change('area_measurement' , 'property_transaction')  }}</label>
                                            <select id="area-measurement" name="area_measurement-{{ $item->id }}"
                                                class="js-select2-custom form-control">
                                                <option>{{ ui_change('select_area_measurement' , 'property_transaction') }}</option>
                                                <option>{{ ui_change('Sq._Mtr.' , 'property_transaction') }}</option>
                                                <option>{{ ui_change('Sq._Ft.' , 'property_transaction') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6 col-lg-4 col-xl-12">
                                        <div class="form-group">
                                            <label
                                                for="notes">{{ ui_change('notes_&_comments' , 'property_transaction') }}</label>
                                            <textarea id="notes" name="notes-{{ $item->id }}" class="form-control" rows="2"> </textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                @php
                                    $unit = App\Models\UnitManagement::where('id', $item->unit_id)
                                        ->with(
                                            'property_unit_management',
                                            'block_unit_management',
                                            'block_unit_management.block',
                                            'floor_unit_management.floor_management_main',
                                            'floor_unit_management',
                                            'unit_management_main',
                                        )
                                        ->first();
                                    $unit_services = App\Models\AgreementUnitsService::where(
                                        'agreement_unit_id',
                                        $item->id,
                                    )->get();
                                @endphp
                                <div class="form-row">
                                    <div class="col-md-6 col-lg-4 col-xl-6">

                                        <div class="form-group">
                                            <label for="area-measurement">{{ ui_change('unit' , 'property_transaction')  }}</label>
                                            <select id="area-measurement" name="unit-{{ $item->id }}"
                                                class="js-select2-custom form-control">
                                                <option value="{{ $item->unit_id }}">
                                                    {{ $unit->property_unit_management->name .
                                                        '-' .
                                                        $unit->block_unit_management->block->name .
                                                        '-' .
                                                        $unit->floor_unit_management->floor_management_main->name .
                                                        '-' .
                                                        $unit->unit_management_main->name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="area-measurement">{{ ui_change('payment_mode' , 'property_transaction')  }}</label>
                                            <select id="area-measurement" name="payment_mode-{{ $item->id }}"
                                                onchange="payment_mode_func({{ $item->id }})"
                                                class="js-select2-custom form-control" required >
                                                <option value="">
                                                    {{ ui_change('select_payment_mode' , 'property_transaction') }}
                                                </option>
                                                <option value="1" {{ 1 == $item->payment_mode ? 'selected' : '' }}>
                                                    {{ ui_change('daily' , 'property_transaction')  }}</option>
                                                <option value="2" {{ 2 == $item->payment_mode ? 'selected' : '' }}>
                                                    {{ ui_change('monthly' , 'property_transaction')  }}</option>
                                                <option value="3" {{ 3 == $item->payment_mode ? 'selected' : '' }}>
                                                    {{ ui_change('bi_monthly' , 'property_transaction')  }}
                                                </option>
                                                <option value="4" {{ 4 == $item->payment_mode ? 'selected' : '' }}>
                                                    {{ ui_change('quarterly' , 'property_transaction')  }}
                                                </option>
                                                <option value="5" {{ 5 == $item->payment_mode ? 'selected' : '' }}>
                                                    {{ ui_change('half_yearly' , 'property_transaction')  }}
                                                </option>
                                                <option value="6" {{ 6 == $item->payment_mode ? 'selected' : '' }}>
                                                    {{ ui_change('yearly' , 'property_transaction')  }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label for="area-measurement">{{ ui_change('pdc' , 'property_transaction')  }}</label>
                                            <select id="area-measurement" name="pdc-{{ $item->id }}"
                                                class="js-select2-custom form-control">
                                                <option value="yes" {{ ('yes' == $item->pdc) ? 'selected' : '' }}>{{ ui_change('yes' , 'property_transaction') }}</option>
                                                <option value="no" {{ ('no' == $item->pdc )? 'selected' : '' }}>{{ ui_change('no' , 'property_transaction') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label
                                                for="area-measurement">{{ ui_change('area_measurement' , 'property_transaction')  }}</label>
                                            <select id="area-measurement" disabled
                                                name="area_measurement-{{ $item->id }}"
                                                class="js-select2-custom form-control">
                                                <option>{{ ui_change('Sq._Mtr.' , 'property_transaction') }}</option>
                                                <option>{{ ui_change('Sq._Ft.' , 'property_transaction') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label for="total-area">{{ ui_change('total_area' , 'property_transaction')  }}</label>
                                            <input type="number" disabled name="total_area_amount-{{ $item->id }}"
                                                class="form-control" placeholder="0.000"
                                                value="{{ $item->total_area_amount }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label for="total-area">{{ ui_change('amount' , 'property_transaction') }}</label>
                                            <input type="number" disabled name="amount-{{ $item->id }}"
                                                class="form-control" onkeyup="rent_mode_amount({{ $item->id }})"
                                                value="{{ $item->amount }}" step="0.001" placeholder="0.000">
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label for="total-area">{{ ui_change('rent_amount' , 'property_transaction') }}</label>
                                            <input type="number" name="rent_amount-{{ $item->id }}"
                                                class="form-control"
                                                onkeyup="rent_mode_amount({{ $item->id }}) , vat_amount_func({{ $item->id }})"
                                                step="0.001" placeholder="0.000" value="{{ $item->rent_amount }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label
                                                for="area-measurement">{{ ui_change('rent_mode' , 'property_transaction') }}</label>
                                            <select id="area-measurement" name="rent_mode-{{ $item->id }}"
                                                class="js-select2-custom form-control" required >
                                                <option value="" {{ 0 == $item->rent_mode ? 'selected' : '' }}>
                                                    {{ ui_change('select_rent_mode' , 'property_transaction') }} </option>
                                                <option value="1" {{ 1 == $item->rent_mode ? 'selected' : '' }}>
                                                    {{ ui_change('daily' , 'property_transaction') }}</option>
                                                <option value="2" {{ 2 == $item->rent_mode ? 'selected' : '' }}>
                                                    {{ ui_change('monthly' , 'property_transaction')  }}</option>
                                                <option value="3" {{ 3 == $item->rent_mode ? 'selected' : '' }}>
                                                    {{ ui_change('bi_monthly' , 'property_transaction') }}
                                                </option>
                                                <option value="4" {{ 4 == $item->rent_mode ? 'selected' : '' }}>
                                                    {{ ui_change('quarterly' , 'property_transaction') }}
                                                </option>
                                                <option value="5" {{ 5 == $item->rent_mode ? 'selected' : '' }}>
                                                    {{ ui_change('half_yearly' , 'property_transaction')  }}
                                                </option>
                                                <option value="6" {{ 6 == $item->rent_mode ? 'selected' : '' }}>
                                                    {{ ui_change('yearly' , 'property_transaction') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label
                                                for="area-measurement">{{ ui_change('rental_gl' , 'property_transaction') }}</label>
                                            <select id="area-measurement" name="rental_gl-{{ $item->id }}"
                                                class="js-select2-custom form-control"
                                                onchange="vat_amount_func({{ $item->id }})">
                                                <option value="0">{{ ui_change('select_rental_gl' , 'property_transaction')  }}
                                                </option>
                                                <option value="0" {{ 0 == $item->rental_gl ? 'selected' : '' }}>
                                                    {{ ui_change('Rental_Income_0%' , 'property_transaction') }}</option>
                                                <option value="10" {{ 10 == $item->rental_gl ? 'selected' : '' }}>
                                                    {{ ui_change('Rental_Income_10%' , 'property_transaction') }}</option>
                                                <option value="20" {{ 20 == $item->rental_gl ? 'selected' : '' }}>
                                                    {{ ui_change('Rental_Income_20%' , 'property_transaction') }}</option>
                                                <option value="30" {{ 30 == $item->rental_gl ? 'selected' : '' }}>
                                                    {{ ui_change('Rental_Income_30%' , 'property_transaction') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('vat_percentage' , 'property_transaction')  }}</label>
                                            <input type="number" readonly name="vat_percentage-{{ $item->id }}"
                                                class="form-control" step="0.001" placeholder="0.000"
                                                value="{{ $item->vat_percentage }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label for="total-area">{{ ui_change('vat_amount' , 'property_transaction') }}</label>
                                            <input type="number" readonly name="vat_amount-{{ $item->id }}"
                                                class="form-control" step="0.001" placeholder="0.000"
                                                value="{{ $item->vat_amount }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-12">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('total_net_rent_amount' , 'property_transaction') }}</label>
                                            <input type="number" readonly
                                                name="total_net_rent_amount-{{ $item->id }}"
                                                value="{{ $item->total_net_rent_amount }}" class="form-control text-white"
                                                step="0.001" placeholder="0.000">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-12">
                                        <div class="form-group">
                                            <button type="button" class="btn btn--primary form-control"
                                                onclick="add_service({{ $item->id }})">{{ ui_change('add_other_services' , 'property_transaction')  }}</button>
                                        </div>
                                    </div>
                                    <div class="card-body" id="main_service_content-{{ $item->id }}">
                                        <input type="hidden" id="old_service_counter-{{ $item->id }}"
                                            name="old_service_counter[{{ $item->id }}]"
                                            value="{{ $unit_services->count() }}">
                                        @foreach ($unit_services as $key => $service_item)
                                            

                                            <div class="row mt-1 bg-warning  border rounded p-2 position-relative "
                                            
                                                id="service-{{ $item->id . '-' .  ($key + 1) }}">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute"
                                                style="top: 5px; right: 5px; z-index: 10;"
                                                data-id="{{ $service_item->id }}" onclick="deleteItemService(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="form-group">
                                                        <label for="charge_mode-{{ $item->id . '-' .  ($key + 1) }}"
                                                            class="form-control-label">{{ ui_change('Charge_Mode' , 'property_transaction') }}</label> <span
                                                            class="starColor">*</span>
                                                        <select
                                                            name="charge_mode-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control"
                                                            onchange="charge_type({{ $item->id }} , {{  ($key + 1) }}) , amount_charge_func({{ $item->id }} , {{  ($key + 1) }}) , percentage_amount_charge_func({{ $item->id }} , {{  ($key + 1) }})">
                                                            @foreach ($services_master as $service_master_item)
                                                            <option value="{{ $service_master_item->id }}"
                                                                {{ $service_master_item->id == $service_item->other_charge_type ? 'selected' : '' }}>
                                                                {{ $service_master_item->name }}</option>
                                                        @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="form-group">
                                                        <label
                                                            for="charge_mode_type-{{ $item->id . '-' .  ($key + 1) }}"
                                                            class="form-control-label">{{ ui_change('Charge_Mode' , 'property_transaction') }}</label> <span
                                                            class="starColor">*</span>
                                                        <select
                                                            name="charge_mode_type-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control"
                                                            onchange="service_value_calc({{ $item->id }} , {{  ($key + 1) }})">
                                                            <option value="amount"
                                                                {{ $service_item->charge_mode == 'amount' ? 'selected' : '' }}>
                                                                {{ ui_change('amount' , 'property_transaction')  }}</option>
                                                            <option value="percentage"
                                                                {{ $service_item->charge_mode == 'percentage' ? 'selected' : '' }}>
                                                                {{ ui_change('percentage' , 'property_transaction')  }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4 col-xl-3"
                                                    id="amount_charge-{{ $item->id . '-' .  ($key + 1) }}">
                                                    <div class="form-group">
                                                        <label
                                                            for="total-area">{{ ui_change('amount' , 'property_transaction') }}</label>
                                                        <input type="number"
                                                            onkeyup="amount_charge_func({{ $item->id }} , {{  ($key + 1) }})"
                                                            name="amount_charge-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control" step="0.001" placeholder="0.000"
                                                            value="{{ $service_item->amount }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4 col-xl-3 d-none"
                                                    id="percentage_amount_charge-{{ $item->id . '-' .  ($key + 1) }}">
                                                    <div class="form-group ">
                                                        <label
                                                            for="total-area">{{ ui_change('percentage' , 'property_transaction') }}</label>
                                                        <input type="number"
                                                            onkeyup="percentage_amount_charge_func({{ $item->id }} , {{  ($key + 1) }})"
                                                            name="percentage_amount_charge-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control" step="0.001" placeholder="0.000">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="form-group">
                                                        <label
                                                            for="total-area">{{ ui_change('calculate_amount' , 'property_transaction') }}</label>
                                                        <input type="number" readonly id="total-area"
                                                            value="{{ $service_item->amount }}"
                                                            name="calculate_amount-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control  bg-white" step="0.001"
                                                            placeholder="0.000">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="form-group">
                                                        <label
                                                            for="total-area">{{ ui_change('start_date' , 'property_transaction') }}</label>
                                                        <input type="text"
                                                            name="start_date-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control main_date" onchange="service_date({{ $item->id }}, {{  ($key + 1) }})"
                                                            value="{{ Carbon\Carbon::today()->format('d/m/Y') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="form-group">
                                                        <label
                                                            for="total-area">{{ ui_change('expaired_date' , 'property_transaction') }}</label>
                                                        <input type="text"
                                                            name="expiry_date-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control main_date"
                                                            value="{{ Carbon\Carbon::today()->addYear()->subDay()->format('d/m/Y') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="form-group">
                                                        <label
                                                            for="total-area">{{ ui_change('vat_percentage' , 'property_transaction') }}</label>
                                                        <input type="number" readonly
                                                            name="vat_percentage-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control  bg-white" step="0.001"
                                                            placeholder="0.000">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="form-group">
                                                        <label
                                                            for="total-area">{{ ui_change('vat_amount' , 'property_transaction') }}</label>
                                                        <input type="number" readonly value="{{ $service_item->vat }}"
                                                            name="vat_amount-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control  bg-white" step="0.001"
                                                            placeholder="0.000">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4 col-xl-12">
                                                    <div class="form-group">
                                                        <label
                                                            for="total-area">{{ ui_change('total_amount' , 'property_transaction') }}</label>
                                                        <input type="number" readonly id="total-area"
                                                            value="{{ $service_item->total }}"
                                                            name="total_amount-{{ $item->id . '-' .  ($key + 1) }}[]"
                                                            class="form-control  bg-white" step="0.001"
                                                            placeholder="0.000">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="form-group">
                                                <label
                                                    for="total-area">{{ ui_change('security_deposit_months_rent' , 'property_transaction') }}</label>
                                                <input type="number"
                                                    name="security_deposit_months_rent-{{ $item->id }}" onkeyup="deposite({{ $item->id }})"
                                                    class="form-control" step="0.001" placeholder="0.000" value="{{ $item->security_deposit }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="form-group">
                                                <label
                                                    for="total-area">{{ ui_change('security_deposit_amount' , 'property_transaction') }}</label>
                                                <input type="number" name="security_deposit_amount-{{ $item->id }}"
                                                    class="form-control" step="0.001" placeholder="0.000" value="{{ $item->security_deposit_amount }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="form-group">
                                                <label
                                                    for="area-measurement">{{ ui_change('is_rent_inclusive_of_ewa' , 'property_transaction')   }}</label>
                                                <select name="is_rent_inclusive_of_ewa-{{ $item->id }}"
                                                    class="js-select2-custom form-control">
                                                    <option value="yes" {{ ($item->is_rent_inclusive_of_ewa == 'yes' ) ? 'selected' : '' }}>{{ ui_change('yes' , 'property_transaction') }}</option>
                                                    <option value="no" {{ ($item->is_rent_inclusive_of_ewa == 'no' ) ? 'selected' : '' }}>{{ ui_change('no' , 'property_transaction') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="form-group">
                                                <label
                                                    for="area-measurement">{{ ui_change('ewa_limit_mode' , 'property_transaction')  }}</label>
                                                <select name="ewa_limit_mode-{{ $item->id }}"
                                                    class="js-select2-custom form-control">
                                                    <option  value="monthly" {{ ($item->ewa_limit_mode == 'monthly' ) ? 'selected' : '' }}>{{ ui_change('monthly' , 'property_transaction')  }}</option>
                                                    <option  value="yearly" {{ ($item->ewa_limit_mode == 'yearly' ) ? 'selected' : '' }}>{{ ui_change('yearly' , 'property_transaction')  }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="form-group">
                                                <label
                                                    for="total-area">{{ ui_change('ewa_limit_monthly' , 'property_transaction') }}</label>
                                                <input type="number" name="ewa_limit_monthly-{{ $item->id }}"
                                                    class="form-control" step="0.001" value="0.000" value="{{ $item->ewa_limit_monthly }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-4 col-xl-3">
                                            <div class="form-group">
                                                <label
                                                    for="">{{ ui_change('lease_break_date' , 'property_transaction')  }}</label>
                                                <input type="text" class="form-control main_date text-white"
                                                    name="lease_break_date-{{ $item->id }}" value="{{  isset($item->lease_break_date) ? \Carbon\Carbon::createFromFormat('Y-m-d' , $item->lease_break_date)->format('d/m/Y') : '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="form-group">
                                                <label
                                                    for="area-measurement">{{ ui_change('notice_period' , 'property_transaction')  }}</label>
                                                <select name="notice_period-{{ $item->id }}"
                                                    class="js-select2-custom form-control">
                                                    <option>{{ ui_change('one_month' , 'property_transaction')  }}</option>
                                                    <option>{{ ui_change('two_month' , 'property_transaction')  }}</option>
                                                    <option>{{ ui_change('three_month' , 'property_transaction') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-6">
                                            <div class="form-group">
                                                <label
                                                    for="total-area">{{ ui_change('lease_break_comments' , 'property_transaction') }}</label>
                                                <input type="text" name="lease_break_comments-{{ $item->id }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        `;
                    </div>
                @endforeach

                <div class="row justify-content-end gap-3 mt-3 mx-1">
                    <button type="reset" class="btn btn-secondary px-5">{{ ui_change('reset' , 'property_transaction')  }}</button>
                    <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit' , 'property_transaction') }}</button>
                </div>
        </form>



    </div>
@endsection

@push('script')
<script>
     const counters = {};

function add_service(i) {
    const container = document.getElementById('main_service_content-' + i);
    const charge = document.getElementById('charge_mode-' + i);
    var form_unit_date = $(`input[name=period_from-${i}]`).val();
    var to_unit_date = $(`input[name=period_to-${i}]`).val();
    let oldCounterElement = document.getElementById('old_service_counter-' + i);
    let last_count = oldCounterElement ? parseInt(oldCounterElement.value) || 0 : 0;
    console.log(last_count)
    if (!counters[i]) {
        counters[i] = last_count;
    }
    counters[i]++;


    let counterInput = document.getElementById('service_counter-' + i);
    if (!counterInput) {
        // const old_value = counters[i] + last_count; 
        const formContainer = document.createElement('div');
        formContainer.innerHTML = `
        <input type="hidden" id="service_counter-${i}" name="service_counter[${i}]" value="${counters[i]}">
        `;
        container.appendChild(formContainer);
        counterInput = document.getElementById('service_counter-' + i);
    } else {

        counterInput.value = counters[i];
    }
    const bladeContent = `
    <div class="row mt-1 bg-warning  border rounded p-2  position-relative" id="service-${i}-${counters[i]}">
         <button type="button" class="btn btn-danger btn-sm position-absolute " 
            style="top: 5px; right: 5px; z-index: 10;" 
            onclick="removeService(this)">
            <i class="fas fa-trash"></i>
        </button>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="charge_mode-${i}-${counters[i]}" class="form-control-label">{{ ui_change('Charge_Mode' , 'property_transaction') }}</label> <span class="starColor">*</span>
                <select name="charge_mode-${i}-${counters[i]}[]" class="form-control"
                    onchange="charge_type(${i},${counters[i]}) , amount_charge_func(${i},${counters[i]}) , percentage_amount_charge_func(${i},${counters[i]})">
                    <option value="0">{{ ui_change('Select_Other_Charge_Type' , 'property_transaction') }}</option>
                    @foreach ($services_master as $service_master_item)
                        <option value="{{ $service_master_item->id }}">{{ $service_master_item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="charge_mode_type-${i}-${counters[i]}" class="form-control-label">{{ ui_change('Charge_Mode' , 'property_transaction') }}</label> <span
                    class="starColor">*</span>
                <select name="charge_mode_type-${i}-${counters[i]}[]" class="form-control"
                    onchange="service_value_calc(${i},${counters[i]})">
                    <option value="amount">{{ ui_change('amount' , 'property_transaction')  }}</option>
                    <option value="percentage">{{ ui_change('percentage' , 'property_transaction') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3" id="amount_charge-${i}-${counters[i]}">
            <div class="form-group">
                <label for="total-area">{{ ui_change('amount' , 'property_transaction') }}</label>
                <input type="number" onkeyup="amount_charge_func(${i},${counters[i]})"
                    name="amount_charge-${i}-${counters[i]}[]" class="form-control" step="0.001" placeholder="0.000">
            </div>
        </div>
       
        <div class="col-md-6 col-lg-4 col-xl-3 d-none" id="percentage_amount_charge-${i}-${counters[i]}">
            <div class="form-group ">
                <label for="total-area">{{ ui_change('percentage' , 'property_transaction') }}</label>
                <input type="number" onkeyup="percentage_amount_charge_func(${i},${counters[i]})"
                    name="percentage_amount_charge-${i}-${counters[i]}[]" class="form-control" step="0.001"
                    placeholder="0.000">
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ ui_change('calculate_amount' , 'property_transaction')  }}</label>
                <input type="number" readonly id="total-area" name="calculate_amount-${i}-${counters[i]}[]"
                    class="form-control  bg-white" step="0.001" placeholder="0.000">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ ui_change('start_date' , 'property_transaction')  }}</label>
                <input type="text"   name="start_date-${i}-${counters[i]}[]" class="main_unit_data text-white form-control"
                value="${form_unit_date}"  onchange="service_date(${i}, ${counters[i]})" >
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ ui_change('expaired_date' , 'property_transaction') }}</label>
                <input type="text" name="expiry_date-${i}-${counters[i]}[]" class="main_unit_data text-white form-control"
                value="${to_unit_date}">
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ ui_change('vat_percentage' , 'property_transaction') }}</label>
                <input type="number" readonly name="vat_percentage-${i}-${counters[i]}[]" class="form-control  bg-white"
                    step="0.001" placeholder="0.000">
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ ui_change('vat_amount' , 'property_transaction') }}</label>
                <input type="number" readonly name="vat_amount-${i}-${counters[i]}[]" class="form-control  bg-white"
                    step="0.001" placeholder="0.000">
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-12">
            <div class="form-group">   
                <label for="total-area">{{ ui_change('total_amount' , 'property_transaction') }}</label>
                <input type="number" readonly id="total-area" name="total_amount-${i}-${counters[i]}[]"
                    class="form-control  bg-white" step="0.001" placeholder="0.000">
            </div>
        </div>
    </div>
    `; 
    container.insertAdjacentHTML('beforeend', bladeContent);
    flatpickr(".main_unit_data", {
        dateFormat: "d/m/Y",
    });

}

function removeService(button) {
    button.closest('.row').remove();
}
    function deleteItemService(button) {
        const itemId = button.getAttribute('data-id');

        $.ajax({
            url: "{{ route('agreement.empty_unit_from_service_agreement', ':id') }}".replace(':id', itemId),
            type: "GET",
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    button.closest('.row').remove();

                    Swal.fire({
                        icon: 'success',
                        title: "{{ ui_change('deleted_successfully' , 'property_transaction') }}",
                        showConfirmButton: true,
                        timer: 3000
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
            }
        });
    }
</script>
    <script>
        flatpickr("#agreement_date", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
            // minDate: "today"
        });
        flatpickr("#main_data", {
            dateFormat: "d/m/Y",
        });
        flatpickr(".main_date", {
            dateFormat: "d/m/Y",
        });

        flatpickr(".period_from_date", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ isset($agreement->agreement_details->period_from) ? \Carbon\Carbon::createFromFormat('Y-m-d', $agreement->agreement_details->period_from)->format('d/m/Y') : '' }}",
            // minDate: "today"
        });
        flatpickr(".period_to_date", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ isset($agreement->agreement_details->period_to) ? \Carbon\Carbon::createFromFormat('Y-m-d', $agreement->agreement_details->period_to)->format('d/m/Y') : '' }}",
            // minDate: "today"
        });
        function deposite(id) {
            rent_amount = $('input[name="rent_amount-' + id + '"]').val();
            deposite_month = $('input[name="security_deposit_months_rent-' + id + '"]').val();
            deposite_all = $('input[name="security_deposit_amount-' + id + '"]').val((rent_amount * deposite_month));
        }

        function service_date(i, key) {
            var form_unit_date = $(`input[name='start_date-${i}-${key}[]']`).val();

            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`input[name='expiry_date-${i}-${key}[]']`).val(formattedDate);


                }
            }
        }
        function agreement_unit_date_clc(i) {
            var form_unit_date = $(`input[name=period_from-${i}]`).val();
            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`input[name=period_to-${i}]`).val(formattedDate);


                }
            }
        }

        function unitFunc(i) {
            var property_id = $('select[name="property_id-' + i + '"]').val();
            var unit_description_id = $('select[name="unit_description_id-' + i + '"]').val();
            var unit_type_id = $('select[name="unit_type_id-' + i + '"]').val();
            var unit_condition_id = $('select[name="unit_condition_id-' + i + '"]').val();
            var view_id = $('select[name="view_id-' + i + '"]').val();
            var property_type = $('select[name="property_type-' + i + '"]').val();
            (property_id, unit_description_id, unit_type_id, unit_condition_id, view_id, property_type);

            $.ajax({
                url: "{{ route('agreement.get_units') }}",
                type: "GET",
                data: {
                    property_id: property_id,
                    unit_description_id: unit_description_id,
                    unit_type_id: unit_type_id,
                    unit_condition_id: unit_condition_id,
                    view_id: view_id,
                    property_type: property_type
                },
                dataType: "json",
                success: function(data) {
                    if (data.length > 0) {
                        $('select[name="unit-' + i + '"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="unit-' + i + '"]').append(
                                '<option value="' + value.id + '">' + value.unit_management_main
                                .name + '</option>'
                            );
                        });
                    } else if (data.length == 0) {
                        $('select[name="unit-' + i + '"]').empty();
                        $('select[name="unit-' + i + '"]').append(
                            '<option value="0">Not Found</option>'
                        );
                    } else {
                        ('No data found.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error occurred:', error);
                }
            });
        }

        function payment_mode_func(i) {
            var payment_mode_id = $('select[name="payment_mode-' + i + '"]').val();
            $('select[name="rent_mode-' + i + '"]').val(payment_mode_id);
            $('select[name="area_measurement-' + i + '"]').removeAttr('disabled');
            $('input[name="amount-' + i + '"]').removeAttr('disabled');
            $('input[name="total_area_amount-' + i + '"]').removeAttr('disabled');
        }

        function rent_mode_amount(i) {
            var amount = parseFloat($('input[name="amount-' + i + '"]').val()) || 0;
            var total_area_amount = parseFloat($('input[name="total_area_amount-' + i + '"]').val()) || 0;

            if (amount != 0 && total_area_amount != 0) {
                $('input[name="vat_percentage-' + i + '"]').removeAttr('disabled');

                var rent_amount = amount * total_area_amount;
                $('input[name="rent_amount-' + i + '"]').val(rent_amount.toFixed(2));
                vat_amount_func(i)
            }
        }

        function vat_amount_func(i) {
            var rent_amount = parseFloat($('input[name="rent_amount-' + i + '"]').val()) || 0;
            var rental_gl = parseFloat($('select[name="rental_gl-' + i + '"]').val()) || 0;
            var vat_percentage = $('input[name="vat_percentage-' + i + '"]').empty();

            var vat_amount = (rent_amount * rental_gl) / 100;
            var total_net_rent_amount = parseFloat(vat_amount) + parseFloat(rent_amount);
            $('input[name="vat_amount-' + i + '"]').val(vat_amount.toFixed(2));
            $('input[name="vat_percentage-' + i + '"]').val(rental_gl.toFixed(2));
            $('input[name="total_net_rent_amount-' + i + '"]').val(total_net_rent_amount.toFixed(2));
        }

        function charge_type(i, j) {
            var charge_type_amount = $('select[name="charge_mode-' + i + '-' + j + '[]"]').val();
            var vat_percentage = $('input[name="vat_percentage-' + i + '-' + j + '[]"]').val(charge_type_amount);
        }

        function service_value_calc(i, j) {
            var service_type = $('select[name="charge_mode_type-' + i + '-' + j + '[]"]').val();
            if (service_type == 'percentage') {
                $('#percentage_amount_charge-' + i + '-' + j + '').removeClass('d-none');
                $('#amount_charge-' + i + '-' + j + '').addClass('d-none');
                var rent_amount = parseFloat($('input[name="rent_amount-' + i + '"]').val()) || 0;

            } else if (service_type == 'amount') {
                $('#amount_charge-' + i + '-' + j + '').removeClass('d-none');
                $('#percentage_amount_charge-' + i + '-' + j + '').addClass('d-none');
            }
        }

        function amount_charge_func(i, j) {
            var amount_val = parseFloat($('input[name="amount_charge-' + i + '-' + j + '[]"]').val()) || 0;
            var vat_percentage_val = parseFloat($('input[name="vat_percentage-' + i + '-' + j + '[]"]').val()) || 0;
            var total_vat_service = amount_val * (vat_percentage_val / 100);
            var total_amount_val = amount_val + total_vat_service;
            $('input[name="vat_amount-' + i + '-' + j + '[]"]').val(total_vat_service.toFixed(2));
            $('input[name="calculate_amount-' + i + '-' + j + '[]"]').val(amount_val.toFixed(2));
            $('input[name="total_amount-' + i + '-' + j + '[]"]').empty().val(total_amount_val.toFixed(2));
        }

        function percentage_amount_charge_func(i, j) {
            var percentage_amount_charge_val = parseFloat($('input[name="percentage_amount_charge-' + i + '-' + j + '[]"]')
                .val()) || 0;
            var rent_amount = parseFloat($('input[name="rent_amount-' + i + '"]').val()) || 0;
            var percentage_amount = rent_amount * (percentage_amount_charge_val / 100);
            $('input[name="calculate_amount-' + i + '-' + j + '[]"]').val(percentage_amount.toFixed(2));
            var vat_percentage_val = parseFloat($('input[name="vat_percentage-' + i + '-' + j + '[]"]').val()) || 0;
            var total_vat_service = percentage_amount * (vat_percentage_val / 100);
            var total_amount_val = percentage_amount + total_vat_service;
            $('input[name="vat_amount-' + i + '-' + j + '[]"]').val(total_vat_service.toFixed(2));
            $('input[name="total_amount-' + i + '-' + j + '[]"]').val(total_amount_val.toFixed(2));
            // $('input[name="grand_total_amount-' + i + '-' + j + '[]"]').val(total_amount_val.toFixed(2));
        }
    </script>
@endpush
