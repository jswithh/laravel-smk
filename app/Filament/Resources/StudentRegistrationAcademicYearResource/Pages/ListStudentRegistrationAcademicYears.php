<?php

namespace App\Filament\Resources\StudentRegistrationAcademicYearResource\Pages;

use App\Filament\Resources\StudentRegistrationAcademicYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentRegistrationAcademicYears extends ListRecords
{
    protected static string $resource = StudentRegistrationAcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
