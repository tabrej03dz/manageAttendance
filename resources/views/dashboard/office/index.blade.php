@extends('dashboard.layout.root')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <h2 class="mb-4 d-inline-block">Offices</h2>
            <a href="{{ route('office.create') }}"
               class="btn btn-primary ml-2 mb-2 mb-sm-0">Create</a>
            {{--        <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"--}}
            {{--           class="btn btn-danger ml-2 mb-2 mb-sm-0">Check Out</a>--}}
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Radius</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($offices as $office)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{$office->name}}</td>
                        <td>{{$office->latitude}}</td>
                        <td>{{$office->longitude}}</td>
                        <td>{{$office->radius}}</td>
                        <td>
                            <a href="{{route('office.edit', ['office' => $office->id])}}" class="btn btn-primary">Edit</a>
                            <a href="{{route('office.delete', ['office' => $office->id])}}" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                @endforeach
                <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>
@endsection
