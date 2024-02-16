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
                        <a href="{{ route('home.index') }}" class="nav-item nav-link @if ($currentURL == 'home.index' || $currentURL == 'Home.Addcourse' || $currentURL == 'Home.Addcourse2' || $currentURL == 'Home.CourseList' || $currentURL == 'Home.view.course' || $currentURL == 'Home.edit.course') active @endif">
                            <span class="menu-icon"><i class="las la-tasks"></i></span>
                            <span class="menu-title">Courses</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('Home.Performance') }}" class="nav-item nav-link @if ($currentURL == 'Home.Performance') active @endif">
                            <span class="menu-icon"><i class="las la-money-check-alt"></i></span>
                            <span class="menu-title">Performance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('Home.HelpSupport') }}" class="nav-item nav-link @if ($currentURL == 'Home.HelpSupport') active @endif">
                            <span class="menu-icon"><i class="las la-tachometer-alt"></i></span>
                            <span class="menu-title">Help & Support</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('Home.earnings') }}" class="nav-item nav-link @if ($currentURL == 'Home.earnings' || $currentURL == 'Home.payment.request' || $currentURL == 'Home.order.details') active @endif">
                            <span class="menu-icon"><i class="las la-file-invoice-dollar"></i></span>
                            <span class="menu-title">Earnings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('Home.students') }}" class="nav-item nav-link  @if ($currentURL == 'Home.students' || $currentURL == 'Home.student.details' || $currentURL == 'Home.progress.report') active @endif">
                            <span class="menu-icon"><i class="las la-user"></i></span>
                            <span class="menu-title">Students</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logout.perform') }}" class="nav-item nav-link">
                            <span class="menu-icon"><i class="las la-tachometer-alt"></i></span>
                            <span class="menu-title">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
