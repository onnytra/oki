<div>
    <div class="exam-container mx-xl-5 my-5">
        <div class="row">
            <div class="col-md-9">
                <div id="question-container">
                    <div class="card question-card">
                        @if ($assignTest->is_done)
                            <a href="{{ route('filament.user.resources.tryouts.index') }}" style="text-decoration: none">
                                <div class="alert alert-success text-center py-3">
                                    <strong>Exam Done</strong>
                                    <br>
                                    <span class="text-muted">Click Here To See Your Score</span>
                                    <br>
                                </div>
                            </a>
                        @endif

                        @if (!$assignTest->is_done)
                            <div class="countdown-timer mb-4 text-success" id="countdown">
                                Time Left: <span id="time">00:00:00</span>
                            </div>
                        @endif
                        <div class="card-body">
                            @if ($currentQuestion)
                                <div class="question-content" style="pointer-events: none;">
                                    <p class="card-text">{!! $currentQuestion->question !!}</p>
                                </div>
                                @foreach ($currentQuestion->questionoptions as $option)
                                    <div class="form-check question-option">
                                        <input class="form-check-input" type="radio"
                                            name="question_{{ $currentQuestion->id }}" value="{{ $option->id }}"
                                            wire:model.live="selectedAnswers.{{ $currentQuestion->id }}"
                                            wire:change="saveAnswer({{ $currentQuestion->id }}, {{ $option->id }})"
                                            @if ($timeLeft <= 0 || $assignTest->is_done) disabled @endif
                                            @if (isset($selectedAnswers[$currentQuestion->id]) && $selectedAnswers[$currentQuestion->id] == $option->id) checked @endif>
                                        <label class="form-check-label"
                                            style="pointer-events: none">{!! $option->option !!}</label>
                                    </div>
                                @endforeach

                                <!-- Clear Answer Button -->
                                <button type="button" class="btn btn-outline-danger mt-2"
                                    wire:click="clearAnswer({{ $currentQuestion->id }})"
                                    @if ($timeLeft <= 0 || $assignTest->is_done) disabled @endif>
                                    Clear Answer
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card question-navigation">
                    <div class="card-body">
                        <h5 class="card-title">Navigation</h5>
                        <div class="btn-group-flex grid-container" role="group">
                            @foreach ($questions as $index => $question)
                                <button type="button" @if ($timeLeft <= 0 || $assignTest->is_done) disabled @endif
                                    wire:click="goToQuestion({{ $question->id }})"
                                    class="btn 
                                        {{ in_array($question->id, $answeredQuestions) ? 'btn-success' : 'btn-outline-secondary' }}
                                        {{ $currentQuestion && $currentQuestion->id === $question->id ? 'active-question' : '' }}">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                        <button type="button" id="submit-button" @if ($timeLeft <= 0 || $assignTest->is_done) disabled @endif
                            class="btn mt-3 w-100 submit-button" x-on:click="showConfirm()">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-group-flex {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            justify-items: center;
        }

        .btn-group-flex button {
            width: 100%;
            height: 50px;
            font-size: 1.1rem;
            padding: 10px;
            border-radius: 6px;
        }

        .submit-button {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white !important;
            font-weight: bold !important;
            border-radius: 6px !important;
            padding: 10px !important;
            font-size: 1.1rem !important;
            margin-top: 10px !important;
            margin-bottom: 10px !important;
            width: 100% !important;
            height: 50px !important;
        }

        .btn-success {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white !important;
        }

        .active-question {
            background-color: #fd7e14 !important;
            border-color: #fd7e14 !important;
            color: white !important;
            font-weight: bold !important;
        }

        .question-card {
            border: 1px solid #ccc;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .question-content {
            font-size: 1.1rem;
        }

        .question-option .form-check-label {
            font-size: 1rem;
        }

        .form-check-input {
            border-radius: 50%;
            margin-right: 10px;
        }

        .form-check-input:checked {
            background-color: #fd7e14;
            border-color: #fd7e14;
        }
    </style>

    <script>
        document.addEventListener('livewire:init', function() {
            let timeLeft = {{ $timeLeft }};
            const display = document.getElementById('time');
            const submitButton = document.getElementById('submit-button');
            let hidden = null;
            let visibilityChange = null;
            let lastActiveTime = new Date().getTime();
            let isVisible = true;

            // Determine the hidden property and visibility change event
            if (typeof document.hidden !== "undefined") {
                hidden = "hidden";
                visibilityChange = "visibilitychange";
            } else if (typeof document.msHidden !== "undefined") {
                hidden = "msHidden";
                visibilityChange = "msvisibilitychange";
            } else if (typeof document.webkitHidden !== "undefined") {
                hidden = "webkitHidden";
                visibilityChange = "webkitvisibilitychange";
            }

            function formatTime(seconds) {
                const hours = Math.floor(seconds / 3600).toString().padStart(2, '0');
                const minutes = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
                const secs = (seconds % 60).toString().padStart(2, '0');
                return `${hours}:${minutes}:${secs}`;
            }

            function updateDisplay(timeRemaining) {
                if (timeRemaining <= 0) {
                    display.textContent = "00:00:00";
                    window.location.reload();
                    return false;
                }
                display.textContent = formatTime(timeRemaining);
                return true;
            }

            function handleVisibilityChange() {
                if (document[hidden]) {
                    isVisible = false;
                    lastActiveTime = new Date().getTime();
                } else {
                    isVisible = true;
                    const currentTime = new Date().getTime();
                    const timeDiff = (currentTime - lastActiveTime) / 1000;

                    if (timeDiff > 3) {
                        @this.call('reportCheat', 'Tab switching violation detected');
                    }
                }
            }

            function handleKeyDown(event) {
                const allowedKeys = /^[0-9a-zA-Z]$/;
                const allowedSpecialKeys = ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'];

                const prohibitedKeys = [
                    'Tab',
                    'Enter',
                    'Backspace',
                    'Delete',
                    'Control',
                    'Alt',
                    'Meta',
                    'Shift',
                    'Escape',
                    'F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'F7', 'F8', 'F9', 'F10', 'F11', 'F12',
                    'PageUp', 'PageDown', 'Home', 'End',
                    'Insert', 'PrintScreen', 'ScrollLock', 'Pause', 'NumLock', 'CapsLock', 'ContextMenu',
                ];
                if (prohibitedKeys.includes(event.key)) {
                    @this.call('reportCheat', 'Prohibited key detected: ' + event.key);
                    event.preventDefault();
                    return;
                }
                if (!allowedSpecialKeys.includes(event.key) && !allowedKeys.test(event.key)) {
                    @this.call('reportCheat', 'Unauthorized key detected: ' + event.key);
                    event.preventDefault();
                    return;
                }
            }

            function handleKeyPress(event) {
                if (event.ctrlKey || event.altKey || event.metaKey) {
                    @this.call('reportCheat', 'Keyboard shortcut detected');
                    event.preventDefault();
                    return;
                }
            }

            if (typeof document.addEventListener !== "undefined" && hidden !== null) {
                document.addEventListener(visibilityChange, handleVisibilityChange, false);
            }

            document.addEventListener('keydown', handleKeyDown, false);
            document.addEventListener('keypress', handleKeyPress, false);

            document.addEventListener('contextmenu', function(event) {
                event.preventDefault();
                @this.call('reportCheat', 'Right-click detected');
                return false;
            });

            updateDisplay(timeLeft);

            const countdown = setInterval(function() {
                timeLeft--;
                if (!updateDisplay(timeLeft)) {
                    clearInterval(countdown);
                }
            }, 1000);

            window.addEventListener('beforeunload', function() {
                clearInterval(countdown);
            });
        });

        function showConfirm() {
            Swal.fire({
                title: 'Exam Will Be Submitted',
                text: 'Are you sure you want to submit the exam?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('submit');
                }
            });
        }
    </script>
</div>
