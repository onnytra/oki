<?php

namespace App\Filament\Resources\ExamResource\Widgets;

use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->color('primary'),
            Stat::make('Total Exams', Exam::count())
                ->color('success'),
            Stat::make('Total Questions', Question::count())
                ->color('warning'),

            
        ];
    }
}
