<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutResource\Pages;
use App\Filament\Resources\AboutResource\RelationManagers;
use App\Models\About;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

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
                        Section::make('Titel & Slug')
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
                                        TextInput::make('title')
                                            ->required(),

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
                        Section::make('Body')
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 12,
                            ])
                            ->schema([
                                TinyEditor::make('body')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsVisibility('public')
                                    ->fileAttachmentsDirectory('uploads')
                                    ->profile('default')
                                    ->ltr()
                                    ->columnSpan('full')
                                    ->required(),
                            ])
                            ->collapsible(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('slug')
                // TextColumn::make('body')
                //     ->sortable()
                //     ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_about');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbouts::route('/'),
            // 'create' => Pages\CreateAbout::route('/create'),
            'edit' => Pages\EditAbout::route('/{record}/edit'),
        ];
    }
}
