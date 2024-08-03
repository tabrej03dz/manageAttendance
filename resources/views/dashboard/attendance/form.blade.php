@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <h2>Check In Form</h2>
        <form>
            <div class="form-group">
                <label for="captureImage">Capture Image</label>
                <input type="file" id="captureImage" capture="user" accept="image/*" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
