@extends('layouts.back-end.app')

@section('title', ui_change('edit_tenant' , 'property_master') )
@php
    $lang = Session::get('locale');
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
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
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
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{-- <img src="{{ asset(main_path() . 'back-end/img/inhouse-product-list.png') }}" alt=""> --}}
                {{ ui_change('edit_tenant' , 'property_master') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Form -->
        {{-- <form class="product-form text-start" action="{{ route('tenant.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf --}}
            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        {{-- <img src="{{ asset(main_path() . 'back-end/img/shop-information.png') }}" class="mb-1"
                            alt=""> --}}
                        <h4 class="mb-0">{{ ui_change('edit_tenant' , 'property_master') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        <li class="nav-item">
                            <a class="nav-link type_link @if ($tenant->type == 'individual') active @endif " href="#"
                                id="personal-link">{{ ui_change('personal' , 'property_master')  }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link type_link @if ($tenant->type == 'company') active @endif " href="#"
                                id="company-link">{{ ui_change('company' , 'property_master')  }}</a>
                        </li>
                    </ul>
                    <div class="col-md-12 tenant_form personal-form @if ($tenant->type == 'company') d-none @endif " id="personal-form">
                        <form action="{{ route('tenant.update' , $tenant->id) }}" method="post">
                            @csrf
                            @method('patch')
                            @include('admin-views.property_transactions.tenants.personal_form')
                            <div class="row justify-content-end gap-3 mt-3 mx-1">
                                <button type="reset" class="btn btn-secondary px-5">{{ ui_change('reset' , 'property_master')  }}</button>
                                <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit' , 'property_master') }}</button>
                            </div>

                        </form>
                    </div>
                    <div class="col-md-12 tenant_form @if ($tenant->type == 'individual') d-none @endif company-form" id="company-form">
                        <form action="{{ route('tenant.update' , $tenant->id) }}" method="post">
                            @csrf
                            @method('patch')
                            @include('admin-views.property_transactions.tenants.company_form' )
                            <div class="row justify-content-end gap-3 mt-3 mx-1">
                                <button type="reset" class="btn btn-secondary px-5">{{ ui_change('reset' , 'property_master')  }}</button>
                                <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit' , 'property_master')  }}</button>
                            </div>

                        </form>


                    </div>
                </div>
            </div>


            {{-- <div>
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>No. of unit(s)</th>
                            <th>Period from</th>
                            <th>Period to</th>
                            <th>Rent Per Month</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="unit-label">Apartment</td>
                            <td><input type="text" placeholder="No. of unit(s)"></td>
                            <td><input type="text" placeholder="DD/MM/YYYY"></td>
                            <td><input type="text" placeholder="DD/MM/YYYY"></td>
                            <td><input type="text" placeholder="0.000"></td>
                        </tr>
                        <tr>
                            <td class="unit-label">Store</td>
                            <td><input type="text" placeholder="No. of unit(s)"></td>
                            <td><input type="text" placeholder="DD/MM/YYYY"></td>
                            <td><input type="text" placeholder="DD/MM/YYYY"></td>
                            <td><input type="text" placeholder="0.000"></td>
                        </tr>
                        <tr>
                            <td class="unit-label">Offices</td>
                            <td><input type="text" placeholder="No. of unit(s)"></td>
                            <td><input type="text" placeholder="DD/MM/YYYY"></td>
                            <td><input type="text" placeholder="DD/MM/YYYY"></td>
                            <td><input type="text" placeholder="0.000"></td>
                        </tr>
                        <tr>
                            <td class="unit-label">Warehouse</td>
                            <td><input type="text" placeholder="No. of unit(s)"></td>
                            <td><input type="text" placeholder="DD/MM/YYYY"></td>
                            <td><input type="text" placeholder="DD/MM/YYYY"></td>
                            <td><input type="text" placeholder="0.000"></td>
                        </tr>
                    </tbody>
                </table>
            </div> --}}

        {{-- </form> --}}



    </div>
@endsection
@push('script')
<script>
    $(".type_link").click(function(e) {
        e.preventDefault();
        $(".type_link").removeClass('active');
        $(".tenant_form").addClass('d-none');
        $(this).addClass('active');

        let form_id = this.id;
        console.log(form_id)
        if (form_id === 'personal-link') {
            $("#personal-form").removeClass('d-none').addClass('active');
            $("#company-form").removeClass('active').addClass('d-none');
        } else if (form_id === 'company-link') {
            $("#company-form").removeClass('d-none').addClass('active');
            $("#personal-form").removeClass('active').addClass('d-none');
        }

    });
    // $(".prefix_link").click(function() {
    //     $(".prefix_input").addClass('d-none');
    //     if ($(this).attr('id') === "active") {
    //         $(".prefix_input").removeClass('d-none');
    //     }
    // });
    // $(".fill_zero_link").click(function() {
    //     $(".fill_zero_link_input").addClass('d-none');
    //     if ($(this).attr('id') === "active") {
    //         $(".fill_zero_link_input").removeClass('d-none');
    //     }
    // });

    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endpush
