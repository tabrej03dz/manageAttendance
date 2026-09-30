@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="container mt-4">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Organisations</h3>
                                <a href="{{ route('organisation.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus-circle"></i> Create New
                                </a>
                            </div>
                        </div>
                
                        <div class="card-body">
                            <!-- Search Bar -->
                            <form action="" method="GET" class="mb-3">
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control" placeholder="Search organisations..." value="{{ request('keyword') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </form>
                
                            <!-- Organisation Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>Logo</th>
                                            <th>Company Name</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>GST Details</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($organisationData as $organisation)
                                            <tr>
                                                
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if($organisation->company_logo)
                                                        <img src="{{ asset('storage/' . $organisation->company_logo) }}" alt="Logo" width="50">
                                                    @else
                                                        <span class="text-muted">No Logo</span>
                                                    @endif
                                                </td>
                                                <td>{{ $organisation->company_name }}</td>
                                                <td>{{ $organisation->company_email ?? 'N/A' }}</td>
                                                <td>{{ $organisation->address ?? 'N/A'}}</td>
                                                <td>{{ $organisation->gst_details ?? 'N/A'}}</td>
                                                <td>
                                                    <span class="badge {{ $organisation->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ ucfirst($organisation->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('organisation.edit', $organisation->id) }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <form action="{{ route('organisation.delete', $organisation->id) }}" method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-muted">No records found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                
                        <div class="card-footer">
                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $organisationData->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Delete Confirmation Script -->
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        document.querySelectorAll(".delete-form").forEach(form => {
                            form.addEventListener("submit", function (event) {
                                if (!confirm("Are you sure you want to delete this organisation?")) {
                                    event.preventDefault();
                                }
                            });
                        });
                    });
                </script>
                
            </div>
        </div>
    </div>
@endsection
