{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Add New Activity')

@section('content_header')
    <h1>Add New Activity</h1>
@stop

@section('content')
    <div class="container py-5">

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
                                                Browse <input type="file" id="imgInp" name="shop_photo">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <img class="img-upload" id='img-upload' src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="/>
                                    <img class="img-upload" id='img-upload-full' src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="/>
                                    <img class="img-upload" id='img-upload-rect' src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6" style="vertical-align: top;">
                        <div class="form-group">
                            <label>Shop Name</label>
                            <input type="text" class="form-control" name="shop_name">
                        </div>
                        <div class="form-group">
                            <label>Shop Name (en)</label>
                            <input type="text" class="form-control" name="shop_name_en">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" class="form-control" name="address">
                        </div>
                        <div class="form-group">
                            <label>Address (en)</label>
                            <input type="text" class="form-control" name="address_en">
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
                        <div class="form-group">
                            <label>Shop Type</label>
                            <select class="form-control" name="shop_type_id">
                                <option selected disabled>Select a shop type</option>
                                @foreach ($shop_types as $shop_type)
                                    <option value="{{$shop_type->shop_type_id}}">{{$shop_type->shop_type_zh_hk}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" rows="7" name="shop_description"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                    </div>
                    <div class="col-md-6" style="vertical-align: top;">
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
                        <div class="form-group">
                            <label>Facebook Username</label>
                            <input type="text" class="form-control" name="fb_username">
                        </div>
                        <div class="form-group">
                            <label>Twitter Username</label>
                            <input type="text" class="form-control" name="tw_username">
                        </div>
                        <div class="form-group">
                            <label>Instagram Username</label>
                            <input type="text" class="form-control" name="ig_username">
                        </div>
                        <div class="form-group">
                            <label>Website URL</label>
                            <input type="text" class="form-control" name="website_url">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Add Shop</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
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
