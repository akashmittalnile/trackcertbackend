<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Cert - Quiz Result</title>
    <link rel="stylesheet" type="text/css" href="{!! assets('assets/website-css/header-footer.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! assets('assets/website-plugins/iconsax/iconsax.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! assets('assets/website-css/auth.css') !!}">
    <script src="{!! assets('assets/website-js/jquery-3.7.0.min.js') !!}" type="text/javascript"></script>
    <script src="{!! assets('assets/website-plugins/bootstrap/js/bootstrap.bundle.min.js') !!}" type="text/javascript"></script>
    <script src="{!! assets('assets/website-js/function.js') !!}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{!! assets('assets/website-css/becomeacreator.css') !!}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <style type="text/css">
        .quiz-results-section {
            position: relative;
            background: #1e1e1e;
            padding: 2rem;
            height: 100%;
        }

        .quiz-results-chart {
            width: 100%;
            height: 275px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .quiz-results-content h3 {
            color: #e27d20;
            text-align: center;
            font-family: League Spartan;
            font-size: 30px;
            font-style: normal;
            font-weight: 600;
            line-height: 100%;
            letter-spacing: -0.3px;
            margin: 0;
            padding: 0;
        }

        .quiz-results-content p {
            color: #8C9AA1;
            text-align: center;
            font-family: League Spartan;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: 100%;
            letter-spacing: -0.2px;
        }

        .quizcircle {
            border-radius: 50%;
            background-color: #e27d20;
            width: 150px;
            height: 150px;
            position: absolute;
            opacity: 0;
            animation: scaleIn 4s infinite cubic-bezier(.36, .11, .89, .32);
        }

        .quiz-results-text {
            z-index: 100;
            background-color: #E0B220;
            border-radius: 100%;
            width: 120px;
            height: 120px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .quiz-results-text h2 {
            color: #FFF;
            text-align: center;
            font-family: League Spartan;
            font-size: 29px;
            font-style: normal;
            font-weight: 700;
            line-height: 100%;
            /* 29px */
            letter-spacing: -0.29px;
            margin: 0;
            padding: 0
        }

        .quiz-results-text h5 {
            color: #000;
            text-align: center;
            font-family: League Spartan;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: 100%;
            /* 14px */
            letter-spacing: -0.14px;
            margin: 0;
            padding: 0
        }


        @keyframes scaleIn {
            from {
                transform: scale(.5, .5);
                opacity: .5;
            }

            to {
                transform: scale(2, 2);
                opacity: 0;
            }
        }

        .quiz-results-card {
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            border: 1px solid #3e3c3c;
            background: #3e3c3c;
            position: relative;margin-bottom: 10px;
        }

        .quiz-results-card h3 {
            color: #281809;
            font-family: League Spartan;
            font-size: 22px;
            font-style: normal;
            font-weight: 600;
            line-height: 16px;
            /* 72.727% */
            letter-spacing: 0.4px;
            margin: 0;
            padding: 0
        }


        .quiz-results-card p {
            color: #ffffff;
            font-family: League Spartan;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: 20px;
            /* 142.857% */
            letter-spacing: 0.25px;
            margin: 0
        }

        .attempt h3 {
            color: #E0B220;
        }

        .Correct h3 {
            color: #34A853;
        }

        .Wrong h3 {
            color: #EB001B;
        }

        .quiz-results-action {
            text-align: center;
        }

        a.Retakebtn {
            border-radius: 5px; background: var(--yellowbtn, #261313); padding: 15px 20px; color: var(--white, #FFF); text-align: center; font-size: 14px; font-style: normal; font-weight: 500; line-height: normal; text-transform: uppercase;border: none; display: inline-block; position: relative; outline: none; 
        }

        .becomeacreator-form-info {
            position: relative;
            padding: 2rem;
        }

        .becomeacreator-form-info h2 {
            font-size: 24px;
            text-align: center;
            margin: 0;
            padding: 0;
            color: #281809;
        }

        .becomeacreator-form-info p {
            font-size: 14px;
            text-align: center;
            margin: 0 0 1rem 0;
            color: #281809;
        }

        .becomeacreator-btn-action {
            text-align: center;
        }

        .becomeacreator-btn-action .close-btn {
            background: #fff;
            color: #281809;
            text-transform: uppercase;
            padding: 10px 30px;
            border: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 5px;
        }

        .becomeacreator-btn-action .Login-btn {
            background: #281809;
            color: #fff;
            text-transform: uppercase;
            padding: 10px 30px;
            border: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 5px;
            box-shadow: 0 4px 28px rgb(168 91 91 / 21%);
        }

        .becomeacreator-form-media {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="quiz-results-section">
        <div class="continer">
            <div class="quiz-results-content">
                @if( number_format((float)(($obtained * 100) / $total), 1) >= $passingPercentage )
                <h3>Hurry !</h3>
                <p>You passed this quiz with a score of</p>
                @else
                <h3>Whoops !</h3>
                <p>You failed this quiz with a score of</p>
                @endif

                <div id="outerContainer">
                    <div class="quiz-results-chart">
                        <div class="quiz-results-text">
                            <h2>{{ number_format((float)(($obtained * 100) / $total), 1) }}%</h2>
                            <h5>Your Score</h5>
                        </div>
                        <div class="quizcircle" style="animation-delay: -3s"></div>
                        <div class="quizcircle" style="animation-delay: -2s"></div>
                        <div class="quizcircle" style="animation-delay: -1s"></div>
                        <div class="quizcircle" style="animation-delay: 0s"></div>
                    </div>
                </div>

                <p>You need {{ $passingPercentage ?? 33 }}% to pass</p>


            </div>
            <div class="quiz-results-section2" style="padding: 20px 0px;">
                <div class="row" style="width: 102%;">
                    <div class="col-md-3">
                        <div class="quiz-results-card">
                            <h3>{{ $totalQuestion ?? 0 }}</h3>
                            <p>Total Question</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quiz-results-card attempt">
                            <h3>{{ $totalQuestion ?? 0 }}</h3>
                            <p>Total attempt Questions</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quiz-results-card Correct">
                            <h3>{{ $totalCorrect ?? 0 }}</h3>
                            <p>Correct Question</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quiz-results-card Wrong">
                            <h3>{{ $totalWrong ?? 0 }}</h3>
                            <p>Wrong Question</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="quiz-results-action">
                @if( !(number_format((float)(($obtained * 100) / $total), 1) >= $passingPercentage) )
                <a class="Retakebtn" href="{{ url('/').'/api/contest/'.encrypt_decrypt('encrypt',$chapterId).'/'.encrypt_decrypt('encrypt',$quizId).'/'.encrypt_decrypt('encrypt', $userId) }}">Retake Quiz</a>
                @endif
            </div>
        </div>
    </div>

</body>

</html>