<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestaurantResource\Pages;
use App\Filament\Resources\RestaurantResource\RelationManagers;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

   // protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Management';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return parent::getEloquentQuery();
        }

        // Non-admins see only their restaurants
        return parent::getEloquentQuery()
            ->whereHas('users', fn ($query) => $query->where('user_id', $user->id));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('slug', Str::slug($state))),
                
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                /*
                * TODO;
                * future implemntation for multi tenant; 
                */ 
                // TextInput::make('domain')
                //     ->required()
                //     ->unique(ignoreRecord: true)
                //     ->placeholder('e.g. pizza.lijo.co.ls'),

                FileUpload::make('logo')
                    ->image()
                    ->directory('logos'),
                 Select::make('users')
                    ->required()
                    ->label('Owners')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->square(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('domain'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}
