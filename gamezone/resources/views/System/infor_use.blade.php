@csrf
<input name="user_id" value="{{$data->id}}" hidden/>
<div class="row">
    <div class="col-sm-12">
        <div class="card-body">

            <div class="form-group">
                <label for="name">Tên tài khoản</label>
                <input id="name" type="text" class="form-control @error('txtName') is-invalid @enderror" name="txtNumber" value="{{$data->login}}"  autocomplete="number" required>
                @error('txtName')
                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="name">Password </label>
                <input id="password" type="password" class="form-control @error('txtPass') is-invalid @enderror" name="txtPass" value="{{$data->password}}"  autocomplete="number" required>
                @error('txtPass')
                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="name">First Name</label>
                <input id="fName" type="text" class="form-control @error('txtFName') is-invalid @enderror" name="txtFName" value="{{ $data->first_name}}"  autocomplete="number" required>
                @error('txtFName')
                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="name">Last Name</label>
                <input id="lName" type="text" class="form-control @error('txtLName') is-invalid @enderror" name="txtLName" value="{{ $data->last_name }}"  autocomplete="number" required>
                @error('txtLName')
                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="name">Email</label>
                <input id="email" type="text" class="form-control @error('txtEmail') is-invalid @enderror" name="txtEmail" value="{{ $data->email }}"  autocomplete="number" required>
                @error('txtEmail')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>
</div>
<script>

</script>