<?php
// app/Filament/Resources/ExamCategoryResource/Pages/CreateExamCategory.php

namespace App\Filament\Resources\ExamCategoryResource\Pages;

use App\Filament\Resources\ExamCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExamCategory extends CreateRecord
{
    protected static string $resource = ExamCategoryResource::class;
}