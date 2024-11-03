<?php

namespace App\Filament\Resources\AssigntestResource\Pages;

use App\Filament\Resources\AssigntestResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Assigntest;

class CreateAssigntest extends CreateRecord
{
    protected static string $resource = AssigntestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (is_array($data['user_id'])) {
            $data['user_id'] = $data['user_id'][0];
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $formData = $this->form->getState();
        $examId = $formData['exam_id'];
        $userIds = $formData['user_id'];

        $remainingUsers = array_slice($userIds, 1);

        foreach ($remainingUsers as $userId) {
            Assigntest::create([
                'exam_id' => $examId,
                'user_id' => $userId,
            ]);
        }
    }
}
