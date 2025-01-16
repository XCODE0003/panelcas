<?php

namespace App\Filament\Resources\SettingAdminResource\Pages;

use App\Filament\Resources\SettingAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSettingAdmins extends ListRecords
{
    protected static string $resource = SettingAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
