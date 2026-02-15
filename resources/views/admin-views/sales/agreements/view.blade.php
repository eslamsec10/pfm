@extends('layouts.back-end.app')
@php
    $lang = session()->get('locale');
@endphp 
@section('title')
    {{ ui_change('agreement_Info' , 'property_transaction') }} 
@endsection
@push('css_or_js')
    <link href="{{ asset('assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #dedede;
            border: 1px solid #dedede;
            border-radius: 2px;
            color: #222;
            display: flex;
            gap: 4px;
            align-items: center;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0">
                {{-- <img src="{{ asset('/assets/back-end/img/all-orders.png') }}" alt=""> --}}
                {{ ui_change('agreement_Info' , 'property_transaction') }}
            </h2>
        </div>

        <!-- End Page Header -->

        <div class="row gx-2 gy-3" id="printableArea">
            <div class="col-lg-8 col-xl-9">
                <!-- Card -->
                <div class="card h-100">
                    <!-- Body -->
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-10 justify-content-between mb-4">
                            <div class="d-flex flex-column gap-10">
                                <h5
                                    class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4">
                                    {{-- <img src="{{ asset('/assets/back-end/img/seller-information.png') }}" class="mb-1"
                                        alt=""> --}}
                                    {{ ui_change('general_Info' , 'property_transaction') }}
                                </h5>
                                <h4 class="text-capitalize">{{ ui_change('agreement_no' , 'property_transaction') }} .#{{ $agreement->agreement_no }}</h4>
                                <div class="">
                                    <i class="tio-date-range"></i>
                                    {{ date('d M Y H:i:s', strtotime($agreement['agreement_date'])) }}
                                </div>
                            </div>
                            <div class="text-sm-right">

                                <div class="d-flex flex-column gap-2 mt-3">
                                    <!-- Order status -->
                                   
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">

                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('total_no_of_Units' , 'property_transaction') }} :
                                    <strong>{{ $agreement->total_no_of_required_units ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('status' , 'property_transaction') }} :
                                    <strong>{{ $agreement->status?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('Leasing_Executive' , 'property_transaction') }} :
                                    <strong>{{ $agreement->agreement_details->employee->name ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('agent' , 'property_transaction') }} :
                                    <strong>{{ $agreement->agreement_details?->agent?->name ?? $agreement->agreement_details?->agent?->company_name }}</strong></span>
                            </div>


                        </div>
                        <div class="row mt-5">


                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('Decision_Maker' , 'property_transaction') }} :
                                    <strong>{{ $agreement->agreement_details->decision_maker ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('decision_Maker_Designation' , 'property_transaction') }} :
                                    <strong>{{ $agreement->agreement_details->decision_maker_designation ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('current_Office_Location' , 'property_transaction') }} :
                                    <strong>{{ $agreement->agreement_details->current_office_location ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('reason_Of_Relocation' , 'property_transaction') }} :
                                    <strong>{{ $agreement->agreement_details->reason_of_relocation ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>

                        </div>
                        <div class="row mt-5">

                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('time_Frame_For_Relocation' , 'property_transaction') }} :
                                    <strong>{{ $agreement->agreement_details->time_frame_for_relocation ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('relocation_date' , 'property_transaction') }} :
                                    <strong>{{ $agreement->agreement_details->relocation_date ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('Period_From' , 'property_transaction')  }} :
                                    <strong>{{ $agreement->agreement_details->period_from ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('Period_To' , 'property_transaction') }} :
                                    <strong>{{ $agreement->agreement_details->period_to ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>

                        </div>


                        <div class="row justify-content-md-end mb-3">
                           
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4 col-xl-3">
                <!-- Card -->
                <div class="card">

                    <!-- Body -->
                    @if ($agreement->tenant)
                        <div class="card-body">
                            <h4 class="mb-4 d-flex align-items-center gap-2">
                                {{-- <img src="{{ asset('/assets/back-end/img/seller-information.png') }}" alt=""> --}}
                                {{ ui_change('tenant_Info' , 'property_transaction') }}
                            </h4>

                            <div class="media flex-wrap gap-3">
                                <div class="">
                                    <img class="avatar rounded-circle avatar-70"
                                        onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                        src="" alt="Image">
                                    {{-- {{ dd( asset($company->logo_image_url)) }} --}}
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    <span class="title-color hover-c1"><strong>{{ $agreement->tenant->name ?? $agreement->tenant->company_name}}</strong></span>
                                    <span class="title-color hover-c1">{{ ui_change('gender' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->gender  ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                                    <span class="title-color"> {{ ui_change('contact_Person' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->contact_person  ?? ui_change('not_Available' , 'property_transaction') }}</strong>
                                    </span>
                                    <span class="title-color break-all"> {{ ui_change('contact_No' , 'property_transaction') }} :
                                        <strong>{{  $agreement->tenant->contact_no  ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                                    <span class="title-color break-all"> {{ ui_change('whatsapp' , 'property_transaction') }} :
                                        <strong>{{  $agreement->tenant->whatsapp_no  ?? ui_change('not_Available' , 'property_transaction') }}</strong></span> 
                                    <span class="title-color break-all">{{ ui_change('email1' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->email1  ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                                    <span class="title-color break-all">{{ ui_change('type' , 'property_transaction') }} :
                                        <strong>{{ ($agreement->tenant->type)  ?? ui_change('not_Available' , 'property_transaction')  }}</strong></span> 
                                    <span class="title-color break-all">{{ ui_change('address1' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->address1 ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                                    <span class="title-color break-all">{{ ui_change('address2' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->address2 ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                                    <span class="title-color break-all">{{ ui_change('address3' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->address3 ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                                    <span class="title-color break-all">{{ ui_change('state' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->state ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                                    <span class="title-color break-all">{{ ui_change('city' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->city ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                                    <span class="title-color break-all">{{ ui_change('country' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->country_master->country->name ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                                    <span class="title-color break-all">{{ ui_change('nationality' , 'property_transaction') }} :
                                        <strong>{{ $agreement->tenant->country_master->nationality_of_owner ?? ui_change('not_Available' , 'property_transaction') }}</strong></span> 
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card-body">
                            <div class="media align-items-center">
                                <span>{{ ui_change('tenant_Not_Found' , 'property_transaction') }}</span>
                            </div>
                        </div>
                    @endif
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>
        @foreach ($agreement_units as $agreement_units_item)
            {{-- @php
                \Log::info($agreement_units_item);
            @endphp --}}
        
        <div class="row gx-2 gy-3" id="printableArea">
            <div class="col-lg-8 col-xl-9">
                <!-- Card -->
                <div class="card h-100">
                    <!-- Body -->
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-10 justify-content-between mb-4">
                            <div class="d-flex flex-column gap-10">
                                <h5
                                    class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4">
                                    <img src="{{ asset('/assets/back-end/img/seller-information.png') }}" class="mb-1"
                                        alt="">
                                    {{ ui_change('units_Info' , 'property_transaction') }}
                                </h5>
                                <h4 class="text-capitalize">{{ ui_change('unit_no', 'property_transaction') }} # {{ $agreement_units_item->agreement_units?->unit_management_main?->unit_no ?? $agreement_units_item->agreement_units?->unit_management_main?->name }}</h4>

                            </div>
                            <div class="text-sm-right">

                                <div class="d-flex flex-column gap-2 mt-3">

                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">

                            <div class="col-md-6 col-lg-6 col-xl-6">
                                <span class="title-color break-all"> {{ ui_change('vat_no' , 'property_transaction')  }} :
                                    <strong>{{ $agreement->vat_no ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-6">
                                <span class="title-color break-all"> {{ ui_change('group_vat_no' , 'property_transaction')  }} :
                                    <strong>{{ $agreement->group_vat_no ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6 col-lg-6 col-xl-6">
                                <span class="title-color break-all"> {{ ui_change('tax_registration_date' , 'property_transaction')  }} :
                                    <strong>{{ Carbon\Carbon::parse($agreement->tax_reg_date)->format('Y-m-d') ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-6">
                                <span class="title-color break-all"> {{ ui_change('tax_rate' , 'property_transaction')  }} :
                                    <strong>{{ $agreement->tax_rate ?? ui_change('not_Available' , 'property_transaction') }}</strong></span>
                            </div>

                        </div>



                        <div class="row justify-content-md-end mb-3">
                            
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4 col-xl-3">
                <!-- Card -->
                <div class="card">

                    <!-- Body -->
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>
        @endforeach
        <!-- End Row -->
    </div>
    <!--Show locations on map Modal -->
     
    <!-- End Modal -->
@endsection

@push('script_2')
    <script>
        $(document).on('change', '.payment_status', function() {
            var id = $(this).attr("data-id");
            var value = $(this).val();
            Swal.fire({
                title: '{{ ui_change('are_you_sure_change_this' , 'property_transaction') }}?',
                text: "{{ ui_change('you_will_not_be_able_to_revert_this' , 'property_transaction') }}!",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{ ui_change('yes_change_it' , 'property_transaction')  }}!',
                cancelButtonText: '{{ ui_change('cancel' , 'property_transaction')  }}',
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "",
                        method: 'POST',
                        data: {
                            "id": id,
                            "payment_status": value
                        },
                        success: function(data) {
                            toastr.success('{{ ui_change('status_change_successfully' , 'property_transaction')  }}');
                            location.reload();
                        }
                    });
                }
            })
        });

        function order_status(status) {
            @if ($agreement['order_status'] == 'delivered')
                Swal.fire({
                    title: '{{ ui_change('order_is_already_delivered_and_transaction_amount_has_been_disbursed_changing_status_can_be_the_reason_of_miscalculation' , 'property_transaction') }}!',
                    text: "{{ ui_change('think_before_you_proceed' , 'property_transaction')  }}.",
                    showCancelButton: true,
                    confirmButtonColor: '#377dff',
                    cancelButtonColor: 'secondary',
                    confirmButtonText: '{{ ui_change('yes_change_it' , 'property_transaction')  }}!',
                    cancelButtonText: '{{ ui_change('cancel' , 'property_transaction') }}',
                }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "",
                            method: 'POST',
                            data: {
                                "id": '{{ $company['id'] }}',
                                "order_status": status
                            },
                            success: function(data) {
                                if (data.success == 0) {
                                    toastr.success(
                                        '{{ ui_change('order_is_already_delivered_you_can_not_change_it' , 'property_transaction') }} !!'
                                        );
                                    location.reload();
                                } else {
                                    toastr.success('{{ ui_change('status_change_successfully!' , 'property_transaction')  }}');
                                    location.reload();
                                }

                            }
                        });
                    }
                })
            @else
                Swal.fire({
                    title: '{{ ui_change('are_you_sure_change_this?' , 'property_transaction')  }}',
                    text: "{{ ui_change('you_will_not_be_able_to_revert_this!' , 'property_transaction') }}",
                    showCancelButton: true,
                    confirmButtonColor: '#377dff',
                    cancelButtonColor: 'secondary',
                    confirmButtonText: '{{ ui_change('yes_change_it!' , 'property_transaction') }}',
                    cancelButtonText: '{{ ui_change('cancel' , 'property_transaction') }}',
                }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "",
                            method: 'POST',
                            data: {
                                "id": '{{ $agreement['id'] }}',
                                "order_status": status
                            },
                            success: function(data) {
                                if (data.success == 0) {
                                    toastr.success(
                                        '{{ ui_change('order_is_already_delivered_you_can_not_change_it' , 'property_transaction')  }} !!'
                                        );
                                    location.reload();
                                } else {
                                    toastr.success('{{ ui_change('status_change_successfully' , 'property_transaction')  }}!');
                                    location.reload();
                                }

                            }
                        });
                    }
                })
            @endif
        }
    </script>

    <script>
        function addDeliveryMan(id) {
            $.ajax({
                type: "GET",
                url: '{{ url('/') }}/admin/orders/add-delivery-man/{{ $agreement['id'] }}/' + id,
                data: {
                    'order_id': '{{ $agreement['id'] }}',
                    'delivery_man_id': id
                },
                success: function(data) {
                    if (data.status == true) {
                        toastr.success('{{ ui_change('delivery_man_successfully_assigned_or_changed' , 'property_transaction')  }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    } else {
                        toastr.error('{{ ui_change('deliveryman_can_not_assign_or_change_in_that_status' , 'property_transaction')  }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function() {
                    toastr.error('{{ ui_change('add_valid_data' , 'property_transaction') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function last_location_view() {
            toastr.warning('{{ ui_change('only_available_when_order_is_out_for_delivery' , 'property_transaction')  }}!', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        function waiting_for_location() {
            toastr.warning('{{ ui_change('waiting_for_location' , 'property_transaction')  }}', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
@endpush
