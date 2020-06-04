@csrf
<input name="account_id" value="{{$data->id}}" hidden/>
<input name="account_request" value="{{$data->request}}" hidden/>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group row">
            <label for="staff_id" class="col-md-4 col-form-label text-md-right">{{ __('Trạng thái thuê bao ') }}{{$data->isdn}}</label>

        </div>
    </div>
    <div class="col-sm-12">
        @if( $data -> request == "UNSUB")
            <div class="form-group row">
                <p>Thuê bao đã hủy gói cước.</p><br>
                <p>Tự động gia hạn (nếu không hủy)</p><br>
                <p>Thuê bao có hiệu lực tới {{$data->expire_datetime}}</p>
            </div>
        @else
            <div class="form-group row">
                <p>Thuê bao đã đăng ký gói cước.</p><br>
                <p>Tự động gia hạn </p><br>
                <p>Thuê bao có hiệu lực tới {{$data->expire_datetime}}</p>
            </div>
        @endif
    </div>
</div>
<script>

</script>