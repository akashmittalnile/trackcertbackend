@extends('super-admin-layouts.app-master')
@section('title', 'Track Cert - Students')
@section('content')
    <div class="body-main-content">
        <div class="pmu-filter-section">
            <div class="pmu-filter-heading">
                <h2>Students</h2>
            </div>
            <div class="pmu-search-filter wd60">
                <form action="">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <div class="form-group search-form-group">
                                <input type="text" class="form-control" name="name"
                                    placeholder="Search by Student Name" value="{{request()->name}}">
                                <span class="search-icon"><img src="{!! assets('assets/superadmin-images/search-icon.svg')!!}"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option @if(request()->status=="") selected @endif value="">Select Account Status</option>
                                    <option @if(request()->status=="1") selected @endif value="1">Active</option>
                                    <option @if(request()->status=="2") selected @endif value="2">In-Active</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <a class="Accountapproval-btn" style="padding: 13px 20px;" href="{{ route('SA.Students') }}"><i class="las la-sync"></i></a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="Accountapproval-btn" type="">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="creator-table-section">
            <div class="creator-table-list">
                @if($datas->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center mt-5 no-record-found">
                    <div>
                        <img src="{{ assets('/assets/website-images/nodata.svg') }}" alt="">
                    </div>
                    <div class="font-weight-bold">
                        <p class="font-weight-bold" style="font-size: 1.2rem;">No record found </p> 
                    </div>
                </div>
                @elseif(!$datas->isEmpty())
                    @foreach($datas as $data)
                        <div class="creator-table-item">
                            <div class="creator-table-col-4">
                                <div class="creator-table-content">
                                    <div class="creator-profile-info">
                                        <div class="creator-profile-image">
                                            @if ($data->profile_image!=null && $data->profile_image!="")
                                                <img src="{!! uploadAssets('upload/profile-image/'.$data->profile_image) !!}">
                                            @else
                                                <img src="{!! assets('assets/superadmin-images/no-image.svg') !!}">
                                            @endif
                                            
                                        </div>
                                        <div class="creator-profile-text">
                                            <h2>{{ ucfirst($data->first_name) }} {{ ucfirst($data->last_name) }}</h2>
                                            <!-- <p>02 Completed Course</p> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="creator-table-col-3">
                                <div class="creator-table-box">
                                    <div class="creator-table-text">Phone No.</div>
                                    <div class="creator-table-value">+1 {{ $data->phone }}</div>
                                </div>
                            </div>
                            <div class="creator-table-col-2">
                                <div class="creator-table-box">
                                    <div class="creator-table-text">Email ID</div>
                                    <div class="creator-table-value">{{ $data->email }}</div>
                                </div>
                            </div>

                            <div class="creator-table-col-2">
                                <div class="creator-table-box">
                                    <div class="creator-table-text">Account Status</div>
                                    <div class="creator-table-value">@if ($data->status==1) Active @else In-active @endif</div>
                                </div>
                            </div>

                            <div class="creator-table-col-1">
                                <div class="mon-table-box">
                                    <a href="{{ url('super-admin/student-detail/'.encrypt_decrypt('encrypt',$data->id)) }}" class="btn-go">
                                        <img src="{!! assets('assets/superadmin-images/arrow-right.svg') !!}">
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="pmu-table-pagination">
                        {{$datas->appends(Request::except('page'))->links('pagination::bootstrap-4')}}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
