<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Models\Profile;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    // Mengubah icon yang lebih sesuai untuk Profile
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    // Menambahkan label yang lebih deskriptif
    protected static ?string $navigationLabel = 'School Profile';

    // Mengatur posisi di navigasi
    protected static ?int $navigationSort = 1;

    // Mengatur plural dan singular label
    protected static ?string $modelLabel = 'Profile';
    protected static ?string $pluralModelLabel = 'Profiles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->description('Manage the basic school information here.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter school name')
                            ->live(onBlur: true),

                        Textarea::make('short_description')
                            ->required()
                            ->maxLength(1000)
                            ->placeholder('Enter school description')
                            ->columnSpanFull(),
                    ]),

                Section::make('School Assets')
                    ->description('Upload school logo and favicon here.')
                    ->schema([
                        FileUpload::make('logo')
                            ->image()
                            ->required()
                            ->maxSize(1024)
                            ->directory('school/logos')
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400')
                            ->columnSpan(1),

                        FileUpload::make('favicon')
                            ->image()
                            ->required()
                            ->maxSize(512)
                            ->directory('school/favicons')
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('64')
                            ->imageResizeTargetHeight('64')
                            ->columnSpan(1),
                    ])->columns(2),

                Section::make('Contact Information')
                    ->description('Manage school contact details here.')
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('school@example.com'),

                        TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder('+62xxx-xxxx-xxxx'),

                        Textarea::make('address')
                            ->required()
                            ->maxLength(500)
                            ->placeholder('Enter complete address')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->square()
                    ->size(40),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->copyMessageDuration(1500),

                TextColumn::make('phone_number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Phone number copied')
                    ->copyMessageDuration(1500),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-m-pencil-square'),

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-m-eye'),
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
            'index' => Pages\ListProfiles::route('/'),
            'create' => Pages\CreateProfile::route('/create'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
        ];
    }

    // Menambahkan global search
    public static function getGlobalSearchAttributes(): array
    {
        return ['name', 'email', 'phone_number', 'address'];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Settings';
    }

    // Mengkustomisasi tampilan hasil search

}
