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
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h1>Appointment</h1>
                            {{-- <a href="{{ route('appointment.create') }}" class="btn btn-light">Create new appointment</a> --}}
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="" method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control" placeholder="Search...">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </form>

                        <table class="table table-hover table-bordered shadow-lg">
                            <thead class="thead-dark">
                                <tr class="bg-dark text-white text-center">
                                    <th>ID</th>
                                    <th>Company Name</th>
                                    <th>Owner Name</th>
                                    <th>Number</th>
                                    <th>Email</th>
                                    <th>Company Address</th>
                                    <th>PinCode</th>
                                    <th>Employee Size</th>
                                    <th>Designation</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($appointmentData as $appointment)
                                    <tr class="text-center hover-highlight">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $appointment->compan_name }}</td>
                                        <td>{{ $appointment->owner_name }}</td>
                                        <td>{{ $appointment->number }}</td>
                                        <td>{{ $appointment->email }}</td>
                                        <td>{{ $appointment->company_address }}</td>
                                        <td>{{ $appointment->pin_code }}</td>
                                        <td>{{ $appointment->emp_size }}</td>
                                        <td>{{ $appointment->designation }}</td>
                                        <td>
                                            <a href="{{ route('request.delete', $appointment->id) }}"
                                                class="btn btn-danger btn-sm">
                                                Delete
                                            </a>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">
                        <!-- Pagination links can be added here if needed -->
                        {{ $appointmentData->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
