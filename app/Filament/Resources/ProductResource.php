<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use App\Models\Restaurant;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

       // protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Management';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return parent::getEloquentQuery();
        }

        // Products from the restaurants they own
        return parent::getEloquentQuery()->whereHas('restaurant.users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Select::make('restaurant_id')
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

            TextInput::make('name')
                ->required()
                ->maxLength(255),

            Textarea::make('description'),

            TextInput::make('base_price')
                ->numeric()
                ->helperText('Used if variants do not override the price.')
                ->required(),

            Repeater::make('images')
                ->relationship('images')
                ->label('Product Images')
                ->schema([
                    FileUpload::make('image_path')
                        ->image()
                        ->directory('products')
                        ->required()
                        ->preserveFilenames(),
                ])
                ->minItems(1)
                ->columns(1),

            Repeater::make('variants')
                ->relationship('variants')
                ->label('Product Variants')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->label('Variant Name'),

                    TextInput::make('price')
                        ->numeric()
                        ->label('Override Price'),
                ])
                ->columns(2)
                ->defaultItems(0)
                ->collapsible()
                ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('base_price')->money('ZAR', true),
                ImageColumn::make('images')
                    ->label('Main Image')
                    ->getStateUsing(fn($record) => $record->images->first()?->image_path)
                    ->disk('public')
                    ->visibility('public') // <-- helps clarify intention
                    ->circular()
            ])
            ->defaultSort('created_at', 'desc')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
