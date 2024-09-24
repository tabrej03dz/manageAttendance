@extends('dashboard.layout.root')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <h2 class="mb-4 d-inline-block">Employees</h2>

            <a href="{{ route('off.create') }}" class="btn btn-primary ml-2 mb-2 mb-sm-0">Create</a>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($offs as $off)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $off->title }}</td>
                            <td>{{ $off->date }}</td>
                            <td>{{ $off->description }}</td>
                            <td>
                                <a href="{{ route('off.edit', ['off' => $off->id]) }}"
                                   class="btn btn-primary">Edit</a>
                                <a href="{{ route('off.delete', ['off' => $off->id]) }}"
                                   class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                    <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
