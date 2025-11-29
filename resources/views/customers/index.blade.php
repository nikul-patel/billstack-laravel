@extends('layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customers')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('customers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Add Customer</a>
    </div>
    <div class="bg-white shadow rounded p-4">
        <table id="customers-table" class="min-w-full stripe hover text-sm">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editTemplate = @json(route('customers.edit', ['customer' => '__ID__']));

            $('#customers-table').DataTable({
                ajax: {
                    url: @json(route('customers.datatable')),
                    dataSrc: 'data',
                },
                columns: [
                    { data: 'name' },
                    { data: 'email', defaultContent: '' },
                    { data: 'phone', defaultContent: '' },
                    { data: 'city', defaultContent: '' },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            const editUrl = editTemplate.replace('__ID__', data);
                            return `<a href="${editUrl}" class="text-blue-600">Edit</a>`;
                        }
                    },
                ],
                pageLength: 10,
            });
        });
    </script>
@endsection
