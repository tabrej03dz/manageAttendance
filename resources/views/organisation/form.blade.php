@extends('dashboard.layout.root')

@section('content')

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h2>{{ isset($organisation) ? 'Edit' : 'Create' }} Organisation</h2>
        </div>
        <div class="card-body">

             <!-- Display All Validation Errors -->
             @if ($errors->any())
             <div class="alert alert-danger">
                 <ul>
                     @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                     @endforeach
                 </ul>
             </div>
         @endif

        

         <form action="{{ isset($organisation) ? route('organisation.update', $organisation->id) : route('organisation.store') }}" 
               method="POST" enctype="multipart/form-data">
             @csrf
             {{-- @if(isset($organisation))
                 @method('PUT')
             @endif --}}
     
             <div class="mb-3">
                 <label class="form-label">Company Logo</label>
                 <input type="file" name="company_logo" class="form-control">
                 @if(isset($organisation) && $organisation->company_logo)
                     <img src="{{ asset('storage/' . $organisation->company_logo) }}" alt="Logo" width="100">
                 @endif
                 @error('company_logo') <small class="text-danger">{{ $message }}</small> @enderror
             </div>
     
             <div class="mb-3">
                 <label class="form-label">Company Name</label>
                 <input type="text" name="company_name" class="form-control" 
                        value="{{ old('company_name', $organisation->company_name ?? '') }}">
                 @error('company_name') <small class="text-danger">{{ $message }}</small> @enderror
             </div>
     
             <div class="mb-3">
                 <label class="form-label">Company Email</label>
                 <input type="email" name="company_email" class="form-control" 
                        value="{{ old('company_email', $organisation->company_email ?? '') }}">
                 @error('company_email') <small class="text-danger">{{ $message }}</small> @enderror
             </div>
     
             <div class="mb-3">
                 <label class="form-label">Address</label>
                 <input type="text" name="address" class="form-control" 
                        value="{{ old('address', $organisation->address ?? '') }}">
                 @error('address') <small class="text-danger">{{ $message }}</small> @enderror
             </div>
     
             <div class="mb-3">
                 <label class="form-label">GST Details</label>
                 <input type="text" name="gst_details" class="form-control" 
                        value="{{ old('gst_details', $organisation->gst_details ?? '') }}">
                 @error('gst_details') <small class="text-danger">{{ $message }}</small> @enderror
             </div>
     
             <div class="mb-3">
                 <label class="form-label">Status</label>
                 <select name="status" class="form-control">
                     <option value="active" {{ old('status', $organisation->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                     <option value="inactive" {{ old('status', $organisation->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                 </select>
                 @error('status') <small class="text-danger">{{ $message }}</small> @enderror
             </div>
     
             <button type="submit" class="btn btn-primary">{{ isset($organisation) ? 'Update' : 'Create' }}</button>
             <a href="{{ route('organisation.index') }}" class="btn btn-secondary">Cancel</a>
         </form>
        </div>
    </div>
</div>

@endsection
