<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{!! assets('assets/website-images/logo.svg') !!}">
    <title>Track Cert - Reset Password</title>
    <link rel="stylesheet" type="text/css" href="{!! assets('assets/website-css/header-footer.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! assets('assets/website-plugins/iconsax/iconsax.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! assets('assets/website-css/auth.css') !!}">
    <script src="{!! assets('assets/website-js/jquery-3.7.0.min.js') !!}" type="text/javascript"></script>
    <script src="{!! assets('assets/website-plugins/bootstrap/js/bootstrap.bundle.min.js') !!}" type="text/javascript"></script>
    <script src="{!! assets('assets/website-js/function.js') !!}" type="text/javascript"></script>
</head>

<body>
    <div class="auth-form-section">
        <div class="container d-flex flex-column align-items-center">
            <div class="auth-form-card">
                <div class="auth-form d-flex flex-column align-items-center">
                    <a href="javascript:void(0)" class="mb-3"><img width="140" height="140" src="{!! assets('assets/website-images/logo.svg') !!}" /></a>
                    <p>Reset Password</p>
                    @include('layouts.partials.messages')
                    <div class="row">
                        <form method="post" action="{{ route('admin.reset_password.otp') }}" id="Form_Login">@csrf
                            <input name="email" type="hidden" value="{{ $email }}">
                            <div class="row text-center mx-auto" style="width: 85%;">
                                <div class="col-md-3">
                                    <div class="form-group auth-form-group ">
                                        <input style="padding: 10px; width: 45px; font-size: 1.2rem;" class="form-control inputstab" name="otp1" type="text" id="n0" maxlength="1" autocomplete="off" autofocus data-next="1" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group auth-form-group ">
                                        <input style="padding: 10px; width: 45px; font-size: 1.2rem;" class="form-control inputstab" name="otp2" type="text" id="n1" maxlength="1" autocomplete="off" autofocus data-next="2" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group auth-form-group ">
                                        <input style="padding: 10px; width: 45px; font-size: 1.2rem;" class="form-control inputstab" name="otp3" type="text" id="n2" maxlength="1" autocomplete="off" autofocus data-next="3" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group auth-form-group ">
                                        <input style="padding: 10px; width: 45px; font-size: 1.2rem;" class="form-control inputstab" name="otp4" type="text" id="n3" maxlength="1" autocomplete="off" autofocus data-next="4" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group text-center">
                                        <button type="submit" class="becomeacreator-btn">Submit</button>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group auth-forgot-text">
                                        <p>Didn't get OTP? <a style="color: blue;" href="{{ route('admin.resend_email', $email) }}"> Resend OTP</a></p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Form with ajax -->
    <script>
        $('.inputstab').keyup(function(e) {
            if (this.value.length === this.maxLength) {
                let next = $(this).data('next');
                $('#n' + next).focus();
            }
        });
    </script>

</body>

</html>