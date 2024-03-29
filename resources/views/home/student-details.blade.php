@extends('layouts.app-master')
@section('title', 'Track Cert - Students Details')
@section('content')

<div class="body-main-content">
    <div class="pmu-filter-section">
        <div class="pmu-filter-heading">
            <a href="{{ route('Home.students')}}" class="newcourse-btn">Back</a>
        </div>
        <div class="pmu-search-filter wd20">
            <div class="row g-2">
                <div class="col-md-12">
                </div>
            </div>
        </div>
    </div>

    <div class="pmu-content-list">
        <div class="pmu-content">
            <div class="row">
                <div class="col-md-3">
                    <div class="user-side-profile">
                        <div class="side-profile-item">
                            <div class="side-profile-media">
                                @if ($data->profile_image!=null && $data->profile_image!="")
                                <img src="{!! uploadAssets('upload/profile-image/'.$data->profile_image) !!}">
                                @else
                                <img src="{!! assets('assets/superadmin-images/no-image.svg') !!}">
                                @endif
                            </div>
                            <div class="side-profile-text">
                                <h2>{{ ucfirst($data->first_name) }} {{ ucfirst($data->last_name) }}</h2>
                                <p>Student</p>
                            </div>
                        </div>

                        <div class="side-profile-overview-info">
                            <div class="row g-1">
                                <!-- <div class="col-md-12">
                                    <div class="side-profile-total-order">
                                        <div class="side-profile-total-icon">
                                            <img src="{!! assets('assets/superadmin-images/email1.svg') !!}">
                                        </div>
                                        <div class="side-profile-total-content">
                                            <h2>Email Address</h2>
                                            <p>{{ $data->email ?? "NA" }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="side-profile-total-order">
                                        <div class="side-profile-total-icon">
                                            <img src="{!! assets('assets/superadmin-images/buliding-1.svg') !!}">
                                        </div>
                                        <div class="side-profile-total-content">
                                            <h2>Phone No.</h2>
                                            <p>{{ $data->phone ?? "NA" }}</p>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="col-md-12">
                                    <div class="side-profile-total-order">
                                        <div class="side-profile-total-icon">
                                            <img src="{!! assets('assets/superadmin-images/accountstatus.svg') !!}">
                                        </div>
                                        <div class="side-profile-total-content">
                                            <h2>Account Status</h2>
                                            <p>
                                                @if($data->status==0) Pending
                                                @elseif($data->status==1) Active
                                                @elseif($data->status==2) Inactive
                                                @elseif($data->status==3) Rejected
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-9">
                    <div class="pmu-filter-section">
                        <div class="pmu-filter-heading">
                            <h2>Courses</h2>
                        </div>
                        <div class="pmu-search-filter wd80">
                            <form action="">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select class="form-control" name="status">
                                                <option @if(request()->status == "") selected @endif value="">Select Status</option>
                                                <option @if(request()->status == '1') selected @endif value="1">Complete</option>
                                                <option @if(request()->status == '0') selected @endif value="0">Ongoing</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group search-form-group">
                                            <input type="text" class="form-control" name="title" placeholder="Search by course name" value="{{ request()->title ?? '' }}">
                                            <span class="search-icon"><img src="{!! assets('assets/superadmin-images/search-icon.svg') !!}"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="date" name="date" value="{{ request()->date ?? '' }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <a class="cancel-btn" style="padding: 12px 17px;" href="{{ route('Home.student.details', $id) }}"><i class="las la-sync"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <button type="submit" style="padding: 13px 17px;" class="cancel-btn">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="pmu-content-list">
                        <div class="pmu-content">
                            <div class="row">

                                @forelse($course as $val)
                                <div class="col-md-12">
                                    <div class="course-item">
                                        <div class="course-item-inner">
                                            <div class="course-item-image w-25">
                                                <a data-fancybox data-type="iframe" data-src="https://www.facebook.com/plugins/video.php?height=314&href=https%3A%2F%2Fwww.facebook.com%2Fapciedu%2Fvideos%2F203104562693996%2F&show_text=false&width=560&t=0" href="javascript:;">
                                                    <video class="w-100 h-100" controls controlslist="nodownload noplaybackrate" disablepictureinpicture volume>
                                                        <source src="{{ uploadAssets('upload/disclaimers-introduction/' . $val->introduction_image) }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    <!-- <div class="pmu-video-icon"><img src="{!! assets('assets/superadmin-images/video.svg')!!}"></div> -->
                                                </a>
                                            </div>
                                            <div class="course-item-content">
                                                <div class="{{ ($val->status==1) ? 'coursestatus' : 'coursestatus-unpublish' }}"><img src="{!! assets('assets/superadmin-images/tick.svg') !!}">
                                                    @if($val->status==1) Completed Course Successfully @else Ongoing Course @endif
                                                </div>
                                                <h2>{{ $val->title ?? "NA" }}</h2>
                                                <div class="course-price">
                                                    @if($val->buy_price == $val->course_fee)
                                                    ${{ number_format((float)$val->course_fee, 2, '.', '') }}
                                                    @else
                                                    <del style="font-size: 1rem; font-weight: 500;">${{ number_format($val->course_fee,2) ? : 0}}</del> &nbsp; ${{ number_format($val->buy_price,2) ? : 0}}
                                                    @endif
                                                </div>
                                                <div class="chapter-test-info">
                                                    <div class="chapter-text">Chapter {{ $val->chapter_count ?? 0 }}</div>
                                                    <div class="chapter-action"><a href="{{ route('Home.progress.report', ['courseId' => encrypt_decrypt('encrypt', $val->id), 'id' => encrypt_decrypt('encrypt', $id)]) }}">Completion Status</a></div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="course-info-list">
                                            <ul>
                                                <li>
                                                    <div class="course-info-box">
                                                        <div class="course-info-text">Course Buy Date : &nbsp;</div>
                                                        <div class="course-info-value"> {{ date('d M Y', strtotime($val->created_date)) }}</div>
                                                    </div>
                                                </li>

                                                <li>
                                                    <div class="course-info-action">
                                                        <!-- <a href="">Send Invoice to email</a> -->
                                                        <a target="_blank" href="{{ route('Home.download.invoice', encrypt_decrypt('encrypt', $val->order_id)) }}">Download Invoice</a>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="d-flex flex-column align-items-center justify-content-center mt-5 no-record-found">
                                    <div>
                                        <img src="{{ assets('/assets/website-images/nodata.svg') }}" alt="">
                                    </div>
                                    <div class="font-weight-bold">
                                        <p class="font-weight-bold" style="font-size: 1.2rem;">No record found </p>
                                    </div>
                                </div>
                                @endforelse

                                <div class="pmu-table-pagination">
                                    {{$course->appends(Request::except('page'))->links('pagination::bootstrap-4')}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark as In-Active  -->
<div class="modal ro-modal fade" id="markasactive" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="student-info-form-info">
                    <h2>Mark as Active</h2>
                    <p>Are you sure mark as Active Again for " {{ ucfirst($data->first_name)}} {{ ucfirst($data->last_name)}}" Once mark as active creator will have access to
                        his account until and unless revert action has been taken again!</p>
                    <div class="student-info-btn-action">
                        @if ($data->status == 1)
                        <a href="{{ route('Home.InactiveStatus',encrypt_decrypt('encrypt',$data->id)) }}" class="save-btn">Yes! Inactive</a>
                        @else
                        <a href="{{ route('Home.InactiveStatus',encrypt_decrypt('encrypt',$data->id)) }}" class="save-btn">Yes! Active</a>
                        @endif

                        @if ($data->status == 1)
                        <a href="#" class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">No! Keep it Active</a>
                        @else
                        <a href="#" class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">No! Keep it Inactive</a>
                        @endif


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Payment Request  -->
<div class="modal ro-modal fade" id="PaymentRequest" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="PaymentRequest-form-info">
                    <h2>Payment Request</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modal-settled-info-text">
                                <img src="images/dollar-circle.svg">
                                <p>Total Payment earned</p>
                                <h4>1007.55</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modal-settled-info-text">
                                <img src="images/dollar-circle.svg">
                                <p>Average Settled Amount</p>
                                <h4>1007.55</h4>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="modal-bank-info-text">
                                <p>Creator Cash-Out Options</p>
                                <div class="modal-added-plan-type">Weekly</div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="modal-bank-info-text">
                                <p>Bank Name</p>
                                <h4>ICICI Bank LTD</h4>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="modal-bank-info-text">
                                <p>Account Number</p>
                                <h4>98374598734949</h4>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="modal-bank-info-text">
                                <p>Routing Number</p>
                                <h4>98374598734949</h4>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="modal-request-section">
                                <div class="modal-request-head">
                                    <h1>PAYMENT REQUEST <span>(02)</span></h1>
                                </div>
                                <div class="modal-request-body">
                                    <div class="modal-request-list">
                                        <div class="modal-request-item">
                                            <div class="modal-request-text">
                                                <p>Requested amount</p>
                                                <h3>$ 900.55</h3>
                                            </div>
                                            <div class="modal-request-action">
                                                <a class="approve-btn" href="#"><img src="images/approve.svg"></a>
                                                <a class="reject-btn" href="#"><img src="images/reject.svg"></a>
                                            </div>
                                        </div>
                                        <div class="modal-request-item">
                                            <div class="modal-request-text">
                                                <p>Requested amount</p>
                                                <h3>$ 900.55</h3>
                                            </div>
                                            <div class="modal-request-action">
                                                <a class="approve-btn" href="#"><img src="images/approve.svg"></a>
                                                <a class="reject-btn" href="#"><img src="images/reject.svg"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Edit courses  -->
<div class="modal ro-modal fade" id="Editcourses" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="PaymentRequest-form-info">
                    <h2>Payment Request</h2>
                    <div class="row">

                        <div class="col-md-12">
                            <div class="modal-settled-info-text">
                                <img src="images/dollar-circle.svg">
                                <p>Total Course Payment Received</p>
                                <h4>3207.55</h4>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="modal-settled-info-text">
                                <img src="images/dollar-circle.svg">
                                <p>Total Creator Settled Payment May, 2023</p>
                                <h4>2234.65</h4>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="date" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="Setfee-box">
                                    <h4>Set fee% for every Course Purchase</h4>
                                    <div class="grant-progress ye-progress">
                                        <div class="grant-use-text">
                                            <span>2%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 2%" aria-valuenow="2" aria-valuemin="0" aria-valuemax="10"></div>
                                        </div>
                                    </div>
                                </div>
                                <input type="text" class="form-control" placeholder="Custom %">
                                <div class="note">On every Course Purchases Creator will get the % revenue Cut</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="cancel-btn" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                <button class="save-btn">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection