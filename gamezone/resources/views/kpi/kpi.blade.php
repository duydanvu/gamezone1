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
                    <li class="breadcrumb-item "><a href="/">Home</a></li>
                    <li class="breadcrumb-item "><a href="#">KPI</a></li>
                    <li class="breadcrumb-item active">KPI Đồng bộ thuê bao</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
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
            <table id="example" class="table table-bordered table-striped " style="width: 100%">
                <thead>
                <tr>
                    <th style="width:5%;text-align: center" >Tổng số</th>
                    <th style="width:15%;text-align: center" >Thuê Bao Đăng Ký</th>
                    <th style="width:15%;text-align: center" >Thuê Bao Hủy Đăng Ký</th>
                    <th style="width:15%;text-align: center" >Thuê Bao Phát Sinh Cước</th>
                    <th style="width:15%;text-align: center">Thuê Bao Gia Hạn</th>
                    <th style="width:15%;text-align: center">Số Đăng Ký</th>
                </tr>
                </thead>
                <tbody id="table_body">
                    {{--@foreach($total as $value)--}}
                        <tr>
                            <td>{{$total->acc_sum}}</td>
                            <td>{{$total->acc_sub}} ~ {{round( ($total->acc_sub/$total->acc_sum)*100 ,3) }} %</td>
                            <td>{{$total->acc_unsub}} ~ {{round( ($total->acc_unsub/$total->acc_sum)*100 ,3) }} %</td>
                            <td>{{$total->acc_psc}} ~ {{round( ($total->acc_psc/$total->acc_sum)*100 ,3) }} %</td>
                            <td>{{$total->acc_gh}} ~ {{round( ($total->acc_gh/$total->acc_sum)*100 ,3) }} %</td>
                            <td>{{$total->acc_dk}} ~ {{round( ($total->acc_dk/$total->acc_sum)*100 ,3) }} %</td>
                        </tr>
                    {{--@endforeach--}}


                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thuê Bao Đăng Ký</h3>
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
                    <th style="width:15%;text-align: center" >Thuê Bao Đăng Ký</th>
                    <th style="width:15%;text-align: center" >Thời Gian</th>
                    <th style="width:15%;text-align: center" >Gói Cước</th>
                    <th style="width:15%;text-align: center">Loại</th>
                </tr>
                </thead>
                <tbody id="table_body">
                @foreach($reg_tran as $key => $value)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$value->isdn}}</td>
                    <td>{{$value->reg_datetime}}</td>
                    <td>{{$value->package_code}}</td>
                    <td>{{$value->type}}</td>
                </tr>
                @endforeach


                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thuê Bao Hủy Đăng Ký</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example2" class="table table-bordered table-striped " style="width: 100%">
                <thead>
                <tr>
                    <th style="width:5%;text-align: center" >STT</th>
                    <th style="width:15%;text-align: center" >Thuê Bao Hủy Đăng Ký</th>
                    <th style="width:15%;text-align: center" >Thời Gian</th>
                    <th style="width:15%;text-align: center" >Gói Cước</th>
                    <th style="width:15%;text-align: center">Loại</th>
                </tr>
                </thead>
                <tbody id="table_body">
                @foreach($unreg_tran as $key => $value)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>{{$value->isdn}}</td>
                        <td>{{$value->reg_datetime}}</td>
                        <td>{{$value->package_code}}</td>
                        <td>{{$value->type}}</td>
                    </tr>
                @endforeach


                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thuê Bao Phát Sinh Cước</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example3" class="table table-bordered table-striped " style="width: 100%">
                <thead>
                <tr>
                    <th style="width:5%;text-align: center" >STT</th>
                    <th style="width:15%;text-align: center" >Thuê Bao Phát Sinh Cước</th>
                    <th style="width:15%;text-align: center" >Thời Gian</th>
                    <th style="width:15%;text-align: center" >Gói Cước</th>
                    <th style="width:15%;text-align: center">Loại</th>
                </tr>
                </thead>
                <tbody id="table_body">
                @foreach($unreg_tran as $key => $value)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>{{$value->isdn}}</td>
                        <td>{{$value->reg_datetime}}</td>
                        <td>{{$value->package_code}}</td>
                        <td> Phát Sinh Cước</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thuê Bao Gia Hạn</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example4" class="table table-bordered table-striped " style="width: 100%">
                <thead>
                <tr>
                    <th style="width:5%;text-align: center" >STT</th>
                    <th style="width:15%;text-align: center" >Thuê Bao Phát Sinh Cước</th>
                    <th style="width:15%;text-align: center" >Thời Gian</th>
                    <th style="width:15%;text-align: center" >Gói Cước</th>
                    <th style="width:15%;text-align: center">Loại</th>
                </tr>
                </thead>
                <tbody id="table_body">
                @foreach($unreg_tran as $key => $value)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>{{$value->isdn}}</td>
                        <td>{{$value->reg_datetime}}</td>
                        <td>{{$value->package_code}}</td>
                        <td> Gia Hạn</td>
                    </tr>
                @endforeach


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
                    url:'{{route('search_date_time')}}',
                    data:dt,
                    success:function(resultData){
                        // // $('.effort').val(resultData);
                        // $('#table_body').html(resultData);
                        console.log(resultData);
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

            $("#example2").DataTable({
                aoColumnDefs: [
                    {
                        bSortable: false,
                        aTargets: ['noSort']
                    } // Disable sorting on columns marked as so
                ]
            });
            // fix table
            $("#example2").parent().css({"overflow": "auto"});

            $("#example3").DataTable({
                aoColumnDefs: [
                    {
                        bSortable: false,
                        aTargets: ['noSort']
                    } // Disable sorting on columns marked as so
                ]
            });
            // fix table
            $("#example3").parent().css({"overflow": "auto"});

            $("#example4").DataTable({
                aoColumnDefs: [
                    {
                        bSortable: false,
                        aTargets: ['noSort']
                    } // Disable sorting on columns marked as so
                ]
            });
            // fix table
            $("#example4").parent().css({"overflow": "auto"});
        });


    </script>

@stop
