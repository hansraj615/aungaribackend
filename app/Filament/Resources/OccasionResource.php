<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OccasionResource\Pages;
use App\Filament\Resources\OccasionResource\RelationManagers;
use App\Models\Occasion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use App\Helpers\TranslatorHelper;
use Filament\Notifications\Notification;

class OccasionResource extends Resource
{
    protected static ?string $model = Occasion::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Pages';
    // protected static ?string $navigationParentItem = 'Gallery Management';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Section::make('Content')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Group::make([
                                            Forms\Components\TextInput::make('name_en')
                                                ->label('Name (English)')
                                                ->required()
                                                ->maxLength(255)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                    if ($operation === 'create') {
                                                        $set('slug', Str::slug($state));
                                                    }
                                                }),
                                            Forms\Components\Textarea::make('description_en')
                                                ->label('Description (English)')
                                                ->maxLength(65535),
                                            Forms\Components\Actions::make([
                                                Forms\Components\Actions\Action::make('translate_to_hindi')
                                                    ->label('Translate to Hindi')
                                                    ->icon('heroicon-m-language')
                                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                                        $name = $get('name_en');
                                                        $description = $get('description_en');

                                                        if (empty($name) && empty($description)) {
                                                            Notification::make()
                                                                ->warning()
                                                                ->title('No content to translate')
                                                                ->body('Please add content in English first.')
                                                                ->send();
                                                            return;
                                                        }

                                                        try {
                                                            if (!empty($name)) {
                                                                $translatedName = TranslatorHelper::translate($name, 'hi');
                                                                if ($translatedName) {
                                                                    $set('name_hi', $translatedName);
                                                                }
                                                            }

                                                            if (!empty($description)) {
                                                                $translatedDesc = TranslatorHelper::translateRichContent($description, 'hi');
                                                                if ($translatedDesc) {
                                                                    $set('description_hi', $translatedDesc);
                                                                }
                                                            }

                                                            Notification::make()
                                                                ->success()
                                                                ->title('Content translated')
                                                                ->send();
                                                        } catch (\Exception $e) {
                                                            Notification::make()
                                                                ->danger()
                                                                ->title('Translation failed')
                                                                ->body('Translation service is currently unavailable.')
                                                                ->send();
                                                        }
                                                    })
                                                    ->color('primary')
                                                    ->button(),
                                            ]),
                                        ]),
                                        Forms\Components\Group::make([
                                            Forms\Components\TextInput::make('name_hi')
                                                ->label('Name (Hindi)')
                                                ->required()
                                                ->maxLength(255),
                                            Forms\Components\Textarea::make('description_hi')
                                                ->label('Description (Hindi)')
                                                ->maxLength(65535),
                                            Forms\Components\Actions::make([
                                                Forms\Components\Actions\Action::make('translate_to_english')
                                                    ->label('Translate to English')
                                                    ->icon('heroicon-m-language')
                                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                                        $name = $get('name_hi');
                                                        $description = $get('description_hi');

                                                        if (empty($name) && empty($description)) {
                                                            Notification::make()
                                                                ->warning()
                                                                ->title('No content to translate')
                                                                ->body('Please add content in Hindi first.')
                                                                ->send();
                                                            return;
                                                        }

                                                        try {
                                                            if (!empty($name)) {
                                                                $translatedName = TranslatorHelper::translate($name, 'en');
                                                                if ($translatedName) {
                                                                    $set('name_en', $translatedName);
                                                                }
                                                            }

                                                            if (!empty($description)) {
                                                                $translatedDesc = TranslatorHelper::translateRichContent($description, 'en');
                                                                if ($translatedDesc) {
                                                                    $set('description_en', $translatedDesc);
                                                                }
                                                            }

                                                            Notification::make()
                                                                ->success()
                                                                ->title('Content translated')
                                                                ->send();
                                                        } catch (\Exception $e) {
                                                            Notification::make()
                                                                ->danger()
                                                                ->title('Translation failed')
                                                                ->body('Translation service is currently unavailable.')
                                                                ->send();
                                                        }
                                                    })
                                                    ->color('primary')
                                                    ->button(),
                                            ]),
                                        ]),
                                    ]),
                            ])->columnSpanFull(),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Occasion::class, 'slug', ignoreRecord: true),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_en')
                    ->label('Name (English)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_hi')
                    ->label('Name (Hindi)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('galleries_count')
                    ->counts('galleries')
                    ->label('Images')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
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
            'index' => Pages\ListOccasions::route('/'),
            'create' => Pages\CreateOccasion::route('/create'),
            'edit' => Pages\EditOccasion::route('/{record}/edit'),
        ];
    }
}
