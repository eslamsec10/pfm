<p>Hello Dear {{ $tenant->name ?? $tenant->company_name }}</p>
<p>{{ $title }} in {{ $company->name }}</p>
<p>{{ $message }}</p>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Unit Name</th>
            <th>Rent</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($table as $row)
        <tr>
            <td>{{ $row['unit'] }}</td>
            <td>{{ $row['rent'] }}</td>
            <td>{{ $row['date'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
