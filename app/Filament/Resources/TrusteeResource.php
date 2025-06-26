<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrusteeResource\Pages;
use App\Filament\Resources\TrusteeResource\RelationManagers;
use App\Models\Trustee;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrusteeResource extends Resource
{
    protected static ?string $model = Trustee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pages';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')->image()->directory('trustees')->nullable(),
                TextInput::make('name')->required(),
                TextInput::make('designation')
                    ->label('Designation')
                    ->placeholder('e.g. Chairman, Secretary, etc.')
                    ->nullable(),
                TextInput::make('email')->email(),
                TextInput::make('phone'),
                Textarea::make('address'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Photo')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl('https://via.placeholder.com/40x40?text=No+Image'),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('designation')
                    ->label('Designation')
                    ->limit(20)
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Phone'),

                TextColumn::make('address')
                    ->label('Address')
                    ->limit(30),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrustees::route('/'),
            'create' => Pages\CreateTrustee::route('/create'),
            'edit' => Pages\EditTrustee::route('/{record}/edit'),
        ];
    }
}
