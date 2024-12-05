@extends('dashboard.layout.root', ['title' => 'Permissions'])
@section('content')

    <!-- /.card -->
    <div class="card">
            <div class="card-header">
                <h3 class="card-title">Permissions</h3>
{{--                @can('create permission')--}}
                <a href="{{route('permission.create')}}" class="btn btn-primary">Create Permission</a>
{{--                @endcan--}}
            </div>
            <!-- /.card-header -->
        <form action="{{route('permission.give')}}" method="post">
            @csrf
            <div class="card-body">
                <div class="form-row align-items-center">
{{--                    @can('give permission to user')--}}
                    <div class="col-12 col-sm-auto mb-md-3">
                        <label for="userSelect" class="col-form-label">Select User</label>
                        <select class="form-control" id="userSelect" name="user_id">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
{{--                    @endcan--}}
{{--                    @can('give permission to role')--}}
                    <div class="col-12 col-sm-auto mb-md-3">
                        <label for="roleSelect" class="col-form-label">Select Role</label>
                        <select class="form-control" id="roleSelect" name="role_id">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
{{--                    @endcan--}}
                    <div class="col-12 col-sm-auto mt-sm-3 mt-md-4 mt-4">
{{--                        @can('give permission')--}}
                        <input type="submit" value="Give Permission" class="btn btn-success btn-block btn-sm-inline">
{{--                        @endcan--}}
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">All</label>
                            </div>
                        </th>
                        <th>Permission name</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" name="permissions[]" value="{{$permission->name}}" type="checkbox" id="{{$permission->id}}" >
                                    <label class="form-check-label"></label>
                                </div>
                            </td>
                            <td>
                                <label class="form-check-label" for="{{$permission->id}}">{{$permission->name}}</label>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </form>
    </div>
    <!-- /.card -->


    <!-- page script -->
    <script>

        function selectAll(){
            alert(this.value);
        }

        $(function () {
            $("#example1").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');

            selectAllCheckbox.addEventListener('change', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        });
    </script>
@endsection
