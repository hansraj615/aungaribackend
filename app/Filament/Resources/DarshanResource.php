<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DarshanResource\Pages;
use App\Filament\Resources\DarshanResource\RelationManagers;
use App\Models\Darshan;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DarshanResource extends Resource
{
    protected static ?string $model = Darshan::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';

    protected static ?string $navigationGroup = 'Pages';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->rows(3),

                TextInput::make('youtube_url')
                    ->label('YouTube URL')
                    ->required()
                    ->url(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('youtube_url')->label('URL')->limit(40),
                Tables\Columns\TextColumn::make('created_at')->label('Added On')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDarshans::route('/'),
            'create' => Pages\CreateDarshan::route('/create'),
            'edit' => Pages\EditDarshan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Darshan Videos';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
