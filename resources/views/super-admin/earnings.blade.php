@extends('super-admin-layouts.app-master')
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
                                <input type="text" class="form-control" name="name" placeholder="Search by Name" value="{{ request()->name }}">
                                <span class="search-icon"><img src="{!! assets('assets/superadmin-images/search-icon.svg') !!}"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group search-form-group">
                                <input type="text" class="form-control" name="number" placeholder="Search by Order Number" value="{{ request()->number }}">
                                <span class="search-icon"><img src="{!! assets('assets/superadmin-images/search-icon.svg') !!}"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="date" name="order_date" class="form-control" value="{{ request()->order_date }}">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <a href="{{ route('SA.Earnings') }}" style="padding: 12px 0px;" class="download-btn"><i class="las la-sync"></i></a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="download-btn" style="padding: 12px 0px;" type="">Search</button>
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
                            <div class="col-md-9">
                                <div class="pmu-table-info-card">
                                    <h2>Total Admin Earning</h2>
                                    <div class="pmu-table-value">${{ number_format((float)(isset($walletBalance->balance) ? $walletBalance->balance : 0), 2) }}</div>
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
                                    <a href="{{ route('SA.Download.Earnings', $arr[1]) }}" class="download-btn">Download Payment Log</a>
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
                                <th>Admin Fee</th>
                                <th>Total Fees Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $index => $val)
                            <tr>
                                <td><span class="sno">{{ $index+1 }}</span> </td>
                                <td class="text-capitalize">{{ $val->first_name ?? "NA" }} {{ $val->last_name }}</td>
                                <td>{{ $val->order_number ?? "NA" }}</td>
                                <td>{{ date('d M, Y H:iA', strtotime($val->created_date)) }}</td>
                                <td>STRIPE</td>
                                <td>${{ number_format((float)$val->admin_amount, 2) }}</td>
                                <td>${{ number_format((float)$val->total_amount_paid, 2) }}</td>
                                <td>{{ ($val->status == 1) ? "Paid" : "Payment Pending" }}</td>
                            </tr>
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
