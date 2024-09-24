@extends('dashboard.layout.root')
@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-head">
                    <h2 class="text-center mb-4">Edit Holiday</h2>
                </div>
                <div class="card-body">
                    <form action="{{route('off.update', ['off' => $off->id])}}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{$off->title}}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{$off->date}}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            @if($offices)
                                <div class="col-md-6">
                                    <label for="office_id" class="form-label">Designation</label>
                                    <select name="office_id" id="" class="form-control">
                                        <option value="">Select Office</option>
                                        @foreach($offices as $office)
                                            <option value="{{$office->id}}" {{$off->office_id == $office->id ? 'selected' : ''}}>{{$office->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="2">{{$off->description}}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
