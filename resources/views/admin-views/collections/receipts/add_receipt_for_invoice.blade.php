@extends('layouts.back-end.app')
@php
    $company =(new App\Models\Company())->setConnection('tenant')->first() ;
    // $company =(new App\Models\Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? App\Models\User::first();
@endphp
@section('title', __('roles.create_receipt'))

@push('css_or_js')
    <style>
        .dropdown-menu {
            width: 200px;
        }

        .input-container {
            background: #ffffcc;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{ __('roles.create_receipt') }}
            </h2>
        </div>
        <!-- End Page Title -->
        <div class="container mt-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('roles.create_receipt') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('receipts.store_receipt') }}" method="post">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td class="width30">{{ __('property_transactions.tenant') }}:</td>
                                    <td>
                                        <input type="text"
                                            value="{{ $tenant->type == 'individual' ? $tenant->name ?? __('general.not_available') : $tenant->company_name ?? __('general.not_available') }}"
                                            readonly style="border: none; background: transparent; width: 100%;">
                                        <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="width30">{{ __('collections.balance_due') }} :</td>
                                    <td>
                                        <input type="text" value="{{ number_format($total_debit, $company->decimals) }}"
                                            readonly name="balance_due"
                                            style="border: none; background: transparent; width: 100%;">
                                    </td>
                                </tr>
                            </thead>
                        </table>
 

                        <!-- Voucher and Payment Method -->
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label class="form-label">Voucher</label>
                                <select class="js-select2-custom form-control" name="voucher_type">
                                    @foreach ($receipt_settings as $receipt_setting_item)
                                        <option value="{{ $receipt_setting_item->id }}">{{ $receipt_setting_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Receipt Ref</label>
                                <input type="text" class="form-control" name="receipt_ref" value="{{ receiptNo(0) }}"
                                    readonly>
                            </div>


                            <div class="col-md-3">
                                <label class="form-label">Receipt Amount</label>
                                <input type="number" id="receipt-amount" name="receipt_amount" class="form-control mb-3"
                                    max="{{ $invoice->total }}" step="0.01">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Receipt Date</label>
                                <input type="text" class="form-control" name="receipt_date" id="from_search_date">
                            </div>
                        </div>
                        <div class="row mb-1" id="payment-fields">


                            <div class="col-md-3">

                                <div class="container mt-4">
                                    {{-- <label class="form-label">Payment Method</label> --}}
                                    <div class="dropdown">
                                        <button class="btn btn-light dropdown-toggle w-100" type="button"
                                            data-bs-toggle="dropdown">
                                            Select Payment Methods
                                        </button>
                                        <ul class="dropdown-menu p-2" id="payment_method">
                                            @foreach ($main_ledgers as $main_ledgers_item)
                                                <li><input type="checkbox" class="payment-checkbox mt-1"
                                                        name="payment_method[]" value="{{ $main_ledgers_item->id }}">
                                                    {{ $main_ledgers_item->name }}
                                                    <input type="hidden" name="payment_method_name"
                                                        value="{{ $main_ledgers_item->name }}">
                                                </li>
                                            @endforeach

                                        </ul>

                                    </div>

                                    <!-- Dynamic Payment Fields -->

                                </div>
                            </div>
                            {{-- <div id="payment-fields" class="mt-3"></div> --}}
                        </div>

                        <!-- Auto Apply Amount & Advance Checkbox -->
                        <div class="mb-3">
                            {{-- <a href="#" class="text-primary">Auto Apply Amount</a> --}}
                            <div class="form-check d-inline-block ms-3">
                                <input class="form-check-input" type="checkbox" name="isAdvance" id="isAdvance">
                                <label class="form-check-label" for="isAdvance">Is Advance</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-group d-none" id="advance_ref">
                                <label class="form-control-label" for="advance_ref">Advance Ref</label>
                                <input class="form-control" type="text" name="advance_ref">
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive" id="items_table">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Ref</th>
                                        <th>Net Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Balance Due</th>
                                        <th>Enter Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($invoices as $invoice_item) --}}
                                    @foreach ($invoice->items as $invoice_item_item)
                                        <tr>
                                            <td>
                                                <input type="text" value="{{ $invoice->invoice_date }}" readonly
                                                    style="border: none; background: transparent; width: 100%;">
                                            </td>
                                            <td>
                                                <input type="text" value="Invoice" readonly
                                                    style="border: none; background: transparent; width: 100%;">
                                            </td>
                                            <td>
                                                <input type="text" value="{{ $invoice->invoice_number }}" readonly
                                                    style="border: none; background: transparent; width: 100%;">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    value="{{ number_format($invoice_item_item->total, $company->decimals) }}"
                                                    readonly style="border: none; background: transparent; width: 100%;">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    value="{{ number_format($invoice_item_item->paid_amount, $company->decimals) }}"
                                                    readonly style="border: none; background: transparent; width: 100%;">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    value="{{ number_format($invoice_item_item->total - $invoice_item_item->paid_amount, $company->decimals) }}"
                                                    readonly style="border: none; background: transparent; width: 100%;">
                                            </td>
                                            <td>
                                                <input
                                                    onfocus="payTotalAmount(this); focus_amount_fn(this, {{ $invoice_item_item->total - $invoice_item_item->paid_amount }}, {{ $company->decimals }})"
                                                    {{-- onfocus="checkTotalAmount(this, 100); focus_amount_fn(this, {{ $invoice_item_item->total - $invoice_item_item->paid_amount }}, {{ $company->decimals }})" --}} {{-- oninput="checkTotalAmount(this, 100)"
                                                        onchange="checkTotalAmount(this, 100)"  --}} type="number" step="0.01"
                                                    onkeyup="payTotalAmount(this)" class="form-control amount-input-table"
                                                    name="pay_amount[{{ $invoice_item_item->id }}]" disabled>
                                            </td>
                                            <input type="hidden" name="receipt_item_id[{{ $invoice_item_item->id }}]"
                                                value="{{ $invoice_item_item->id }}">
                                        </tr>
                                    @endforeach
                                    {{-- <input type="hidden" name="invoice_id[]" value="{{ $invoice->id }}"> --}}
                                    {{-- @endforeach --}}
                                </tbody>
                            </table>
                        </div>

                        <!-- Buttons -->
                        <div class="text-end">
                            <button class="btn btn--primary"><i class="bi bi-save"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>
        $('input[name="receipt_amount"]').on('keyup', function() {
            var receipt_amount = $(this).val();
            var inputs = $('input[name^="pay_amount"]');
            var inputs_two = $('input[name^="payment_amount"]');
            inputs.each(function() {
                $(this).removeAttr('disabled');
                $(this).val(0);
            });
            inputs_two.each(function() {
                $(this).removeAttr('disabled');
                $(this).val(0);
            });
        });

        function payTotalAmount(element) {
            var receipt_amount = parseFloat($('input[name="receipt_amount"]').val()) || 0;
            var inputs = $('input[name^="pay_amount"]');
            var amount = 0;

            inputs.each(function() {
                amount += parseFloat($(this).val()) || 0;
            });

            var currentValue = parseFloat($(element).val()) || 0;
            var maxAllowed = receipt_amount - (amount - currentValue);

            if (currentValue > maxAllowed) {
                $(element).val(maxAllowed);
            }
        }

        function payTotalAmountLedger(element) {
            var receipt_amount = parseFloat($('input[name="receipt_amount"]').val()) || 0;
            var inputs = $('input[name^="payment_amount"]');
            var amount = 0;

            inputs.each(function() {
                amount += parseFloat($(this).val()) || 0;
            });

            var currentValue = parseFloat(element.val()) || 0;
            var maxAllowed = receipt_amount - (amount - currentValue);

            if (currentValue > maxAllowed) {
                $(element).val(maxAllowed);
            }

        }

        $(document).ready(function() {
            let container = $("#payment-fields");

            $(document).on("change", ".payment-checkbox", function() {
                let receiptAmount = parseFloat($('input[name="receipt_amount"]').val()) || 0;
                let method = $(this).val();
                let payment_method_name = $(this).closest('li').find("input[name='payment_method_name']")
                    .val();
                console.log(payment_method_name);
                if (payment_method_name && payment_method_name.toLowerCase().startsWith("bank")) {
                    if ($(this).is(":checked")) {
                        let inputField = `
            <div class="col-md-3 input-container" id="input-${method.replace(/\s+/g, '-')}">
                <strong>${payment_method_name}</strong>
                <label class="form-label d-block">Cheque Amount</label>
                <input type="number" step="0.01" class="form-control amount-input" data-method="${method}" 
                    name="payment_amount[${method}]" min="0" placeholder="Enter amount"  >
            </div>  
            <div class="col-md-3 input-container" id="input_bank_name-${method.replace(/\s+/g, '-')}">
                <strong>${payment_method_name}</strong>
                <label class="form-label d-block">Bank Name</label>
                <input type="text" class="form-control amount-input" data-method="${method}" 
                    name="bank_name[${method}]"   placeholder="Enter Bank Name"  >
            </div> 
            <div class="col-md-3 input-container" id="input_cheque_no-${method.replace(/\s+/g, '-')}">
                <strong>${payment_method_name}</strong>
                <label class="form-label d-block">Cheque No</label>
                <input type="text" class="form-control amount-input" data-method="${method}" 
                    name="cheque_no[${method}]"   placeholder="Enter Cheque No"  >
            </div> 
            <div class="col-md-3 input-container" id="input_cheque_date-${method.replace(/\s+/g, '-')}">
                <strong>${payment_method_name}</strong>
                <label class="form-label d-block">Cheque No</label>
                <input type="text" class="form-control amount-input cheque_date_input" data-method="${method}" 
                    name="cheque_date[${method}]"   >
            </div> 
            `;
                        container.append(inputField);
                        flatpickr(".cheque_date_input", {
                            dateFormat: "d/m/Y",
                            defaultDate: "today"
                        });
                    } else {
                        $("#input-" + method.replace(/\s+/g, '-')).remove();
                        $("#input_bank_name-" + method.replace(/\s+/g, '-')).remove();
                        $("#input_cheque_no-" + method.replace(/\s+/g, '-')).remove();
                        $("#input_cheque_date-" + method.replace(/\s+/g, '-')).remove();
                    }

                    payTotalAmountLedger($(this));
                } else if ($(this).is(":checked")) {

                    let inputField = `
            <div class="col-md-3 input-container" id="input-${method.replace(/\s+/g, '-')}">
                <strong>${payment_method_name}</strong>
                <label class="form-label d-block">Amount</label>
                <input type="number" step="0.01" class="form-control amount-input" data-method="${method}" 
                    name="payment_amount[${method}]" min="0" placeholder="Enter amount"  >
            </div>`;
                    container.append(inputField);
                } else {
                    $("#input-" + method.replace(/\s+/g, '-')).remove();
                }

                payTotalAmountLedger($(this));
            });

            $(document).on("input", 'input[name^="payment_amount"]', function() {
                payTotalAmountLedger($(this));
            });
        });
    </script>
    <script>
        flatpickr("#from_search_date", {
            dateFormat: "d/m/Y",
            defaultDate: "today"
        });


        function focus_amount_fn(element, amount_value, decimals) {
            var amount = $('#receipt-amount').val() || 0;
            $(element).val(parseFloat(amount_value).toFixed(decimals));
            // checkTotalAmount(element, amount);
            payTotalAmount(element);
        }
    </script>
    <script>
        $('input[name="isAdvance"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('#items_table').addClass('d-none');
                $('#advance_ref').removeClass('d-none');
            } else {
                $('#items_table').removeClass('d-none');
                $('#advance_ref').addClass('d-none');

            }
        });
    </script>
    <script>
        $('select[name="voucher_type"]').on('change', function() {
            var receipt_id = $(this).val();
            var container = $('#payment_method');

            if (receipt_id) {
                $.ajax({
                    url: "{{ route('get_voucher_type_id', ':id') }}".replace(':id', receipt_id),
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        if (data.receipt_settings && data.receipt_settings.id) {
                            $.ajax({
                                url: "{{ route('get_receipt_no') }}",
                                type: "GET",
                                data: {
                                    id: data.receipt_settings.id
                                },
                                success: function(receiptNoData) {
                                    $('input[name="receipt_ref"]').val(receiptNoData);
                                    if (Array.isArray(data.receipt_settings.main_ledgers) &&
                                        data.receipt_settings.main_ledgers.length > 0) {
                                        let unitHtml = '';
                                        container.empty();
                                        data.receipt_settings.main_ledgers.forEach(
                                            main_ledger => {
                                                unitHtml += `
        <li>
            <input type="checkbox" class="payment-checkbox mt-1" name="payment_method[]" 
                value="${main_ledger.id}"> ${main_ledger.name}
        <input type="hidden" name="payment_method_name"
                                                    value="${main_ledger.name}"> </li>`;
                                            });

                                        container.append(unitHtml);
                                    } else {

                                    }

                                },
                                error: function(xhr, status, error) {
                                    console.error('Error in receiptNo:', error);
                                }
                            });
                        }
                    },

                    error: function(xhr, status, error) {
                        console.error('Error occurred:', error);
                        container.empty();
                    }
                });
            } else {
                container.empty();
            }
        });
    </script>

    <script>
        document.getElementById('receipt-amount').addEventListener('input', function() {
            const max = parseFloat(this.max);
            const value = parseFloat(this.value);
            if (value > max) {
                this.value = max;
                alert('⚠️ The value cannot exceed the total invoice.(' + max + ')');
            }
        });
    </script>
    {{-- <script>
        // $(document).ready(function() {
        //     let container = $("#payment-fields");


        //     $(document).on("change", ".payment-checkbox", function() {
        //         let receiptAmount = parseFloat($("#receipt-amount").val());
        //         let method = $(this).val();
        //         let payment_method_name = $(this).closest('li').find("input[name='payment_method_name']").val();

        //         if ($(this).is(":checked")) {
        //             let inputField = `
    //     <div class="col-md-3 input-container" id="input-${method.replace(/\s+/g, '-')}">
    //         <strong>${payment_method_name}</strong>
    //         <label class="form-label d-block">Amount</label>
    //         <input type="number" class="form-control amount-input" data-method="${method}" 
    //             name="payment_amount[${method}]" min="0" placeholder="Enter amount">
    //     </div>`;
        //             container.append(inputField);
        //         } else {
        //             $("#input-" + method.replace(/\s+/g, '-')).remove();
        //             payTotalAmountLedger($(this));
        //         }
        //     });  
        // });
    </script> --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
