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
                    <li class="breadcrumb-item "><a href="#">Report</a></li>
                    <li class="breadcrumb-item active">Report Time</li>
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
                    <th style="width:3%" rowspan="2">#</th>
                    <th style="width:10%" rowspan="2">Ngày</th>
                    <th rowspan="2">Thuê Bao Đăng Ký Mới</th>
                    <th colspan="2" style="text-align: center">Thuê Bao Hủy</th>
                    <th rowspan="2">Thuê Bao PCS</th>
                    <th rowspan="2">Thuê Bao Active</th>
                    <th rowspan="2">Tỷ lệ Gia Hạn (%)</th>
                    <th colspan="4" style="text-align: center">Đăng Ký</th>
                </tr>
                <tr>
                    <th>Hệ Thống Hủy</th>
                    <th>Thuê Bao Tự Hủy</th>
                    <th>SMS</th>
                    <th>VASGATE</th>
                    <th>WAP</th>
                    <th>Tổng đăng ký</th>
                </tr>
                </thead>
                <tbody id="table_body">
                <tr>
                    <td> Tổng </td>
                    <td></td>
                    <td>{{$total_sub}}</td>
                    <td>{{$total_unsub_pp}}</td>
                    <td>{{$total_unsub_stm}}</td>
                    <td>{{$total_psc}}</td>
                    <td>{{$total_active}}</td>
                    <td></td>
                    <td>{{$total_dk_sms}}</td>
                    <td>{{$total_dk_vasgate}}</td>
                    <td>{{$total_dk_wap}}</td>
                    <td></td>
                </tr>
                @if ( count($total) > 0)
                        @foreach($total as $key => $value)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$value->date}}</td>
                            <td>{{$value->acc_sub}}</td>
                            <td>{{$value->acc_unsub_pp}}</td>
                            <td>{{$value->acc_unsub_stm}}</td>
                            <td>{{$value->acc_psc}}</td>
                            <td>{{$value->acc_active}}</td>
                            <td>{{round( ($value->acc_gh/$sum)*100 ,3) }} %</td>
                            <td>{{$value->acc_dk_sms}}</td>
                            <td>{{$value->acc_dk_vasgate}}</td>
                            <td>{{$value->acc_dk_wap}}</td>
                            <td>{{$value->acc_dk_sms + $value->acc_dk_vasgate + $value->acc_dk_wap}}</td>
                        </tr>
                        @endforeach
                @else
                    <td colspan="8" style="text-align: center">
                        <h3>Empty Data</h3>
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
            var url = "{{ route ('export_to_file_csv',['datetime' => ":datetime"])}}";
            url = url.replace(':datetime', datetimes);
            console.log(url);
            window.location.href = url;
        })

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
