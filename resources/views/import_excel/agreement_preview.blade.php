@extends('layouts.back-end.app')
@section('title', ui_change('preview_excel'))

@section('content')
    <div class="content container-fluid">

        <div class="mb-4">
            <h2 class="h1 mb-1 text-capitalize d-flex gap-2">
                {{ ui_change('Import') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.import.inline-menu')

        <!-- Content Row -->
        {{-- <div class="row" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"> --}}

        <form action="{{ route('import.confirm_agreement') }}" method="POST">
            @csrf
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ ui_change('Preview_Imported_Data') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped table-bordered table-hover align-middle mb-0">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th class="text-center fw-bold sticky-top"
                                        style="top:0; background:#4f99e4; z-index:10;">
                                        {{ ui_change('sl') }}
                                    </th>

                                    @foreach (array_keys($validRows->first()) as $header)
                                        <th class="text-center fw-bold sticky-top"
                                            style="top:0; background:#4f99e4; z-index:10;">
                                            {{ ui_change($header) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($validRows as $rowIndex => $row)
                                    <tr>
                                        <td>{{ $rowIndex + 1 }}</td>

                                        @foreach ($row as $value)
                                            @php
                                                // تحويل Excel serial date
                                                if (is_numeric($value) && $value > 10000) {
                                                    try {
                                                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(
                                                            $value,
                                                        );
                                                        $value = $date->format('Y-m-d');
                                                    } catch (\Exception $e) {
                                                    }
                                                }
                                            @endphp
                                            <td>{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        {{ ui_change('Confirm_&_Save') }}
                    </button>
                </div>
            </div>
        </form>

        <script>
            function resizeInput(input) {
                input.style.width = "1px";
                input.style.width = (input.scrollWidth + 10) + "px";
            }

            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll(".auto-resize").forEach(function(input) {
                    resizeInput(input);
                });
            });
        </script>


    </div>

@endsection
