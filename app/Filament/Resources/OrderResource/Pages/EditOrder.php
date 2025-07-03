<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Events\OrderStatusUpdated;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

     protected function afterSave(): void
    {
        event(new OrderStatusUpdated($this->record));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
