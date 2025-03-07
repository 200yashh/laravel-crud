<!DOCTYPE html>
<html>
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>       
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Users List</div>
                    <div class="card-body">
                        <button class="btn btn-success mb-3" id="create-user">Create User</button>
                        <table class="table table-bordered" id="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Profile Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create modal  --}}
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">User Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="user_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="first_name" id="first_name"  value="test" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="test" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" id="email" value="test@gmail.com" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="phone" id="phone" value="12345" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Profile Photo</label>
                            <input type="file" class="form-control-file" name="image" id="image">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="create_user" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Edit modal  --}}
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">User Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="user_edit_form" enctype="multipart/form-data">
                    <div id="edit_user_modal_body"></div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="edit_user" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
<script>
    var table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('user.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'first_name', name: 'first_name' },
            { data: 'last_name', name: 'last_name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'image', name: 'image', 
                render: function(data) {
                    return data != null ? '<img src="'+data+'" width="50">' : '';
                }},
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
</script>
<script>
$(document).ready(function() {    

    $('#create-user').click(function() {
        $('#user_form').trigger("reset");
        $('#preview').hide();
        $('#userModal').modal('show');
    });


    $('#create_user').on('click',function() {
        var form = $('#user_form')[0];
        var formData = new FormData(form);
        console.log(formData);

        $.ajax({
            url: "{{ route('user.store') }}",
            type: "POST",
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // console.log(response);
                
                if (response.status) {
                    $('#userModal').modal('hide');
                    table.ajax.reload();
                }
            },
        });
    });

    $(document).on('click', '#edit_user',function() {
        var form = $(document).find('#user_edit_form')[0];
        var formData = new FormData(form);
        
        var id = $("#edit_id").val();
        console.log(formData);
        

        $.ajax({
            url: "{{ route('user.update', ':id') }}".replace(':id', id),
            type: "POST",
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.status) {
                    $('#editUserModal').modal('hide');
                    table.ajax.reload();
                }
            },
        });
    });

});
function userDeleteData(ele) {

    // console.log(ele); 
    if(confirm("Are you sure?")) {
        var id = $(ele).data('id');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                "_token": "{{ csrf_token() }}",
                _method: 'DELETE'
            },
            url: "{!! route('user.destroy',1) !!}",
            success: function(response) {
                table.ajax.reload();
            }
        });
    }
};

function editUserData(ele){
        var id = $(ele).data('id');
        $.ajax({
            type: 'GET',
            dataType: 'json',
            data: {
                id: id,
            },
            url: "{!! route('user.edit',1) !!}",
            success: function(response) {
                if (response.status) {
                    $('#edit_user_modal_body').html(response.html);
                    $('#editUserModal').modal('show');
                }
            }
        });
}
</script>
</body>
</html>
