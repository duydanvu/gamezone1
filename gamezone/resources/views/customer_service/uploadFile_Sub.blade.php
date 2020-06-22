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
                    <li class="breadcrumb-item active">Đăng ký / Hủy Đăng Ký theo danh sách</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary-dashboard">
        <div class="card-header">
            <h3 class="card-title">Đăng ký theo danh sách</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <form action="{{ url('cussv/upload_sub/po') }}" enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}
                        <p>Gói Cước :</p>
                        <input type="checkbox" id="day" name="day" value="day">
                        <label for="day">Gói Cước Ngày</label><br>
                        <input type="checkbox" id="week" name="week" value="week">
                        <label for="week">Gói Cước Tuần</label><br>
                        <input type="file" name="filesTest" required="true">
                        <p></p>
                        <br/>
                        <input type="submit" value="Upload">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-outline card-primary-dashboard">
        <div class="card-header">
            <h3 class="card-title">Hủy đăng ký theo danh sách</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <form action="{{ url('cussv/upload_unsub/po') }}" enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}
                        <p>Upload File :</p>
                        <input type="file" name="filesTest1" required="true">
                        <p></p>
                        <br/>
                        <input type="submit" value="Upload">
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@stop

@section('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript">
        $('input[type="checkbox"]').on('change', function() {
            $('input[type="checkbox"]').not(this).prop('checked', false);
        });
    </script>
@stop
