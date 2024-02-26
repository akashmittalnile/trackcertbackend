<link rel="stylesheet" type="text/css" href="{!! assets('assets/website-css/header-footer.css') !!}">
<link rel="stylesheet" type="text/css" href="{!! assets('assets/website-plugins/iconsax/iconsax.css') !!}">
<link rel="stylesheet" type="text/css" href="{!! assets('assets/website-css/auth.css') !!}">
<script src="{!! assets('assets/website-js/jquery-3.7.0.min.js') !!}" type="text/javascript"></script>
<script src="{!! assets('assets/website-plugins/bootstrap/js/bootstrap.bundle.min.js') !!}" type="text/javascript"></script>
<script src="{!! assets('assets/website-js/function.js') !!}" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="{!! assets('assets/website-css/becomeacreator.css') !!}">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
    body{
        background-color: #1e1e1e !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .course-player-quiz-inner h3{
        color: #e27d20 !important;
        font-size: 1.3rem !important;
    }
    .course-player-quiz-question h4{
        color: #ffffff !important;
    }
    .course-player-quiz-choice-label{
        background-color: #000 !important;
        color: #8C9AA1 !important;
    }
    .course-player-quiz-choice-checkbox {
        border: 2px solid #3e3c3c !important; 
    }
    .course-player-quiz-choice-text{
        color: #8C9AA1 !important;
    }
</style>
<div class="course-player-quiz">
    <div class="course-player-quiz-inner">
        <h3 id="question">QUESTION 1 OF {{ $questionCount }}</h3>

        @foreach($data as $keyq => $val)
            <div class="{{ ($keyq+1 != 1 ? 'd-none' : '') }}" id="card-{{$keyq+1}}">
                <div class="course-player-quiz-question">
                    <h4>{{$val['title'] ?? "NA"}}?</h4>
                    <p>Marks :- {{ $val['marks'] ?? 0 }}</p>
                    <p>Choose only {{ $val['optionCount'] ?? 0 }} best answer.</p>
                </div>
                <div>
                    <form  method="POST" id="formData">
                        <div class="course-player-quiz-choices">
                            <?php $s_no = 'A'; ?>
                            @forelse($val['option'] as $keyo => $op)
                                <div class="course-player-quiz-radio" id="option-{{ $val['question_id'] }}-{{ $op['id'] }}">
                                    <input type="{{($val['optionCount']>1)?'checkbox':'radio'}}" id="choices-{{$keyq}}-{{ $keyo }}" name="option" value="{{$op['id']}}" required>
                                    <label for="choices-{{$keyq}}-{{ $keyo }}">
                                        <div class="course-player-quiz-choice-checkbox">
                                            <div class="course-player-quiz-choice-label">{{ $s_no }}</div>
                                            <div class="course-player-quiz-choice-text">{{ $op['answer'] ?? "NA" }}</div>
                                        </div>
                                    </label>
                                </div>
                            <?php $s_no ++; ?>
                            @empty
                            @endforelse
                            <input type="hidden" name="quiz_id" value="{{$val['step_id']}}">
                            <input type="hidden" name="question_id" value="{{$val['question_id']}}"> 
                        </div>
                        <div class="course-player-footer-action" id="card-button-{{ $val['question_id'] }}">
                            <button type="submit" id="submit" class="Confirm-btn submit" data-index="{{$keyq+1}}">
                                Confirm
                            </button>
                        </div>
                    </form>
                    <div id="explanation" class="course-player-quiz-explanation d-none">

                    </div>
                    <div class="course-player-footer-action d-none" id="next-button-{{ $val['question_id'] }}">
                        <button id="next-btn" data-index="{{$keyq+1}}" type="button" class="Confirm-btn {{($questionCount==$keyq+1)?'d-none':'' }}">
                            Next
                        </button>
                    </div>
                    <div class="course-player-footer-action d-none" id="result-btn">
                        <a href="{{ url('/') }}/api/result/{{ encrypt_decrypt('encrypt', $val['step_id']) }}/{{ encrypt_decrypt('encrypt', $userId ) }}" class="Confirm-btn">
                            Result
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function(){
        $(".submit").click(function (e) {
            // alert(2);
            e.preventDefault();
            var id = $("input[name='option']:checked").val();
            if(id=="" || id==null || id==undefined){
                toastr.error('Please select one of these options.');
                return false;
            }
            var quiz_id = $("input[name='quiz_id']").val();
            var question_id = $("input[name='question_id']").val();
            let confirmIndex = $(this).attr('data-index');
            let formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('option', id);
            formData.append('quiz_id', quiz_id);
            formData.append('question_id', question_id);
            formData.append('user_id', "{{ $userId }}");
            $.ajax({
                url: "{{url('/')}}" + "/api/contest-form",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                contentType: false,
                cache: false,
                processData:false,
                data: formData,
                success: function (data) {
                    console.log(data);
                    if (data.status) {
                        toastr.success(data.message);
                        
                        $('#card-button-'+data.request.question_id).addClass('d-none');
                        
                        if(confirmIndex == "{{ $questionCount }}"){
                            // $("#result-btn").removeClass('d-none');
                            window.location.replace($("#result-btn a").attr('href'));
                            // $("#result-btn a").trigger('click');
                        }else{
                            // $('#next-button-'+data.request.question_id).removeClass('d-none');
                            $(`#next-button-${data.request.question_id} button`).click();
                        }
                    }
                }
            });
        });


    })
    
    $(document).on('click', '#next-btn', function(){
        let cardIndex = $(this).attr('data-index');
        let nextIndex = parseInt(cardIndex)+1;
        $("#card-"+cardIndex).remove();
        $("#card-"+nextIndex).removeClass('d-none');
        $("#question").html("QUESTION "+ nextIndex +" OF {{ $questionCount }}");
    })

    
