<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{!! assets('assets/website-images/logo.svg') !!}">
    <title>Track Cert Login</title>
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
                    <p class="mb-3">Sign in to continue</p>
                    @include('layouts.partials.messages')
                    <div class="row">
                        <form method="post" action="{{ route('SA.login.perform') }}" id="arkansas_login_form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="role" value="3" />
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email ID" required>
                                    @if ($errors->has('email'))
                                    <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="Password" class="form-control" name="password" value="" placeholder="Password" required>
                                    @if ($errors->has('password'))
                                    <span class="text-danger text-left">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <button class="becomeacreator-btn" id="LoginCheck" type="submit">Sign In</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <a class="ForgotPassword-text" href="{{ route('SA.forgot.password') }}">Forgot Password?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<style>
    .alert{
        width: 89%;
    }
</style>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        $.validator.addMethod("emailValidate", function(value) {
            return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value);
        }, 'Please enter valid email address.');

        $('#arkansas_login_form').validate({
            rules: {
                email: {
                    required: true,
                    minlength: 10,
                    maxlength: 50,
                    emailValidate: true
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 30,
                },
            },
            messages: {
                email: {
                    required: 'Please enter email address',
                },
                password: {
                    required: 'Please enter password',
                },

            },
            submitHandler: function(form) {
                // This function will be called when the form is valid and ready to be submitted
                form.submit();
            },
            errorElement: "span",
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");
                error.css("font-size", '0.9rem');
                element.closest(".form-group").append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass("is-invalid");
            },
        });
    });
</script>

</html>