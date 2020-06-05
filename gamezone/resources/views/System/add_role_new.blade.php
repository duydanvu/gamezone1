@csrf
<input name="user_id" value="{{$data->id}}" hidden/>
<div class="row">
    <div class="col-sm-12">
        <div class="card-body">
            <h1>Update Role </h1>
            <h4>Account {{$data->login}}</h4>
            <h4>Email : {{$data->email}}</h4>
            <p></p>
            <input type="checkbox" id="roleAdmin" name="roleAdmin" value="ROLE_ADMIN" >
            <label for="roleAdmin"> Role Admin</label><br>
            <input type="checkbox" id="roleUser" name="roleUser" value="ROLE_USER" >
            <label for="roleUser"> Role User</label><br>
        </div>
    </div>
</div>
<script>

</script>