@extends('dashboard.layout.root')
@section('content')
    <div class="container mt-5">
        <h2>Edit</h2>
        <form action="{{ route('office.update', ['office' => $office->id]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Office Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{$office->name}}" required>
            </div>
            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="text" class="form-control" id="latitude" name="latitude" value="{{$office->latitude}}" required>
            </div>
            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="text" class="form-control" id="longitude" name="longitude" value="{{$office->longitude}}" required>
            </div>
            <div class="form-group">
                <label for="radius">Radius</label>
                <input type="text" class="form-control" id="radius" name="radius" value="{{$office->radius}}">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
