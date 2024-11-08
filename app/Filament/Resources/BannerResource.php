<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Resources\Resource;
use Filament\Resources\Form;
use Filament\Resources\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    // Mengatur icon navigasi yang sesuai
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    // Menambahkan label yang lebih deskriptif
    protected static ?string $navigationLabel = 'Banners';

    // Mengatur posisi di navigasi
    protected static ?int $navigationSort = 2;

    // Mengatur plural dan singular label
    protected static ?string $modelLabel = 'Banner';
    protected static ?string $pluralModelLabel = 'Banners';

    /**
     * Mendefinisikan form untuk create dan edit Banner
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make('Informasi Banner')
                    ->description('Kelola informasi dasar banner di sini.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Banner')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama banner')
                            ->live(onBlur: true),

                        TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('https://contoh.com'),

                        FileUpload::make('image')
                            ->label('Gambar Banner')
                            ->image()
                            ->required()
                            ->maxSize(2048) // Maksimum 2MB
                            ->directory('banners/images')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(1200)
                            ->imageResizeTargetHeight(600)
                            ->helperText('Unggah gambar dengan resolusi minimal 1200x600px.'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Toggling ini akan mengaktifkan atau menonaktifkan banner.'),

                        TextInput::make('order')
                            ->label('Urutan')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->placeholder('1')
                            ->helperText('Menentukan urutan tampilnya banner. Semakin kecil angkanya, semakin dulu ditampilkan.'),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Mendefinisikan tabel untuk menampilkan daftar Banner
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->square()
                    ->size(50)
                    ->circular(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('url')
                    ->label('URL')
                    ->copyable()
                    ->copyMessage('URL disalin')
                    ->copyMessageDuration(1500)
                    ->limit(50)
                    ->tooltip(fn($record) => $record->url),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable()
                    ->action(function (Banner $record, bool $state) {
                        $record->update(['is_active' => $state]);
                    }),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Hanya Aktif')
                    ->toggle(),
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

    /**
     * Mendefinisikan relasi yang terkait jika ada
     */
    public static function getRelations(): array
    {
        return [
            // Tambahkan relasi jika Banner memiliki hubungan dengan model lain
        ];
    }

    /**
     * Mendefinisikan halaman yang digunakan oleh resource ini
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }

    /**
     * Menambahkan atribut yang akan digunakan untuk pencarian global
     */
    public static function getGlobalSearchAttributes(): array
    {
        return ['name', 'url'];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Settings';
    }
}
