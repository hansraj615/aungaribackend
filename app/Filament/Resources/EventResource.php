<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Helpers\TranslatorHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('name_en')
                                ->label('Event Name (English)')
                                ->required(),
                            Forms\Components\Textarea::make('description_en')
                                ->label('Description (English)'),

                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('translate_to_hindi')
                                    ->label('Translate to Hindi')
                                    ->icon('heroicon-m-language')
                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                        $title = $get('name_en');
                                        $description = $get('description_en');

                                        if (empty($title) && empty($description)) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Nothing to translate')
                                                ->body('Please enter English title or description.')
                                                ->send();
                                            return;
                                        }

                                        try {
                                            if (!empty($title)) {
                                                $set('name_hi', TranslatorHelper::translate($title, 'hi'));
                                            }

                                            if (!empty($description)) {
                                                $set('description_hi', TranslatorHelper::translateRichContent($description, 'hi'));
                                            }

                                            Notification::make()
                                                ->success()
                                                ->title('Translated to Hindi')
                                                ->send();
                                        } catch (\Exception $e) {
                                            Notification::make()
                                                ->danger()
                                                ->title('Translation failed')
                                                ->body('Something went wrong during translation.')
                                                ->send();
                                        }
                                    }),
                            ]),
                        ]),

                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('name_hi')
                                ->label('Event Name (Hindi)'),
                            Forms\Components\Textarea::make('description_hi')
                                ->label('Description (Hindi)'),

                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('translate_to_english')
                                    ->label('Translate to English')
                                    ->icon('heroicon-m-language')
                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                        $title = $get('name_hi');
                                        $description = $get('description_hi');

                                        if (empty($title) && empty($description)) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Nothing to translate')
                                                ->body('Please enter Hindi title or description.')
                                                ->send();
                                            return;
                                        }

                                        try {
                                            if (!empty($title)) {
                                                $set('name_en', TranslatorHelper::translate($title, 'en'));
                                            }

                                            if (!empty($description)) {
                                                $set('description_en', TranslatorHelper::translateRichContent($description, 'en'));
                                            }

                                            Notification::make()
                                                ->success()
                                                ->title('Translated to English')
                                                ->send();
                                        } catch (\Exception $e) {
                                            Notification::make()
                                                ->danger()
                                                ->title('Translation failed')
                                                ->body('Something went wrong during translation.')
                                                ->send();
                                        }
                                    }),
                            ]),
                        ]),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('start_date')->required(),
                        Forms\Components\DatePicker::make('end_date')->required(),
                    ]),

                    Forms\Components\Toggle::make('show_in_banner')->label('Show in Banner'),

                    Forms\Components\FileUpload::make('image')
                        ->label('Event Image')
                        ->image()
                        ->directory('events')
                        ->disk('public')
                        ->maxSize(2048)
                        ->nullable(),

                    // Forms\Components\Select::make('trustee_id')
                    //     ->label('Trustee')
                    //     ->relationship('trustee', 'name')
                    //     ->searchable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Image')->disk('public')->circular(),
                Tables\Columns\TextColumn::make('name_en')->label('Event Name'),
                Tables\Columns\TextColumn::make('start_date'),
                Tables\Columns\TextColumn::make('end_date'),
                Tables\Columns\BooleanColumn::make('show_in_banner')->label('Banner'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
