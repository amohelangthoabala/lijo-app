<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderItemsResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Management';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->whereIn('restaurant_id', $user->restaurants->pluck('id'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Customer')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('restaurant_id')
                    ->label('Restaurant')
                    ->required()
                    ->options(function () {
                        $user = auth()->user();

                        if ($user->hasRole('admin')) {
                            return Restaurant::pluck('name', 'id');
                        }

                        return $user->restaurants->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('type')
                    ->label('Order Type')
                    ->required()
                    ->options([
                        'pickup' => 'Pickup',
                        'delivery' => 'Delivery',
                    ])
                    ->native(false),

                Forms\Components\Textarea::make('delivery_address')
                    ->label('Delivery Address')
                    ->placeholder('Required only if delivery')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('delivery_cost')
                    ->label('Delivery Cost (LSL)')
                    ->numeric()
                    ->default(0.00)
                    ->required(),

                Forms\Components\TextInput::make('total')
                    ->label('Total (LSL)')
                    ->numeric()
                    ->default(0.00)
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Order Status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'preparing' => 'Preparing',
                        'delivering' => 'Delivering',
                        'delivered' => 'Delivered',
                    ])
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('restaurant.name')->label('Restaurant')->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->sortable(),
                Tables\Columns\TextColumn::make('status')->sortable(),
                Tables\Columns\TextColumn::make('delivery_cost')->label('Delivery Cost')->numeric(),
                Tables\Columns\TextColumn::make('total')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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