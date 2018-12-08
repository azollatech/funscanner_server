{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Add New Activity')

@section('content_header')
    <h1>Add New Activity</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-body">
                        <div class="add-shop">
                            <form action="{{url('/')}}/admin/add-shop" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row mt-5">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Upload Image</label>
                                                    <div class="input-group btn-group">
                                                        <span class="btn btn-primary">
                                                            <span class="btn-file">
                                                                Browse <input type="file" id="imgInp" name="activity_photo">
                                                            </span>
                                                        </span>
                                                        <input type="text" class="form-control" style="width: 320px;" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div style="display: inline-block; margin-top: 8px;">
                                                        <div>Square Thumbnail</div>
                                                        <img class="img-upload" id='img-upload' src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="/>
                                                    </div>
                                                    {{-- <img class="img-upload" id='img-upload-full' src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="/> --}}
                                                    <div style="display: inline-block; margin-top: 8px;">
                                                        <div>Rectangular Thumbnail</div>
                                                        <img class="img-upload" id='img-upload-rect' src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="vertical-align: top;">
                                        <div class="form-group">
                                            <label>Activity Name</label>
                                            <input type="text" class="form-control" name="activity_name">
                                        </div>
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="text" class="form-control" name="price">
                                        </div>
                                        <div class="form-group">
                                            <label>Details</label>
                                            <textarea class="form-control" rows="3" name="details"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea class="form-control" rows="3" name="address"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>District</label>
                                            <select class="form-control" name="district_id">
                                                <option selected disabled>Select a district</option>
                                                @foreach ($districts as $district)
                                                    <option value="{{$district->district_id}}">{{$district->district_zh_hk}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="vertical-align: top;">
                                        <div class="form-group">
                                            <label>Traffic</label>
                                            <input type="text" class="form-control" name="traffic">
                                        </div>
                                        <div class="form-group">
                                            <label>Must Know</label>
                                            <textarea class="form-control" rows="3" name="must_know"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Duration</label>
                                            <input type="text" class="form-control" name="duration">
                                        </div>
                                        <div class="form-group">
                                            <label>Opening Hours Weekdays</label>
                                            <div>
                                                <input type="text" class="form-control" name="weekdays_open_hour" placeholder="h" style="display: inline-block; width: 64px;">:
                                                <input type="text" class="form-control" name="weekdays_open_mins" placeholder="m" style="display: inline-block; width: 64px;">
                                                <input type="text" class="form-control" name="weekdays_open_apm" placeholder="am/pm" style="display: inline-block; width:80px;"> -
                                                <input type="text" class="form-control" name="weekdays_close_hour" placeholder="h" style="display: inline-block; width: 64px;">:
                                                <input type="text" class="form-control" name="weekdays_close_mins" placeholder="m" style="display: inline-block; width: 64px;">
                                                <input type="text" class="form-control" name="weekdays_close_apm" placeholder="am/pm" style="display: inline-block; width:80px;">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Opening Hours Saturdays</label>
                                            <div>
                                                <input type="text" class="form-control" name="sats_open_hour" placeholder="h" style="display: inline-block; width: 64px;">:
                                                <input type="text" class="form-control" name="sats_open_mins" placeholder="m" style="display: inline-block; width: 64px;">
                                                <input type="text" class="form-control" name="sats_open_apm" placeholder="am/pm" style="display: inline-block; width:80px;"> -
                                                <input type="text" class="form-control" name="sats_close_hour" placeholder="h" style="display: inline-block; width: 64px;">:
                                                <input type="text" class="form-control" name="sats_close_mins" placeholder="m" style="display: inline-block; width: 64px;">
                                                <input type="text" class="form-control" name="sats_close_apm" placeholder="am/pm" style="display: inline-block; width:80px;">
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="sats_closed">
                                                <label class="form-check-label">Closed</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Opening Hours Sundays</label>
                                            <div>
                                                <input type="text" class="form-control" name="suns_open_hour" placeholder="h" style="display: inline-block; width: 64px;">:
                                                <input type="text" class="form-control" name="suns_open_mins" placeholder="m" style="display: inline-block; width: 64px;">
                                                <input type="text" class="form-control" name="suns_open_apm" placeholder="am/pm" style="display: inline-block; width:80px;"> -
                                                <input type="text" class="form-control" name="suns_close_hour" placeholder="h" style="display: inline-block; width: 64px;">:
                                                <input type="text" class="form-control" name="suns_close_mins" placeholder="m" style="display: inline-block; width: 64px;">
                                                <input type="text" class="form-control" name="suns_close_apm" placeholder="am/pm" style="display: inline-block; width:80px;">
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="suns_closed">
                                                <label class="form-check-label">Closed</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- ./box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Bootstrap upload */
        .btn-file {
            position: relative;
            overflow: hidden;
        }
        .btn-file input[type=file] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            background: white;
            cursor: inherit;
            display: block;
        }
        .img-upload {
            margin-top: 2px;
            vertical-align: top;
            border: 1px solid lightgrey;
            background: #f5f5f5;
        }
        #img-upload{
            width: 320px;
            height: 320px;
            object-fit: cover;
        }
        #img-upload-full{
            width: 320px;
            height: 320px;
            object-fit: contain;
        }
        #img-upload-rect{
            width: 360px;
            height: 140px;
            object-fit: cover;
        }
    </style>
@stop

@section('js')
    {{-- Bootstrap Upload --}}
    <script>
        $(document).ready( function() {
            $(document).on('change', '.btn-file :file', function() {
            var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [label]);
            });

            $('.btn-file :file').on('fileselect', function(event, label) {

                var input = $(this).parents('.input-group').find(':text'),
                    log = label;

                if( input.length ) {
                    input.val(log);
                } else {
                    if( log ) alert(log);
                }

            });
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#img-upload').attr('src', e.target.result);
                        $('#img-upload-full').attr('src', e.target.result);
                        $('#img-upload-rect').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#imgInp").change(function(){
                readURL(this);
            });
        });
    </script>
@stop
