<div class="sidebar-wrapper">
    <?php
    $currentURL = Route::currentRouteName();
    ?>
    <div class="sidebar">
        <nav class="navbar navbar-light">
            <div class="sidebar-logo">
                <a href="" class="navbar-brand mx-4 mb-3">
                    <img src="{!! assets('assets/superadmin-images/logo.svg') !!}">
                </a>
            </div>
            <div class="navbar-nav">
                <ul class="sidebar-navbar-list">
                    <li>
                        <a href="{{ route('SA.Dashboard') }}" class="nav-item nav-link  @if ($currentURL == 'SA.Dashboard') active @endif">
                            <span class="menu-icon"><i class="las la-tachometer-alt"></i></span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.ContentCreators') }}" class="nav-item nav-link @if ($currentURL == 'SA.ContentCreators' || $currentURL == 'SA.ListedCourse' || $currentURL == 'SA.AccountApprovalRequest' || $currentURL == 'SA.Addcourse2' || $currentURL == 'SA.CourseList' || $currentURL == 'SA.Payment.Request') active @endif">
                            <span class="menu-icon"><i class="las la-user"></i></span>
                            <span class="menu-title">Content Creators</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Content-Creator.Course') }}" class="nav-item nav-link @if ($currentURL == 'SA.Content-Creator.Course' || $currentURL == 'SA.Content-Creator.Course.Chapter') active @endif">
                            <span class="menu-icon"><i class="las la-user"></i></span>
                            <span class="menu-title">Creator Courses</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Course') }}" class="nav-item nav-link @if ($currentURL == 'SA.Course' || $currentURL == 'SA.AddCourse' || $currentURL == 'SA.Course.Chapter' || $currentURL == 'SA.view.course' || $currentURL == 'SA.edit.course') active @endif">
                            <span class="menu-icon"><i class="las la-tasks"></i></span>
                            <span class="menu-title">Manage Course</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Students') }}" class="nav-item nav-link @if ($currentURL == 'SA.Students' ||$currentURL ==  'SA.StudentDetail' || $currentURL == 'SA.progress.report') active @endif">
                            <span class="menu-icon"><i class="las la-tasks"></i></span>
                            <span class="menu-title">Students</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Earnings') }}" class="nav-item nav-link @if ($currentURL == 'SA.Earnings') active @endif">
                            <span class="menu-icon"><i class="las la-tasks"></i></span>
                            <span class="menu-title">Earnings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Product.Orders') }}" class="nav-item nav-link @if ($currentURL == 'SA.Product.Orders' || $currentURL == 'SA.Product.order.details') active @endif">
                            <span class="menu-icon"><i class="las la-crown"></i></span>
                            <span class="menu-title">Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Category') }}" class="nav-item nav-link @if ($currentURL == 'SA.Category'||$currentURL == 'SA.AddCategory'||$currentURL == 'SA.EditCategory') active @endif">
                            <span class="menu-icon"><i class="las la-gem"></i></span>
                            <span class="menu-title">Manage Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.TagListing') }}" class="nav-item nav-link @if ($currentURL == 'SA.TagListing') active @endif">
                            <span class="menu-icon"><i class="las la-gem"></i></span>
                            <span class="menu-title">Manage Tags</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Coupons') }}" class="nav-item nav-link @if ($currentURL == 'SA.Coupons') active @endif">
                            <span class="menu-icon"><i class="las la-gem"></i></span>
                            <span class="menu-title">Manage Coupons</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Notifications') }}" class="nav-item nav-link @if ($currentURL == 'SA.Notifications' || $currentURL == 'SA.Create.Notifications') active @endif">
                            <span class="menu-icon"><i class="las la-file-invoice-dollar"></i></span>
                            <span class="menu-title">Manage Notifications</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Performance') }}" class="nav-item nav-link @if ($currentURL == 'SA.Performance') active @endif">
                            <span class="menu-icon"><i class="las la-money-check-alt"></i></span>
                            <span class="menu-title">Performance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.Posts') }}" class="nav-item nav-link @if ($currentURL == 'SA.Posts' || $currentURL == 'SA.Edit.Post' || $currentURL == 'SA.Create.Post') active @endif">
                            <span class="menu-icon"><i class="las la-money-check-alt"></i></span>
                            <span class="menu-title">Manage Pages</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('SA.HelpSupport') }}" class="nav-item nav-link @if ($currentURL == 'SA.HelpSupport') active @endif">
                            <span class="menu-icon"><i class="las la-money-check-alt"></i></span>
                            <span class="menu-title">Help & Support</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>