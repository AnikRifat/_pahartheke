<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $customer)
            <tr>
                <td>{{ $customer->user->name }}</td>
                <td>{{ $customer->user->phone }}</td>
                <td>{{ $customer->user->email }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
