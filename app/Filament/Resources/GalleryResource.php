<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Filament\Resources\GalleryResource\RelationManagers;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Helpers\TranslatorHelper;
use Filament\Notifications\Notification;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Gallery Management';

    protected static ?int $navigationSort = 2;

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
                                            Forms\Components\TextInput::make('title_en')
                                                ->label('Title (English)')
                                                ->required()
                                                ->maxLength(255)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                    // Auto translate to Hindi
                                                    $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                    if ($translated) {
                                                        $set('title_hi', $translated);
                                                    }
                                                }),
                                            Forms\Components\Textarea::make('description_en')
                                                ->label('Description (English)')
                                                ->maxLength(65535)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                    // Auto translate to Hindi
                                                    $translated = TranslatorHelper::translateRichContent($state ?? '', 'hi');
                                                    if ($translated) {
                                                        $set('description_hi', $translated);
                                                    }
                                                }),
                                            Forms\Components\Actions::make([
                                                Forms\Components\Actions\Action::make('translate_to_hindi')
                                                    ->label('Translate to Hindi')
                                                    ->icon('heroicon-m-language')
                                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                                        $title = $get('title_en');
                                                        $description = $get('description_en');

                                                        if (empty($title) && empty($description)) {
                                                            Notification::make()
                                                                ->warning()
                                                                ->title('No content to translate')
                                                                ->body('Please add content in English first.')
                                                                ->send();
                                                            return;
                                                        }

                                                        try {
                                                            if (!empty($title)) {
                                                                $translatedTitle = TranslatorHelper::translate($title, 'hi');
                                                                if ($translatedTitle) {
                                                                    $set('title_hi', $translatedTitle);
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
                                            Forms\Components\TextInput::make('title_hi')
                                                ->label('Title (Hindi)')
                                                ->required()
                                                ->maxLength(255)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                    // Auto translate to English
                                                    $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                    if ($translated) {
                                                        $set('title_en', $translated);
                                                    }
                                                }),
                                            Forms\Components\Textarea::make('description_hi')
                                                ->label('Description (Hindi)')
                                                ->maxLength(65535)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                    // Auto translate to English
                                                    $translated = TranslatorHelper::translateRichContent($state ?? '', 'en');
                                                    if ($translated) {
                                                        $set('description_en', $translated);
                                                    }
                                                }),
                                            Forms\Components\Actions::make([
                                                Forms\Components\Actions\Action::make('translate_to_english')
                                                    ->label('Translate to English')
                                                    ->icon('heroicon-m-language')
                                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                                        $title = $get('title_hi');
                                                        $description = $get('description_hi');

                                                        if (empty($title) && empty($description)) {
                                                            Notification::make()
                                                                ->warning()
                                                                ->title('No content to translate')
                                                                ->body('Please add content in Hindi first.')
                                                                ->send();
                                                            return;
                                                        }

                                                        try {
                                                            if (!empty($title)) {
                                                                $translatedTitle = TranslatorHelper::translate($title, 'en');
                                                                if ($translatedTitle) {
                                                                    $set('title_en', $translatedTitle);
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
                        Forms\Components\Section::make('Gallery Images')
                            ->schema([
                                Forms\Components\Repeater::make('images')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\FileUpload::make('image_path')
                                            ->label('Image')
                                            ->image()
                                            ->imageEditor()
                                            ->required()
                                            ->maxSize(5120) // 5MB
                                            ->directory('gallery'),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('caption_en')
                                                    ->label('Caption (English)')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                        $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                        if ($translated) {
                                                            $set('caption_hi', $translated);
                                                        }
                                                    }),
                                                Forms\Components\TextInput::make('caption_hi')
                                                    ->label('Caption (Hindi)')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                        $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                        if ($translated) {
                                                            $set('caption_en', $translated);
                                                        }
                                                    }),
                                            ]),
                                    ])
                                    ->orderable('display_order')
                                    ->orderColumn('display_order')
                                    ->defaultItems(1)
                                    ->addActionLabel('Add Image')
                                    ->columns(1),
                            ])->columnSpanFull(),
                        Forms\Components\Select::make('occasion_id')
                            ->relationship('occasion', 'name_en')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name_en')
                                            ->label('Name (English)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('name_hi')
                                            ->label('Name (Hindi)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description_en')
                                            ->label('Description (English)')
                                            ->maxLength(65535),
                                        Forms\Components\Textarea::make('description_hi')
                                            ->label('Description (Hindi)')
                                            ->maxLength(65535),
                                    ]),
                            ]),
                        Forms\Components\DateTimePicker::make('event_date')
                            ->label('Event Date & Time'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Gallery'),
                        Forms\Components\TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover Image')
                    ->square(),
                Tables\Columns\TextColumn::make('title_en')
                    ->label('Title (English)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title_hi')
                    ->label('Title (Hindi)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('occasion.name_en')
                    ->label('Occasion (English)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('occasion.name_hi')
                    ->label('Occasion (Hindi)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('images_count')
                    ->counts('images')
                    ->label('Images')
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('display_order')
            ->filters([
                Tables\Filters\SelectFilter::make('occasion')
                    ->relationship('occasion', 'name_en')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueLabel('Featured')
                    ->falseLabel('Not Featured')
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
            ])
            ->reorderable('display_order');
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
