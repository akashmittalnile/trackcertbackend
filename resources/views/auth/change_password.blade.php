<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{!! assets('assets/website-images/logo.svg') !!}">
    <title>Track Cert - Change Password</title>
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
                    <p>Change Password</p>
                    @include('layouts.partials.messages')
                    <div class="row">
                        <form method="post" action="{{ route('admin.change_password_update') }}" id="Form_Login">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}" />
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" value=""
                                        placeholder="New Password" required>
                                    @if ($errors->has('password'))
                                        <span class="text-danger text-left">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="password" class="form-control" name="cnf_password" value=""
                                        placeholder="Confirm Password" required>
                                    @if ($errors->has('password'))
                                        <span class="text-danger text-left">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <button class="becomeacreator-btn" type="submit" id="LoginCheck">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <!-- Submit Form with ajax -->
    <script>
        $(document).ready(function() {

            $.validator.addMethod("AtLeastOnenumber", function(value) {
                return /(?=.*[0-9])/.test(value);
            }, 'At least 1 number is required.');

            $.validator.addMethod("AtLeastOneUpperChar", function(value) {
                return /^(?=.*[A-Z])/.test(value);
            }, 'At least 1 uppercase character is required.');

            $.validator.addMethod("AtLeastOneLowerChar", function(value) {
                return /^(?=.*[a-z])/.test(value);
            }, 'At least 1 lower character is required.');

            $.validator.addMethod("AtLeastOneSpecialChar", function(value) {
                return !/^[A-Za-z0-9 ]+$/.test(value);
            }, 'At least 1 special character is required.');

            $('#Form_Login').validate({
                rules: {
                    password: {
                        required: true,
                        minlength: 6,
                        maxlength: 30,
                        AtLeastOnenumber: true,
                        AtLeastOneUpperChar: true,
                        AtLeastOneLowerChar: true,
                        AtLeastOneSpecialChar: true
                    },
                    cnf_password: {
                        required: true,
                        equalTo: "input[name='password']"
                    },
                },
                messages: {
                    password: {
                        required: 'Please enter new password',
                    },
                    cnf_password: {
                        required: 'Please enter confirm password',
                        equalTo: "Password and confirm password must be same."
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

</body>

</html>