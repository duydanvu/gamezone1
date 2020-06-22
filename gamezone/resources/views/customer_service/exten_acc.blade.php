@extends('adminlte::page')
@section('title', 'Pool List')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item "><a href="/home">Home</a></li>
                    <li class="breadcrumb-item "><a href="#">Chăm Sóc Khách Hàng</a></li>
                    <li class="breadcrumb-item active">Lịch sử gia hạn</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary-dashboard">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="card-header">
            <h3 class="card-title">Search</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Search by Time(From-To)</label>
                        <input  type="text" id="date_range" class="form-control date_range @error('idear_start_end') is-invalid @enderror" value="" name="idear_start_end">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer" style="background: transparent;">
            <div class="row">
                <div class="col-12 col-md-12 col-sm-12">
                    <a href=" " type="submit" class="btn btn-default" >Refresh</a>
                    <a type="submit" id="export_data"  class="btn btn-success btn-xs offset-lg-10" style="margin: auto" >Export</a>
                    <button type="submit" id="fillter_date" class="btn btn-primary" style="float: right;">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped " style="width: 100%">
                <thead>
                <tr>
                    <th style="width:5%;text-align: center" >STT</th>
                    <th style="width:10%;text-align: center" >Phone</th>
                    <th style="width:15%;text-align: center" >Thời gian gia dịch</th>
                    <th style="width:20%;text-align: center" >Loại Giao Dịch</th>
                    <th style="width:5%;text-align: center">Gói cước</th>
                    <th style="width:10%;text-align: center" >Trạng Thái</th>
                    <th style="width:10%;text-align: center">Kênh</th>
                    <th style="text-align: center">Cước phí</th>
                </tr>
                </thead>
                <tbody id="table_body">
                @if ( count($exten_acc) > 0)
                    @foreach($exten_acc as $key => $value)
                        <tr>
                            <td>{{$key + 1}}</td>
                            <td>{{$value->isdn}}</td>
                            <td>{{$value->reg_datetime}}</td>
                            <td>{{$value->type}}</td>
                            <td>{{$value->package_code}}</td>
                            <td>{{$value->tt}}</td>
                            <td>{{$value->channel}}</td>
                            <td>{{$value->charge_price}}</td>
                        </tr>
                    @endforeach
                @else
                    <td colspan="8" style="text-align: center">
                        <h3>Empty Pool Action</h3>
                    </td>
                @endif

                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>

@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@stop

@section('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $('.date_range').daterangepicker({
            timePicker: true,
            startDate: moment().startOf('month'),
            endDate: moment(),
            locale: {
                format: 'MM/DD/YYYY'
            }
        });
        $('#export_data').click(function () {
            var datetimes = $('#date_range').val();
            datetimes = datetimes.split('/').join('.');
            datetimes = datetimes.split(' ').join('');
            console.log(datetimes);
            var url = "{{ route ('export_renew_to_file_csv',['datetime' => ":datetime"])}}";
            url = url.replace(':datetime', datetimes);
            console.log(url);
            window.location.href = url;
        });
        $(document).ready(function(){
            $('#fillter_date').click(function () {
                let date_range = $('#date_range').val();
                let _token = $('meta[name="csrf-token"]').val();
                var startEnd = $("#date_range").val();
                var dt = {_token, startEnd};
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type:'POST',
                    url:'{{route('search_date_time_exten_acc')}}',
                    data:dt,
                    success:function(resultData){
                        // // $('.effort').val(resultData);
                        $('#table_body').html(resultData);
                        // console.log(resultData);
                    }
                });
            });
        });
        // Datatable
        $(function () {
            $("#example1").DataTable({
                aoColumnDefs: [
                    {
                        bSortable: false,
                        aTargets: ['noSort']
                    } // Disable sorting on columns marked as so
                ]
            });
            // fix table
            $("#example1").parent().css({"overflow": "auto"});
        });


    </script>

@stop
