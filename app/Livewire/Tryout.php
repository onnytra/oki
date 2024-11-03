<?php

namespace App\Livewire;

use App\Models\Answer;
use App\Models\Assigntest;
use App\Models\Exam;
use App\Models\Questionoptions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Tryout extends Component
{
    public $exam;
    public $assignTest;
    public $questions;
    public $currentQuestion;
    public $timeLeft;
    public $selectedAnswers = [];
    public $currentQuestionId;

    public function mount($id)
    {
        $this->assignTest = Assigntest::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($this->assignTest->is_done) {
            \Filament\Notifications\Notification::make()
                ->title('Exam Has Been Taken')
                ->success()
                ->send();
            return redirect()->route('filament.user.resources.tryouts.index');
        } elseif ($this->assignTest->is_cheat) {
            \Filament\Notifications\Notification::make()
                ->title('Cheat Detected')
                ->body('Cheat Detected. Please Contact Admin.')
                ->danger()
                ->send();
            return redirect()->route('filament.user.resources.tryouts.index');
        };

        $this->exam = Exam::with(['questions.questionoptions'])
            ->findOrFail($this->assignTest->exam_id);

        if (!Carbon::now()->greaterThan($this->exam->start)) {
            \Filament\Notifications\Notification::make()
                ->title('Ujian Belum Dimulai')
                ->body('Silakan kembali setelah waktu mulai.')
                ->danger()
                ->send();
            return redirect()->route('filament.user.resources.tryouts.index');
        }


        $this->questions = $this->exam->questions;

        if ($this->questions->isNotEmpty()) {
            $this->currentQuestionId = $this->questions->first()->id;
            $this->currentQuestion = $this->questions->first();
        }

        $this->initializeAnswers();
        $this->calculateTimeLeft();

        if ($this->timeLeft <= 0 && !$this->assignTest->is_done) {
            $this->assignTest->update(['is_done' => true]);
        }
    }

    protected function initializeAnswers()
    {
        $this->questions->each(function ($question) {
            Answer::firstOrCreate(
                [
                    'assigntest_id' => $this->assignTest->id,
                    'question_id' => $question->id,
                ],
                ['score' => $this->exam->empty]
            );
        });

        $answers = Answer::where('assigntest_id', $this->assignTest->id)
            ->whereNotNull('option_id')
            ->get();

        foreach ($answers as $answer) {
            $this->selectedAnswers[$answer->question_id] = $answer->option_id;
        }
    }

    public function render()
    {
        return view('livewire.tryout');
    }

    public function goToQuestion($questionId)
    {
        if ($this->timeLeft <= 0 || $this->assignTest->is_done) {
            return;
        }

        $this->currentQuestionId = $questionId;
        $this->currentQuestion = $this->questions->find($questionId);
        $this->calculateTimeLeft();
    }

    protected function calculateTimeLeft()
    {
        if ($this->assignTest->is_done) {
            $this->timeLeft = 0;
            return;
        }

        $now = Carbon::now();
        $endTime = Carbon::parse($this->exam->end);

        if ($now->gt($endTime)) {
            $this->timeLeft = 0;
            if (!$this->assignTest->is_done) {
                $this->assignTest->update(['is_done' => true]);
            }
        } else {
            $this->timeLeft = $endTime->diffInSeconds($now);
        }
    }

    public function saveAnswer($questionId, $optionId)
    {
        if ($this->timeLeft <= 0 || $this->assignTest->is_done) {
            return;
        }

        $option = Questionoptions::find($optionId);
        $score = $option->is_true ? $this->exam->true : $this->exam->false;

        Answer::where('assigntest_id', $this->assignTest->id)
            ->where('question_id', $questionId)
            ->update([
                'option_id' => $optionId,
                'score' => $score
            ]);

        $this->selectedAnswers[$questionId] = $optionId;
    }
    public function clearAnswer($questionId)
    {
        if ($this->timeLeft <= 0 || $this->assignTest->is_done) {
            return;
        }
        Answer::where('assigntest_id', $this->assignTest->id)
            ->where('question_id', $questionId)
            ->update([
                'option_id' => null,
                'score' => $this->exam->empty
            ]);

        unset($this->selectedAnswers[$questionId]);
    }

    public function reportCheat($reason)
    {
        if ($this->assignTest->is_done || $this->assignTest->is_cheat) {
            return;
        }

        $this->assignTest->update([
            'is_cheat' => true,
            'cheat_reason' => $reason,
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Violation Detected')
            ->body('Your exam has been terminated due to a violation.')
            ->danger()
            ->send();

        $this->redirect(route('filament.user.resources.tryouts.index'));
    }

    public function submit()
    {
        if ($this->timeLeft <= 0) {
            return;
        }

        $this->assignTest->update(['is_done' => true]);
        $this->calculateTimeLeft();

        session()->flash('message', 'Data berhasil disimpan');
        // return redirect()->route('filament.user.pages.dashboard');
    }
}
