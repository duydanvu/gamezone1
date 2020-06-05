@csrf
<input name="user_id" value="{{$id}}" hidden/>
<div class="row">
    <div class="col-sm-12">
        <div class="card-body">
            <h1>Update Role </h1>
            <input type="checkbox" id="roleAdmin" name="roleAdmin" value="ROLE_ADMIN" @if($role_admin == 1) checked @endif>
            <label for="roleAdmin"> Role Admin</label><br>
            <input type="checkbox" id="roleUser" name="roleUser" value="ROLE_USER" @if($role_user == 1) checked @endif>
            <label for="roleUser"> Role User</label><br>
        </div>
    </div>
</div>
<script>

</script>