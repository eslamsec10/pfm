@extends('layouts.back-end.app')

@section('title', ui_change('dashboard' , 'dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        table {
            /* width: 100%; */
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
            /* background-color: #f9f9f9; */
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
            background-color: #0177cd;
            color: white;
            /* background-color: var(--secondary); #2b368f */
            padding: 20px;
            border-radius: 10px;
            /* max-width: 1200px; */
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
        <!-- Page Header -->
        <div class="page-header pb-0 mb-0 border-0">
            <div class="flex-between align-items-center">
                <div>
                    <h1 class="page-header-title"
                        style="text-align: {{ Session::get('locale') === 'ar' ? 'right' : 'left' }};">
                        {{ ui_change('dashboard' , 'dashboard') }}</h1>
                    <p>{{ ui_change('welcome_message' , 'dashboard') }}.</p>
                </div>
            </div>
        </div> 
        <div class="card mb-2 remove-card-shadow">
            <div class="card-body">
                <div class="row flex-between align-items-center g-2 mb-3">


                </div>
                <form action="{{ route('general_search_units_in_dashboard') }}" method="get">
                    <div class="row g-2" id="order_stats">
                        <div class="form-container mt-3  ">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="building">{{ ui_change('Building' , 'dashboard') }}</label>
                                    <select id="building" name="property_id" class="js-select2-custom form-control">
                                        <option value="">{{ ui_change('Select_building' , 'dashboard')}}</option>
                                        @foreach ($buildings as $building)
                                            <option value="{{ $building->id }}"  >{{ $building->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="unit-description">{{ ui_change('Unit_Description' , 'dashboard')}}</label>
                                    <select id="unit-description" name="unit_description_id"
                                        class="js-select2-custom form-control">
                                        <option value="">{{ ui_change('Any' , 'dashboard') }}</option>
                                        @foreach ($unit_descriptions as $unit_description)
                                            <option value="{{ $unit_description->id }}">
                                                {{ $unit_description->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="unit-type">{{ ui_change('Unit_Type','dashboard') }}</label>
                                    <select id="unit-type" name="unit_type_id" class="js-select2-custom form-control">
                                        <option value="">{{ ui_change('Any','dashboard') }}</option>
                                        @foreach ($unit_types as $unit_type)
                                            <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="unit-condition">{{ ui_change('Unit_Condition','dashboard') }}</label>
                                    <select id="unit-condition" name="unit_condition_id"
                                        class="js-select2-custom form-control">
                                        <option value="">{{ ui_change('Any','dashboard') }}</option>
                                        @foreach ($unit_conditions as $unit_condition)
                                            <option value="{{ $unit_condition->id }}">{{ $unit_condition->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="preferred-view">{{ ui_change('Preferred_View') }}</label>
                                    <select id="preferred-view" name="view_id" class="js-select2-custom form-control">
                                        <option value="">{{ ui_change('Any' ,'dashboard') }}</option>
                                        @foreach ($views as $view)
                                            <option value="{{ $view->id }}">{{ $view->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row justify-content-end gap-3 mt-3 mx-1">
                        <button type="submit" class="btn btn-warning px-5"><i
                                class="fa fa-search"></i>{{ ui_change('search','dashboard') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-2 remove-card-shadow">
            <div class="card-body">
                <div class="row flex-between align-items-center g-2 mb-3">
                    <div class="col-sm-6">
                        <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                            <img src="{{ asset('/assets/back-end/img/business_analytics.png') }}" alt="">
                        </h4>
                    </div>

                </div>
                <div class="row g-2" id="order_stats">
                    @include('admin-views.partials.user_dashboard_details')
                </div>
            </div>
        </div>



        <div class="row g-1">

            <!-- End Total Business Overview -->

            <div class="col-md-6 col-xl-4">
                <!-- Card -->
                <div class="card h-100 remove-card-shadow">
                    @include('admin-views.partials._top-customer', ['top_customer' => $tenants])
                </div>
                <!-- End Card -->
            </div>
            <div class="col-md-6 col-xl-8">


                <!-- Column -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h4 class="card-title">{{ ui_change('Agreements','dashboard') }} </h4>
                                <h5 class="card-subtitle">{{ ui_change('Overview_of_Latest_Month','dashboard') }}</h5>
                            </div>
                            <div class="ml-auto">
                                <ul class="list-inline font-12 dl m-r-10">
                                    <li class="list-inline-item">
                                        <i class="fas fa-dot-circle text-info"></i> {{ ui_change('Bookings','dashboard') }}
                                    </li>
                                    <li class="list-inline-item">
                                        <i class="fas fa-dot-circle text-danger"></i> {{ ui_change('Agreements','dashboard') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div id="product-sales" style="height:305px"></div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection



@push('script')
    <script src="{{ asset('assets/back-end') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <!--chartis chart-->
    <script src="{{ asset('js/dashboards/chartist.min.js') }}"></script>
    <script src="{{ asset('js/dashboards/custom.min.js') }}"></script>
    <script src="{{ asset('js/dashboards/chartist-plugin-tooltip.min.js') }}"></script>
    <!--c3 charts -->
    <!--chartjs -->
    <script src="{{ asset('js/dashboards/raphael.min.js') }}"></script>
    <script src="{{ asset('js/dashboards/morris.min.js') }}"></script>
    <script src="{{ asset('js/dashboards/dashboard1.js') }}"></script>

    <script src="{{ asset('assets/back-end') }}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script src="{{ asset('assets/back-end') }}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js">
    </script>
    <script>
        
        $('select[name=property_id]').on('change', function() {
            var property_id = $(this).val();
            if (property_id) {
                $.ajax({
                    url: "{{ route('dashboard.get_unit_details', ':id') }}".replace(':id', property_id),
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            $('select[name="unit_description_id"]').empty();
                            $('select[name="unit_type_id"]').empty();
                            $('select[name="unit_condition_id"]').empty();
                            $('select[name="view_id"]').empty();
                            $('select[name="unit_condition_id"]').append(
                                `<option value="">Any</option>`
                            );
                        $('select[name="view_id"]').append(
                                `<option value="">Any</option>`
                            );
                        $('select[name="unit_type_id"]').append(
                                `<option value="">Any</option>`
                            );
                        $('select[name="unit_description_id"]').append(
                                `<option value="">Any</option>`
                            );
                            data.unit_conditions.forEach(function(unit_condition) {
                                $('select[name="unit_condition_id"]').append(
                                    `<option value="${unit_condition.id}">${unit_condition.name}</option>`
                                );
                            });
                            data.unit_view.forEach(function(view) {
                                $('select[name="view_id"]').append(
                                    `<option value="${view.id}">${view.name}</option>`
                                );
                            });
                            data.unit_types.forEach(function(unit_type) {
                                $('select[name="unit_type_id"]').append(
                                    `<option value="${unit_type.id}">${unit_type.name}</option>`
                                );
                            });
                            data.unit_descriptions.forEach(function(desc) {
                                $('select[name="unit_description_id"]').append(
                                    `<option value="${desc.id}">${desc.name}</option>`
                                );
                            });
                           

                        } else {}
                    },
                    error: function(xhr, status, error) {
                        console.error('Error occurred:', error , status);
                    }
                });
            }
        })
        $('select[name=unit_description_id]').on('change', function() {
            var property_id= $('select[name=property_id]').val();
            var unit_description_id = $(this).val();

            if (property_id) {
                $.ajax({
                    url: "{{ route('dashboard.get_unit_details', ':id') }}".replace(':id', property_id),
                    type: "GET",
                    data:   {
                        'unit_description_id': unit_description_id
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            // $('select[name="unit_description_id"]').empty();
                            $('select[name="unit_type_id"]').empty();
                            $('select[name="unit_condition_id"]').empty();
                            $('select[name="view_id"]').empty();
                            $('select[name="unit_condition_id"]').append(
                                `<option value="">Any</option>`
                            );
                        $('select[name="view_id"]').append(
                                `<option value="">Any</option>`
                            );
                        $('select[name="unit_type_id"]').append(
                                `<option value="">Any</option>`
                            );
                        // $('select[name="unit_description_id"]').append(
                        //         `<option value="">Any</option>`
                        //     );
                        data.unit_types.forEach(function(unit_type) {
                                $('select[name="unit_type_id"]').append(
                                    `<option value="${unit_type.id}">${unit_type.name}</option>`
                                );
                            });
                            data.unit_conditions.forEach(function(unit_condition) {
                                $('select[name="unit_condition_id"]').append(
                                    `<option value="${unit_condition.id}">${unit_condition.name}</option>`
                                );
                            });
                            data.unit_view.forEach(function(view) {
                                $('select[name="view_id"]').append(
                                    `<option value="${view.id}">${view.name}</option>`
                                );
                            });
                           
                            // data.unit_descriptions.forEach(function(desc) {
                            //     $('select[name="unit_description_id"]').append(
                            //         `<option value="${desc.id}">${desc.name}</option>`
                            //     );
                            // });
                           

                        } else {}
                    },
                    error: function(xhr, status, error) {
                        console.error('Error occurred:', error);
                    }
                });
            }
        })
        $('select[name=unit_type_id]').on('change', function() {
            var property_id= $('select[name=property_id]').val();
            var unit_type_id = $(this).val();
            var unit_description_id= $('select[name=unit_description_id]').val();

            if (property_id) {
                $.ajax({
                    url: "{{ route('dashboard.get_unit_details', ':id') }}".replace(':id', property_id),
                    type: "GET",
                    data:   {
                        'unit_description_id': unit_description_id,
                        'unit_type_id': unit_type_id,

                    },
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            // $('select[name="unit_description_id"]').empty();
                            // $('select[name="unit_type_id"]').empty();
                            $('select[name="unit_condition_id"]').empty();
                            $('select[name="view_id"]').empty();
                            $('select[name="unit_condition_id"]').append(
                                `<option value="">Any</option>`
                            );
                        $('select[name="view_id"]').append(
                                `<option value="">Any</option>`
                            );
                        $('select[name="unit_type_id"]').append(
                                `<option value="">Any</option>`
                            );
                        $('select[name="unit_description_id"]').append(
                                `<option value="">Any</option>`
                            );
                            data.unit_conditions.forEach(function(unit_condition) {
                                $('select[name="unit_condition_id"]').append(
                                    `<option value="${unit_condition.id}">${unit_condition.name}</option>`
                                );
                            });
                            data.unit_view.forEach(function(view) {
                                $('select[name="view_id"]').append(
                                    `<option value="${view.id}">${view.name}</option>`
                                );
                            });
                            // data.unit_types.forEach(function(unit_type) {
                            //     $('select[name="unit_type_id"]').append(
                            //         `<option value="${unit_type.id}">${unit_type.name}</option>`
                            //     );
                            // });
                            // data.unit_descriptions.forEach(function(desc) {
                            //     $('select[name="unit_description_id"]').append(
                            //         `<option value="${desc.id}">${desc.name}</option>`
                            //     );
                            // });
                           

                        } else {}
                    },
                    error: function(xhr, status, error) {
                        console.error('Error occurred:', error);
                    }
                });
            }
        })
        $('select[name=unit_condition_id]').on('change', function() {
            var property_id= $('select[name=property_id]').val();
            var unit_type_id= $('select[name=unit_type_id]').val();
            var unit_description_id= $('select[name=unit_description_id]').val();
            var unit_condition_id = $(this).val();

            if (property_id) {
                $.ajax({
                    url: "{{ route('dashboard.get_unit_details', ':id') }}".replace(':id', property_id),
                    type: "GET",
                    data:   {
                        'unit_condition_id': unit_condition_id,
                        'unit_description_id': unit_description_id,
                        'unit_type_id': unit_type_id,
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            // $('select[name="unit_description_id"]').empty();
                            // $('select[name="unit_type_id"]').empty();
                            // $('select[name="unit_condition_id"]').empty();
                            $('select[name="view_id"]').empty();
                            // $('select[name="unit_condition_id"]').append(
                            //     `<option value="">Any</option>`
                            // );
                        $('select[name="view_id"]').append(
                                `<option value="">Any</option>`
                            );
                        // $('select[name="unit_type_id"]').append(
                        //         `<option value="">Any</option>`
                        //     );
                        // $('select[name="unit_description_id"]').append(
                        //         `<option value="">Any</option>`
                        //     );
                            // data.unit_conditions.forEach(function(unit_condition) {
                            //     $('select[name="unit_condition_id"]').append(
                            //         `<option value="${unit_condition.id}">${unit_condition.name}</option>`
                            //     );
                            // });
                            data.unit_view.forEach(function(view) {
                                $('select[name="view_id"]').append(
                                    `<option value="${view.id}">${view.name}</option>`
                                );
                            });
                            // data.unit_types.forEach(function(unit_type) {
                            //     $('select[name="unit_type_id"]').append(
                            //         `<option value="${unit_type.id}">${unit_type.name}</option>`
                            //     );
                            // });
                            // data.unit_descriptions.forEach(function(desc) {
                            //     $('select[name="unit_description_id"]').append(
                            //         `<option value="${desc.id}">${desc.name}</option>`
                            //     );
                            // });
                           

                        } else {}
                    },
                    error: function(xhr, status, error) {
                        console.error('Error occurred:', error);
                    }
                });
            }
        })
    </script>
@endpush
                    

@push('script_2')
    <script>
        function earningStatisticsUpdate(t) {
            let value = $(t).attr('data-earn-type');

            $.ajax({
                url: '#',
                type: 'GET',
                data: {
                    type: value
                },
                beforeSend: function() {
                    $('#loading').fadeIn();
                },
                success: function(response_data) {
                    document.getElementById("updatingData").remove();
                    let graph = document.createElement('canvas');
                    graph.setAttribute("id", "updatingData");
                    document.getElementById("set-new-graph").appendChild(graph);

                    var ctx = document.getElementById("updatingData").getContext("2d");

                    var options = {
                        responsive: true,
                        bezierCurve: false,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    color: "rgba(180, 208, 224, 0.5)",
                                    zeroLineColor: "rgba(180, 208, 224, 0.5)",
                                }
                            }],
                            yAxes: [{
                                gridLines: {
                                    color: "rgba(180, 208, 224, 0.5)",
                                    zeroLineColor: "rgba(180, 208, 224, 0.5)",
                                    borderDash: [8, 4],
                                }
                            }]
                        },
                        legend: {
                            display: true,
                            position: "top",
                            labels: {
                                usePointStyle: true,
                                boxWidth: 6,
                                fontColor: "#758590",
                                fontSize: 14
                            }
                        },
                        plugins: {
                            datalabels: {
                                display: false
                            }
                        },
                    };
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: [],
                            datasets: [{
                                    label: "{{ ui_change('in-house') }}",
                                    data: [],
                                    backgroundColor: "#ACDBAB",
                                    hoverBackgroundColor: "#ACDBAB",
                                    borderColor: "#ACDBAB",
                                    fill: false,
                                    lineTension: 0.3,
                                    radius: 0
                                },
                                {
                                    label: "{{ ui_change('seller') }}",
                                    data: [],
                                    backgroundColor: "#0177CD",
                                    hoverBackgroundColor: "#0177CD",
                                    borderColor: "#0177CD",
                                    fill: false,
                                    lineTension: 0.3,
                                    radius: 0
                                },
                                {
                                    label: "{{ ui_change('commission') }}",
                                    data: [],
                                    backgroundColor: "#FFB36D",
                                    hoverBackgroundColor: "FFB36D",
                                    borderColor: "#FFB36D",
                                    fill: false,
                                    lineTension: 0.3,
                                    radius: 0
                                }
                            ]
                        },
                        options: options
                    });

                    myChart.data.labels = response_data.inhouse_label;
                    myChart.data.datasets[0].data = response_data.inhouse_earn;
                    myChart.data.datasets[1].data = response_data.seller_earn;
                    myChart.data.datasets[2].data = response_data.commission_earn;

                    myChart.update();
                },
                complete: function() {
                    $('#loading').fadeOut();
                }
            });
        }
    </script>

    <script>
        // INITIALIZATION OF CHARTJS
        // =======================================================
        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function() {
            $.HSCore.components.HSChartJS.init($(this));
        });

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));
    </script>

    {{-- <script>
            var ctx = document.getElementById('business-overview');
            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [
                        '{{ ui_change('customer') }} ',
                        '{{ ui_change('store') }} ',
                        '{{ ui_change('product') }} ',
                        '{{ ui_change('order') }} ',
                        '{{ ui_change('brand') }} ',
                    ],
                    datasets: [{
                        label: '{{ ui_change('business') }}',
                        data: ['{{ $data['customer'] }}', '{{ $data['store'] }}', '{{ $data['product'] }}',
                            '{{ $data['order'] }}', '{{ $data['brand'] }}'
                        ],
                        backgroundColor: [
                            '#041562',
                            '#DA1212',
                            '#EEEEEE',
                            '#11468F',
                            '#000000',
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script> --}}

    <script>
        $(function() {

            //get the doughnut chart canvas
            var ctx1 = $("#user_overview");

            //doughnut chart data
            var data1 = {
                labels: ["Customer", "Seller", "Delivery Man"],
                datasets: [{
                    label: "User Overview",
                    data: [88297, 34546, 15000],
                    backgroundColor: [
                        "#017EFA",
                        "#51CBFF",
                        "#56E7E7",
                    ],
                    borderColor: [
                        "#017EFA",
                        "#51CBFF",
                        "#56E7E7",
                    ],
                    borderWidth: [1, 1, 1]
                }]
            };

            //options
            var options = {
                responsive: true,
                cutoutPercentage: 65,
                legend: {
                    display: true,
                    position: "bottom",
                    align: "start",
                    maxWidth: 100,
                    labels: {
                        usePointStyle: true,
                        boxWidth: 6,
                        fontColor: "#758590",
                        fontSize: 14
                    }
                }
            };

            //create Chart class object
            var chart1 = new Chart(ctx1, {
                type: "doughnut",
                data: data1,
                options: options
            });
        });
    </script>

    <script>
        $(function() {
            //get the line chart canvas
            var ctx = $("#order_statictics");

            //line chart data
            var data = {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                        label: "In-house",
                        data: [10000, 50000, 100000, 140000, 40000, 10000, 50000, 100000, 130000, 40000,
                            80000, 120000
                        ],
                        backgroundColor: "#FFB36D",
                        borderColor: "#FFB36D",
                        fill: false,
                        lineTension: 0.3,
                        radius: 2
                    },
                    {
                        label: "Seller",
                        data: [9000, 60000, 110000, 130000, 50000, 29000, 60000, 110000, 100000, 50000,
                            70000, 90000
                        ],
                        backgroundColor: "#0177CD",
                        borderColor: "#0177CD",
                        fill: false,
                        lineTension: 0.3,
                        radius: 2
                    }
                ]
            };

            //options
            var options = {
                responsive: true,
                bezierCurve: false,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        gridLines: {
                            color: "rgba(180, 208, 224, 0.5)",
                            zeroLineColor: "rgba(180, 208, 224, 0.5)",
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            color: "rgba(180, 208, 224, 0.5)",
                            zeroLineColor: "rgba(180, 208, 224, 0.5)",
                            borderDash: [8, 4],
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: "top",
                    labels: {
                        usePointStyle: true,
                        boxWidth: 6,
                        fontColor: "#758590",
                        fontSize: 14
                    }
                }
            };

            //create Chart class object
            var chart = new Chart(ctx, {
                type: "line",
                data: data,
                options: options
            });
        });
    </script>

    <script>
        function order_stats_update(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '#',
                data: {
                    statistics_type: type
                },
                beforeSend: function() {
                    $('#loading').fadeIn();
                },
                success: function(data) {
                    console.log(data)
                    $('#order_stats').html(data.view)
                },
                complete: function() {
                    $('#loading').fadeOut();
                }
            });
        }

        function business_overview_stats_update(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '#',
                data: {
                    business_overview: type
                },
                beforeSend: function() {
                    $('#loading').fadeIn();
                },
                success: function(data) {
                    console.log(data.view)
                    $('#business-overview-board').html(data.view)
                },
                complete: function() {
                    $('#loading').fadeOut();
                }
            });
        }
    </script>
@endpush
