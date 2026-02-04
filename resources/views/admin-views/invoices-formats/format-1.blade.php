<h2>Invoice #{{ $invoice->invoice_number }}</h2>

<p>Tenant: {{ $invoice->tenant->name ?? '' }}</p>
<p>Date: {{ $invoice->invoice_date }}</p>

<table border="1" width="100%">
    <tr>
        <th>Service</th>
        <th>Total</th>
    </tr>

    @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->category }}</td>
            <td>{{ $item->total }}</td>
        </tr>
    @endforeach
</table>

<h3>Total: {{ $invoice->total }}</h3>
