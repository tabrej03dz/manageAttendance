@extends('dashboard.layout.root')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <h2>Create Office</h2>
            <form action="{{ route('office.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Office Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="text" class="form-control" id="latitude" name="latitude" required>
                </div>
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="text" class="form-control" id="longitude" name="longitude" required>
                </div>
                <div class="form-group">
                    <label for="radius">Radius</label>
                    <input type="text" class="form-control" id="radius" name="radius">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection
