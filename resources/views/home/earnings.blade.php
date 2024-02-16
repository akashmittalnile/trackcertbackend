@extends('layouts.app-master')
@section('title', 'Track Cert - Earnings')
@section('content')
    <div class="body-main-content">
        <div class="pmu-filter-section">
            <div class="pmu-filter-heading">
                <h2>Earnings</h2>
            </div>
            <div class="pmu-search-filter wd80">
                <form action="">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group search-form-group">
                                <input type="text" class="form-control" name="name" placeholder="Enter Name here....." value="{{ request()->name }}">
                                <span class="search-icon"><img src="{!! assets('assets/superadmin-images/search-icon.svg') !!}"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group search-form-group">
                                <input type="text" class="form-control" name="number" placeholder="Enter Order Number here....." value="{{ request()->number }}">
                                <span class="search-icon"><img src="{!! assets('assets/superadmin-images/search-icon.svg') !!}"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="date" name="order_date" class="form-control" value="{{ request()->order_date }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="download-btn" style="padding: 12px 0px;" type="">Search</button>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <a href="{{ route('Home.earnings') }}" style="padding: 12px 0px;" class="download-btn"><i class="las la-sync"></i></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="pmu-content-list">
            <div class="pmu-table-content">
                <div class="pmu-card-table pmu-table-card">
                    <div class="pmu-table-filter">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="pmu-table-info-card">
                                    <h2>Total Earning</h2>
                                    <div class="pmu-table-value total_earning">${{ number_format((float)$payment - $requestedAmount, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="pmu-table-info-card">
                                    <h2>My Wallet</h2>
                                    <div class="pmu-table-value">${{ number_format((float)$mymoney, 2) }}</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="pmu-table-form-card">
                                    <a href="{{ route('Home.payment.request') }}" class="download-btn">Payment Request List</a>
                                </div>
                            </div>
                            @php
                                $full = url()->full();
                                $current = url()->current();
                                $arr = explode($current, $full);
                                $str = '';
                                for ($i = 1; $i < strlen($arr[1]); $i++) {
                                    $str .= $arr[1][$i];
                                }
                                $arr[1] = $str;
                            @endphp
                            <div class="col-md-3">
                                <div class="pmu-table-form-card">
                                    <a href="{{ route('Home.download.earnings', $arr[1]) }}" class="download-btn">Download Earning Log</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Name</th>
                                <th>Order Number</th>
                                <th>Date Of Payment</th>
                                <th>Payment Mode</th>
                                <th>Earning</th>
                                <th>Total fees paid</th>
                                <th>Status</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count=0;?>
                            @forelse($orders as $index => $val)
                            <tr>
                                <td><span class="sno">{{ $index+1 }}</span> </td>
                                <td class="text-capitalize">{{ $val->first_name ?? "NA" }} {{ $val->last_name }}</td>
                                <td>{{ $val->order_number ?? "NA" }}</td>
                                <td>{{ date('d M, Y H:i A', strtotime($val->created_date)) }}</td>
                                <td>STRIPE</td>
                                <td>${{ number_format((float)($val->amount-$val->admin_amount), 2) }}</td>
                                <td>${{ number_format((float)$val->amount, 2) }}</td>
                                <td>{{ ($val->status == 1) ? "Payment Done" : "Payment Pending" }}</td>
                                <td>
                                    <a title="Order Details" href="{{ route('Home.order.details', encrypt_decrypt('encrypt', $val->order_id)) }}" style="padding: 12px 0px;" class="download-btn"><i class="las la-eye"></i></a>
                                </td>
                            </tr>
                            <?php  $count += ($val->amount-$val->admin_amount) ?>
                            @empty
                            <tr class="text-center">
                                <td colspan="8">No record found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="pmu-table-pagination">
                        {{$orders->appends(Request::except('page'))->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        a:hover{
            color: #fff;
        }
    </style>
@endsection
