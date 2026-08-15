<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApplicant extends CreateRecord
{
    protected static string $resource = ApplicantResource::class;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Add Applicant');
    }

    // Change the "Create & create another" button label
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Add & Add Another');
    }
}