</script>




<style type="text/css">
@import url('https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
body{background-color: #fdf6ff; font-family: 'Source Sans 3', sans-serif;}
.course-player-quiz{flex: 1 1 0; width: 100%; max-width: 43.75rem; min-height: 0; margin: 2.5rem auto 0; padding: 0 1rem;}
.course-player-quiz-inner h3 {font-size: .75rem; font-weight: 400; line-height: 1rem; text-transform: uppercase; display: block; margin: 0 0 0.5rem; color: #e0b220; } 
.course-player-quiz-question h4 {font-size: 1.75rem;line-height: 2.625rem;color: #040706;padding: 0;margin-top: 0;}
.course-player-quiz-question p{font-size: .875rem;font-weight: 400;line-height: 1.25rem;margin: 0 0 1rem;    color: #818181;}

.course-player-quiz-choice-checkbox {display: flex;position: relative;flex-direction: row;align-items: center;width: 100%;margin: 0 0 1rem;padding: 0;border-width: 1px;border-style: solid;border-radius: 2px;cursor: pointer;border-color: #261313;}
.course-player-quiz-choice-label {display: inline-flex;align-self: stretch;padding: 16px 16px;background-color: #261313;margin: 0;font-size: 1.125rem;font-weight: 600;line-height: 1.5rem;color: #fff;}

.course-player-quiz-choice-text {font-size: 1rem;position: relative;font-weight: 400;line-height: 1.5rem;display: block;width: 100%;margin: 0;padding: 16px 56px 16px 16px;text-align: left;color: #040706;}

.course-player-quiz-radio label {position: relative; display: inline-block; height: auto; cursor: pointer; margin-bottom: 0; width: 100%;}
.course-player-quiz-radio label::before, 
.course-player-quiz-radio label::after {position: absolute; top: 0; border-radius: 10px; left: 0; display: block; right: 0; bottom: 0; }
.course-player-quiz-radio label::before {content: " "; }
.course-player-quiz-radio input[type="radio"] {position: absolute; opacity: 0; z-index: -1; margin: 0; }
.course-player-quiz-radio input[type="radio"] + label::after {content: ""; color: #FFC107; font-size: 15px; height: 15px; width: 15px; margin: 0 auto; text-align: center; left: 5px; top: 5px; }
.course-player-quiz-radio input[type="radio"]:checked + label::before {border-color: #FC4A26; }
.course-player-quiz-radio input[type="radio"] + label::after {-webkit-transform: scale(0); -moz-transform: scale(0); -ms-transform: scale(0); -o-transform: scale(0); transform: scale(0); }
.course-player-quiz-radio input[type="radio"]:checked + label::after {-webkit-transform: scale(1); -moz-transform: scale(1); -ms-transform: scale(1); -o-transform: scale(1); transform: scale(1); }

.course-player-quiz-radio input[type="radio"]:hover + label .course-player-quiz-choice-checkbox{border-color: #e27d20 ; }

.course-player-quiz-radio input[type="radio"]:checked + label .course-player-quiz-choice-checkbox{border-color: #ffffff; outline-offset: -2px; outline-style: solid; outline-width: 1px; outline-color: #ffffff; }

.course-player-quiz-radio input[type="radio"]:checked + label .course-player-quiz-choice-checkbox .course-player-quiz-choice-label{background-color: #ffffff;color: #fff;}


.course-player-footer-action {text-align: right; padding-top: 24px; }
button.Confirm-btn {border-radius: 5px; background: var(--yellowbtn, #261313); padding: 15px 20px; color: var(--white, #FFF); text-align: center; font-size: 14px; font-style: normal; font-weight: 500; line-height: normal; text-transform: uppercase;border: none; display: inline-block; position: relative; outline: none; }
a.Confirm-btn {border-radius: 5px; background: var(--yellowbtn, #261313); padding: 15px 20px; color: var(--white, #FFF); text-align: center; font-size: 14px; font-style: normal; font-weight: 500; line-height: normal; text-transform: uppercase;border: none; display: inline-block; position: relative; outline: none; }
.course-player-quiz-explanation {margin:25px 0 0; padding: 24px; border-color: #261313; border-width: 1px; border-radius: 2px; border-style: solid; font-size: 1rem; font-weight: 600; line-height: 1.5rem; }

.correctanswer-msg{color: #4caf50; }
.wronganswer-msg{color: #d83232; }

.course-player-quiz-radio.correctanswer .course-player-quiz-choice-checkbox{border-color: #4caf50; }
.course-player-quiz-radio.wronganswer .course-player-quiz-choice-checkbox{border-color: #d83232; }

.correctanswer .course-player-quiz-choice-label{background-color: #4caf50;}
.wronganswer .course-player-quiz-choice-label{background-color: #d83232;}


.correctanswer .course-player-quiz-choice-text:before {content: ''; position: absolute; right: 10px; background: url(correctanswer-icon.svg); width: 20px; height: 20px }
.wronganswer .course-player-quiz-choice-text:before {content: ''; position: absolute; right: 10px; background: url(wronganswer-icon.svg); width: 20px; height: 20px }

</style>