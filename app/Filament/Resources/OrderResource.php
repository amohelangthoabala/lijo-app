<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderItemsResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Management';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        $query = parent::getEloquentQuery();

        if (!$user->hasRole('admin')) {
            $query->whereIn('restaurant_id', $user->restaurants->pluck('id'));
        }

        // ✅ Order by status flow, then newest first
        return $query->orderByRaw("FIELD(status, 'pending', 'accepted', 'preparing', 'delivering', 'delivered')")
                     ->orderByDesc('created_at');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->maxLength(255)
                ->default(null),

            Forms\Components\TextInput::make('phone')
                ->tel()
                ->maxLength(255)
                ->default(null),

            Forms\Components\TextInput::make('email')
                ->email()
                ->maxLength(255)
                ->default(null),

            Forms\Components\Select::make('restaurant_id')
                ->label('Restaurant')
                ->required()
                ->options(function () {
                    $user = auth()->user();

                    if ($user->hasRole('admin')) {
                        return \App\Models\Restaurant::pluck('name', 'id');
                    }

                    return $user->restaurants->pluck('name', 'id');
                })
                ->searchable()
                ->preload(),

            Forms\Components\Textarea::make('delivery_address')
                ->columnSpanFull(),

            Forms\Components\Select::make('status')
                ->required()
                ->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'preparing' => 'Preparing',
                    'delivering' => 'Delivering',
                    'delivered' => 'Delivered',
                ])
                ->native(false),

            Forms\Components\TextInput::make('total')
                ->required()
                ->numeric()
                ->default(0.00),
        ]);
    }

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('Order #')
                ->sortable(),

            Tables\Columns\TextColumn::make('user.name')
                ->label('Customer')
                ->searchable(),

            Tables\Columns\TextColumn::make('phone')
                ->label('Phone')
                ->searchable(),

            Tables\Columns\SelectColumn::make('status')
                ->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'preparing' => 'Preparing',
                    'delivering' => 'Delivering',
                    'delivered' => 'Delivered',
                ])
                ->sortable(),

            Tables\Columns\TextColumn::make('total')
                ->label('Total')
                ->money('USD', true)
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime()
                ->sortable(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->label('Status')
                ->options([
                    '' => 'All', // <--- Add empty key for all
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'preparing' => 'Preparing',
                    'delivering' => 'Delivering',
                    'delivered' => 'Delivered',
                ])
                ->default(''), // default to all
        ], layout: FiltersLayout::AboveContent)
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
