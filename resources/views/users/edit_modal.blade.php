    {{-- @csrf --}}
    <div class="modal-body">
        <input type="hidden" name="id" id="edit_id" value="{{ $data['id'] }}">
        <div class="form-group mb-3">
            <label>First Name</label>
            <input type="text" class="form-control" name="first_name" id="edit_first_name" value="{{ $data['first_name'] }}" required>
        </div>
        <div class="form-group mb-3">
            <label>Last Name</label>
            <input type="text" class="form-control" name="last_name" id="edit_last_name" value="{{ $data['last_name'] }}" required>
        </div>
        <div class="form-group mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" id="edit_email" value="{{ $data['email'] }}" required>
        </div>
        <div class="form-group mb-3">
            <label>Phone</label>
            <input type="text" class="form-control" name="phone" id="edit_phone" value="{{ $data['phone'] }}" required>
        </div>
        <div class="form-group mb-3">
            <label>Profile Photo</label>
            <input type="file" class="form-control-file" name="image" id="edit_image">
            @if (!empty($data["image"]))
                <img id="preview" src="{{ asset("uploads/" .$data["image"]) }}" alt="Profile Preview" style="max-width: 200px; margin-top: 10px; ">
            @endif
        </div>
    </div>