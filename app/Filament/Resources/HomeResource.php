<?php

namespace App\Filament\Resources;

use App\Models\Home;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\HomeResource\Pages;
use App\Helpers\TranslatorHelper;
use Filament\Forms\Get;
use Filament\Forms\Set;

class HomeResource extends Resource
{
    protected static ?string $model = Home::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Home Page';
    protected static ?string $modelLabel = 'Home Page Content';

    protected static ?string $navigationGroup = 'Pages';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Home Page Content')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make('Hero Section')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('Hero Carousel')
                                    ->schema([
                                        FileUpload::make('hero_section.images')
                                            ->multiple()
                                            ->image()
                                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                                            ->directory('home/hero')
                                            ->imageEditor()
                                            ->reorderable()
                                            ->downloadable()
                                            ->openable()
                                            ->columnSpanFull(),

                                        TextInput::make('hero_section.title_en')
                                            ->label('Title (English)')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                $set('hero_section.title_hi', $translated);
                                            }),

                                        TextInput::make('hero_section.title_hi')
                                            ->label('Title (Hindi)')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                $set('hero_section.title_en', $translated);
                                            }),

                                        TextInput::make('hero_section.subtitle_en')
                                            ->label('Subtitle (English)')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                $set('hero_section.subtitle_hi', $translated);
                                            }),

                                        TextInput::make('hero_section.subtitle_hi')
                                            ->label('Subtitle (Hindi)')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                $set('hero_section.subtitle_en', $translated);
                                            }),
                                    ]),
                            ]),

                        Tabs\Tab::make('Dynamic Sections')
                            ->icon('heroicon-o-squares-plus')
                            ->schema([
                                Forms\Components\Section::make('Custom Sections')
                                    ->description('Add and arrange content sections')
                                    ->schema([
                                        Forms\Components\Repeater::make('dynamic_sections')
                                            ->label('Page Sections')
                                            ->addActionLabel('Add New Section')
                                            ->collapsible()
                                            ->cloneable()
                                            ->itemLabel(fn(array $state): ?string =>
                                            $state['title_en'] ?? 'New Section')
                                            ->schema([
                                                Select::make('type')
                                                    ->options([
                                                        'text' => 'Text Content',
                                                        'image_text' => 'Image + Text',
                                                        'cards' => 'Card Grid',
                                                        'video' => 'Video Embed',
                                                        'cta' => 'Call to Action',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->columnSpanFull(),

                                                TextInput::make('title_en')
                                                    ->label('Section Title (English)')
                                                    ->required()
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                        $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                        $set('title_hi', $translated);
                                                    }),

                                                TextInput::make('title_hi')
                                                    ->label('Section Title (Hindi)')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                        $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                        $set('title_en', $translated);
                                                    }),

                                                RichEditor::make('content_en')
                                                    ->label('Content (English)')
                                                    ->visible(fn($get) => !in_array($get('type'), ['cards', 'video']))
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                        $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                        $set('content_hi', $translated);
                                                    }),

                                                RichEditor::make('content_hi')
                                                    ->label('Content (Hindi)')
                                                    ->visible(fn($get) => !in_array($get('type'), ['cards', 'video']))
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                        $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                        $set('content_en', $translated);
                                                    }),

                                                FileUpload::make('image')
                                                    ->image()
                                                    ->maxSize(5120)
                                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                                                    ->directory('home/sections')
                                                    ->imageEditor()
                                                    ->visible(fn($get) =>
                                                    in_array($get('type'), ['image_text', 'cards', 'cta']))
                                                    ->columnSpanFull(),

                                                TextInput::make('video_url')
                                                    ->url()
                                                    ->visible(fn($get) => $get('type') === 'video')
                                                    ->columnSpanFull(),

                                                Forms\Components\Repeater::make('items')
                                                    ->visible(fn($get) => $get('type') === 'cards')
                                                    ->schema([
                                                        TextInput::make('title_en')
                                                            ->label('Card Title (English)')
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                                $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                                $set('title_hi', $translated);
                                                            }),

                                                        TextInput::make('title_hi')
                                                            ->label('Card Title (Hindi)')
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                                $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                                $set('title_en', $translated);
                                                            }),

                                                        Textarea::make('description_en')
                                                            ->label('Description (English)')
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                                $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                                $set('description_hi', $translated);
                                                            }),

                                                        Textarea::make('description_hi')
                                                            ->label('Description (Hindi)')
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                                $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                                $set('description_en', $translated);
                                                            }),

                                                        TextInput::make('icon')
                                                            ->maxLength(50),
                                                    ])
                                                    ->grid(2),

                                                TextInput::make('button_text_en')
                                                    ->label('Button Text (English)')
                                                    ->visible(fn($get) => $get('type') === 'cta')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                        $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                        $set('button_text_hi', $translated);
                                                    }),

                                                TextInput::make('button_text_hi')
                                                    ->label('Button Text (Hindi)')
                                                    ->visible(fn($get) => $get('type') === 'cta')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                        $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                        $set('button_text_en', $translated);
                                                    }),

                                                TextInput::make('button_link')
                                                    ->visible(fn($get) => $get('type') === 'cta'),
                                            ])
                                            ->defaultItems(1)
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Display Settings')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Forms\Components\Section::make('Content Display')
                                    ->schema([
                                        Toggle::make('show_read_more')
                                            ->label('Enable "Read More" functionality')
                                            ->default(true),

                                        TextInput::make('read_more_char_limit')
                                            ->numeric()
                                            ->minValue(50)
                                            ->maxValue(500)
                                            ->default(150)
                                            ->suffix('characters'),

                                        Toggle::make('show_section_navigation')
                                            ->label('Show section quick navigation')
                                            ->default(true),
                                    ])->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomes::route('/'),
            'create' => Pages\CreateHome::route('/create'),
            'edit' => Pages\EditHome::route('/{record}/edit'),
        ];
    }
}
