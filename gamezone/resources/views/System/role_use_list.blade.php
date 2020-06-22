@extends('adminlte::page')
@section('title', 'Pool List')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Admin User</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item "><a href="/home">Home</a></li>
                    <li class="breadcrumb-item "><a href="#">Admin User</a></li>
                    <li class="breadcrumb-item active">Phân quyển</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="button-group-card-header">
                @if($role_use_number == 1)
                <button id = "" type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-list-new-account-not-role"><i class="fas fa-plus-circle"></i> Add Role For New Account </button>
                @endif
                {{--<a href="{{route('export_to_file_csv')}}" class="btn btn-success btn-xs offset-lg-10" style="float: right;">export</a>--}}
            </div>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:10%">Tên Đăng Nhập</th>
                    <th style="width:15%">Địa Chỉ Email</th>
                    <th style="width:15%">Role </th>
                    <th style="width:10%">Action</th>
                </tr>
                </thead>
                <tbody id="table_body">
                @if(count($data) > 0)
                    @foreach($data as $key => $value)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$value->login}}</td>
                            <td>{{$value->email}}</td>
                            <td>{{$value->authority_name}}</td>
                            <td class="text-center">
                                @if($role_use_number == 1)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary">Action</button>
                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <a href="{{route('admin_list_update_role',['id'=> $value->id])}}" data-remote="false"
                                               data-toggle="modal" data-target="#modal-role-action-update" class="btn dropdown-item">
                                                <i class="fas fa-edit">Update Role</i>
                                            </a>
                                            <a href="{{route('admin_delete_role',['id'=> $value->id,'role'=>$value->authority_name])}}"  class="btn dropdown-item">
                                                <i class="fas fa-users"> Delete</i>
                                            </a>
                                        </div>

                                    </div>
                                @endif
                            </td>
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

    {{-- modal --}}
    <div class="modal fade" id="modal-role-action-update">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Action</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form action="{{route('admin_role_update_post_user')}}" method="post">
                    <div class="modal-body">
                        @csrf

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    {{-- modal --}}
    <div class="modal fade" id="modal-list-new-account-not-role">
        <div class="modal-dialog" >
            <div class="modal-content" style="width: 600px">
                <div class="modal-header">
                    <h4 class="modal-title">List Account Not Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th style="width:5%">Tên Đăng Nhập</th>
                            <th style="width:10%">Địa Chỉ Email</th>
                            <th style="width:10%">Action</th>
                        </tr>
                        </thead>
                        <tbody id="table_body">
                        @if(count($acc_not_role) > 0)
                            @foreach($acc_not_role as $key => $value)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$value->login}}</td>
                                    <td>{{$value->email}}</td>
                                    <td class="text-center">
                                        @if($role_use_number == 1)
                                            <a href="{{route('admin_add_role',['id'=> $value->id])}}" data-remote="false"
                                               data-toggle="modal" data-target="#modal-role-action-add" class="btn dropdown-item">
                                                <i class="fas fa-edit">Add Role</i>
                                            </a>
                                        @endif
                                    </td>
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
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{-- modal --}}

    {{-- modal --}}
    <div class="modal fade" id="modal-role-action-add">
        <div class="modal-dialog">
            <div class="modal-content" style="width: 600px">
                <div class="modal-header">
                    <h4 class="modal-title">Add Rloe</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form action="{{route('admin_role_add_new')}}" method="post">
                    <div class="modal-body">
                        @csrf

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@stop

@section('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>

        $("#modal-role-action-update").on("show.bs.modal", function(e) {
            var link = $(e.relatedTarget);
            $(this).find(".modal-body").load(link.attr("href"));
        });
        $("#modal-role-action-add").on("show.bs.modal", function(e) {
            var link = $(e.relatedTarget);
            $(this).find(".modal-body").load(link.attr("href"));
        });

    </script>
@stop
