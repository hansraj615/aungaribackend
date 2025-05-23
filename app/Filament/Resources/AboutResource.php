<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutResource\Pages;
use App\Models\About;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use App\Helpers\TranslatorHelper;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Notifications\Notification;

class AboutResource extends Resource
{
    protected static ?string $model = About::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Pages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'lg' => 12,
                ])
                    ->schema([
                        Section::make('Title & Slug')
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 9,
                            ])
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->schema([
                                        TextInput::make('title_en')
                                            ->label('Title (English)')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                                $translated = TranslatorHelper::translate($state ?? '', 'hi');
                                                $set('title_hi', $translated);
                                            }),

                                        Actions::make([
                                            Action::make('translate_title')
                                                ->label('Translate Title to Hindi')
                                                ->action(function (Set $set, Get $get) {
                                                    $title = $get('title_en');
                                                    if (empty($title)) {
                                                        Notification::make()
                                                            ->warning()
                                                            ->title('No title to translate')
                                                            ->body('Please add a title in English first.')
                                                            ->send();
                                                        return;
                                                    }

                                                    try {
                                                        $translated = TranslatorHelper::translate($title, 'hi');
                                                        if ($translated !== null) {
                                                            $set('title_hi', $translated);
                                                            Notification::make()
                                                                ->success()
                                                                ->title('Title translated')
                                                                ->send();
                                                        } else {
                                                            throw new \Exception('Translation failed');
                                                        }
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

                                        TextInput::make('title_hi')
                                            ->label('Title (Hindi)')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                                $translated = TranslatorHelper::translate($state ?? '', 'en');
                                                $set('title_en', $translated);
                                            }),

                                        TextInput::make('slug')
                                            ->required(),
                                    ]),
                            ]),

                        Section::make('Media')
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 3,
                            ])
                            ->schema([
                                FileUpload::make('images')
                                    ->multiple()
                                    ->image()
                                    ->directory('about-images')
                                    ->columnSpan('full')
                                    ->imagePreviewHeight('150')
                                    ->downloadable()
                                    ->openable()
                                    ->preserveFilenames(),
                            ])
                            ->collapsible()
                            ->collapsed(true),
                    ]),

                Grid::make([
                    'default' => 1,
                    'lg' => 12
                ])
                    ->schema([
                        Section::make('Content')
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 12,
                            ])
                            ->schema([
                                RichEditor::make('body_en')
                                    ->label('Content (English)')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('uploads')
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ])
                                    ->columnSpan('full')
                                    ->required(),

                                Actions::make([
                                    Action::make('translate_content')
                                        ->label('Translate Content to Hindi')
                                        ->action(function (Set $set, Get $get) {
                                            $content = $get('body_en');
                                            if (empty($content)) {
                                                Notification::make()
                                                    ->warning()
                                                    ->title('No content to translate')
                                                    ->body('Please add some content in English first.')
                                                    ->send();
                                                return;
                                            }

                                            try {
                                                // Use the specialized editor translation method
                                                $translated = TranslatorHelper::translateEditor($content, 'hi');

                                                if ($translated !== null) {
                                                    $set('body_hi', $translated);
                                                    Notification::make()
                                                        ->success()
                                                        ->title('Content translated')
                                                        ->send();
                                                } else {
                                                    throw new \Exception('Translation failed');
                                                }
                                            } catch (\Exception $e) {
                                                Notification::make()
                                                    ->danger()
                                                    ->title('Translation failed')
                                                    ->body('Translation service is currently unavailable. Please try again later.')
                                                    ->send();
                                            }
                                        })
                                        ->color('primary')
                                        ->button(),
                                ])->columnSpanFull(),

                                RichEditor::make('body_hi')
                                    ->label('Content (Hindi)')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('uploads')
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ])
                                    ->columnSpan('full')
                                    ->extraAttributes([
                                        'data-lang' => 'hi',
                                        'dir' => 'auto',
                                        'style' => 'font-family: "Noto Sans Devanagari", Arial, sans-serif;',
                                    ]),
                            ])
                            ->collapsible(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_en')->label('Title (English)'),
                TextColumn::make('title_hi')->label('Title (Hindi)'),
                TextColumn::make('slug'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canViewAny(): bool
    {
        return true; // TODO: Implement proper authorization
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbouts::route('/'),
            'edit' => Pages\EditAbout::route('/{record}/edit'),
        ];
    }
}
