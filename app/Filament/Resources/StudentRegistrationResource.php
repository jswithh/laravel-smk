<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentRegistrationResource\Pages;
use App\Models\StudentRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Collection;

class StudentRegistrationResource extends Resource
{
    protected static ?string $model = StudentRegistration::class;

    // Custom navigation settings
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Student Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Student Registrations';
    protected static ?string $modelLabel = 'Student Registration';
    protected static ?string $pluralModelLabel = 'Student Registrations';
    protected static ?string $slug = 'student-registrations';

    /**
     * Mendefinisikan skema formulir untuk resource Pendaftaran Siswa di panel admin Filament.
     * Formulir ini mencakup bagian untuk informasi pribadi, keluarga, fisik,
     * alamat, tambahan, pendidikan sebelumnya, informasi orang tua, wali, bantuan sosial,
     * dan informasi pendaftaran.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bagian Informasi Pribadi
                Forms\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Nomor Registrasi')
                            ->default(fn() => StudentRegistration::generateRegistrationNumber())
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->numeric()
                            ->length(16),

                        Forms\Components\TextInput::make('family_card_number')
                            ->label('Nomor Kartu Keluarga')
                            ->required()
                            ->numeric()
                            ->length(16),

                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->numeric()
                            ->length(10),

                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'male'   => 'Laki-laki',
                                'female' => 'Perempuan',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->required()
                            ->maxDate(now()->subYears(10))
                            ->displayFormat('d F Y'),

                        Forms\Components\Select::make('religion')
                            ->label('Agama')
                            ->options([
                                'islam'      => 'Islam',
                                'protestant' => 'Protestan',
                                'catholic'   => 'Katolik',
                                'hindu'      => 'Hindu',
                                'buddha'     => 'Buddha',
                                'confucian'  => 'Konghucu',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                // Bagian Informasi Keluarga
                Forms\Components\Section::make('Informasi Keluarga')
                    ->schema([
                        Forms\Components\TextInput::make('child_order')
                            ->label('Anak ke-')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        Forms\Components\TextInput::make('siblings_count')
                            ->label('Jumlah Saudara')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        Forms\Components\Select::make('child_status')
                            ->label('Status Anak')
                            ->options([
                                'biological' => 'Anak Kandung',
                                'step'       => 'Anak Tiri',
                                'adopted'    => 'Anak Angkat',
                            ])
                            ->required(),
                    ])
                    ->columns(3),

                // Bagian Informasi Fisik
                Forms\Components\Section::make('Informasi Fisik')
                    ->schema([
                        Forms\Components\TextInput::make('height')
                            ->label('Tinggi Badan')
                            ->numeric()
                            ->required()
                            ->suffix('cm')
                            ->minValue(100)
                            ->maxValue(250),

                        Forms\Components\TextInput::make('weight')
                            ->label('Berat Badan')
                            ->numeric()
                            ->required()
                            ->suffix('kg')
                            ->minValue(25)
                            ->maxValue(200),

                        Forms\Components\Select::make('blood_type')
                            ->label('Golongan Darah')
                            ->options([
                                'a'  => 'A',
                                'b'  => 'B',
                                'ab' => 'AB',
                                'o'  => 'O',
                            ]),
                    ])
                    ->columns(3),

                // Bagian Informasi Alamat
                Forms\Components\Section::make('Informasi Alamat')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('village')
                            ->label('Desa/Kelurahan')
                            ->required(),

                        Forms\Components\TextInput::make('district')
                            ->label('Kecamatan')
                            ->required(),

                        Forms\Components\TextInput::make('city')
                            ->label('Kota/Kabupaten')
                            ->required(),

                        Forms\Components\TextInput::make('province')
                            ->label('Provinsi')
                            ->required(),

                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->required()
                            ->numeric()
                            ->length(5),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(3),

                // Bagian Informasi Tambahan
                Forms\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\Select::make('uniform_size')
                            ->label('Ukuran Seragam')
                            ->options([
                                's'     => 'S',
                                'm'     => 'M',
                                'l'     => 'L',
                                'xl'    => 'XL',
                                'xxl'   => 'XXL',
                                'xxxl'  => 'XXXL',
                                'jumbo' => 'Jumbo',
                            ])
                            ->required(),

                        Forms\Components\Select::make('residence_status')
                            ->label('Status Tempat Tinggal')
                            ->options([
                                'owned'  => 'Milik Sendiri',
                                'rented' => 'Sewa',
                            ])
                            ->required(),

                        Forms\Components\Select::make('transportation')
                            ->label('Transportasi')
                            ->options([
                                'walking'               => 'Jalan Kaki',
                                'motorcycle'            => 'Motor',
                                'car'                   => 'Mobil',
                                'public_transportation' => 'Transportasi Umum',
                            ])
                            ->required(),
                    ])
                    ->columns(3),

                // Bagian Pendidikan Sebelumnya
                Forms\Components\Section::make('Pendidikan Sebelumnya')
                    ->schema([
                        Forms\Components\Select::make('previous_school_type')
                            ->label('Jenis Sekolah Sebelumnya')
                            ->options([
                                'smpn'   => 'SMPN',
                                'smpit'  => 'SMPIT',
                                'smp'    => 'SMP',
                                'mtsn'   => 'MTsN',
                                'mts'    => 'MTs',
                                'pkbm'   => 'PKBM',
                                'ponpes' => 'Ponpes',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('previous_school_address')
                            ->label('Alamat Sekolah Sebelumnya')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('diploma_number')
                            ->label('Nomor Ijazah'),

                        Forms\Components\DatePicker::make('diploma_date')
                            ->label('Tanggal Ijazah')
                            ->displayFormat('d F Y'),

                        Forms\Components\TextInput::make('graduation_year')
                            ->label('Tahun Lulus')
                            ->numeric()
                            ->length(4)
                            ->required(),

                        Forms\Components\TextInput::make('student_phone')
                            ->label('Nomor Telepon Siswa')
                            ->tel()
                            ->required()
                            ->maxLength(15),
                    ])
                    ->columns(2),

                // Bagian Informasi Orang Tua
                Forms\Components\Section::make('Informasi Orang Tua')
                    ->schema([
                        // Informasi Ayah
                        Forms\Components\TextInput::make('father_name')
                            ->label('Nama Ayah')
                            ->required(),

                        Forms\Components\TextInput::make('father_nik')
                            ->label('NIK Ayah')
                            ->required()
                            ->numeric()
                            ->length(16),

                        Forms\Components\TextInput::make('father_occupation')
                            ->label('Pekerjaan Ayah')
                            ->required(),

                        Forms\Components\TextInput::make('father_income')
                            ->label('Penghasilan Ayah')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->inputMode('decimal')
                            ->step(1000)
                            ->minValue(0)
                            ->maxValue(999999999999)
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => str_replace(['.', ','], ['', '.'], $state)),

                        // Informasi Ibu
                        Forms\Components\TextInput::make('mother_name')
                            ->label('Nama Ibu')
                            ->required(),

                        Forms\Components\TextInput::make('mother_nik')
                            ->label('NIK Ibu')
                            ->required()
                            ->numeric()
                            ->length(16),

                        Forms\Components\TextInput::make('mother_occupation')
                            ->label('Pekerjaan Ibu')
                            ->required(),

                        Forms\Components\TextInput::make('mother_income')
                            ->label('Penghasilan Ibu')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->inputMode('decimal')
                            ->step(1000)
                            ->minValue(0)
                            ->maxValue(999999999999)
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => str_replace(['.', ','], ['', '.'], $state)),

                        Forms\Components\Textarea::make('parents_address')
                            ->label('Alamat Orang Tua')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('parents_phone')
                            ->label('Nomor Telepon Orang Tua')
                            ->tel()
                            ->required()
                            ->maxLength(15),
                    ])
                    ->columns(2),

                // Bagian Informasi Wali
                Forms\Components\Section::make('Informasi Wali')
                    ->schema([
                        Forms\Components\TextInput::make('guardian_name')
                            ->label('Nama Wali'),

                        Forms\Components\TextInput::make('guardian_occupation')
                            ->label('Pekerjaan Wali'),

                        Forms\Components\TextInput::make('guardian_income')
                            ->label('Penghasilan Wali')
                            ->numeric()
                            ->prefix('Rp')
                            ->inputMode('decimal')
                            ->step(1000)
                            ->minValue(0)
                            ->maxValue(999999999999)
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => str_replace(['.', ','], ['', '.'], $state)),

                        Forms\Components\Textarea::make('guardian_address')
                            ->label('Alamat Wali')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('guardian_phone')
                            ->label('Nomor Telepon Wali')
                            ->tel()
                            ->maxLength(15),
                    ])
                    ->columns(2)
                    ->collapsed(),

                // Bagian Bantuan Sosial
                Forms\Components\Section::make('Bantuan Sosial')
                    ->schema([
                        Forms\Components\TextInput::make('kks_number')
                            ->label('Nomor KKS'),

                        Forms\Components\TextInput::make('kip_number')
                            ->label('Nomor KIP'),
                    ])
                    ->columns(2)
                    ->collapsed(),

                // Bagian Informasi Pendaftaran
                Forms\Components\Section::make('Informasi Pendaftaran')
                    ->schema([
                        Forms\Components\Select::make('reference_source')
                            ->label('Sumber Referensi')
                            ->options([
                                'friend'   => 'Teman',
                                'teacher'  => 'Guru',
                                'alumni'   => 'Alumni',
                                'neighbor' => 'Tetangga',
                                'personal' => 'Pribadi',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('selected_major')
                            ->label('Jurusan Pilihan')
                            ->required(),

                        Forms\Components\Select::make('registration_status')
                            ->label('Status Pendaftaran')
                            ->options([
                                'pending'  => 'Menunggu',
                                'approved' => 'Diterima',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('pending')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('Nomor Registrasi')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('selected_major')
                    ->label('Jurusan Pilihan')
                    ->searchable()
                    ->sortable(),

                // Kolom status dengan badge
                Tables\Columns\TextColumn::make('registration_status')
                    ->label('Status Pendaftaran')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending'  => 'Menunggu',
                        'approved' => 'Diterima',
                        'rejected' => 'Ditolak',
                        default    => 'Tidak Diketahui',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('registration_status')
                    ->label('Status Pendaftaran')
                    ->options([
                        'pending'  => 'Menunggu',
                        'approved' => 'Diterima',
                        'rejected' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('selected_major')
                    ->label('Jurusan Pilihan')
                    ->options(function () {
                        return StudentRegistration::distinct()
                            ->pluck('selected_major', 'selected_major')
                            ->toArray();
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->label('Tanggal Pendaftaran')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Dari'),
                        Forms\Components\DatePicker::make('created_until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\Action::make('printRegistration')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn(StudentRegistration $record): string => route('student-registration.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn(StudentRegistration $record): bool => $record->registration_status === 'approved'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus')
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Perbarui Status')
                        ->icon('heroicon-o-check-circle')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'pending'  => 'Menunggu',
                                    'approved' => 'Diterima',
                                    'rejected' => 'Ditolak',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function ($record) use ($data) {
                                $record->update(['registration_status' => $data['status']]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->emptyStateHeading('Belum ada pendaftaran siswa')
            ->emptyStateDescription('Mulailah dengan membuat pendaftaran siswa baru.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStudentRegistrations::route('/'),
            'create' => Pages\CreateStudentRegistration::route('/create'),
            'edit'   => Pages\EditStudentRegistration::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->full_name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'registration_number',
            'full_name',
            'nik',
            'nisn',
            'email',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Registrasi' => $record->registration_number,
            'Jurusan'    => $record->selected_major,
            'Status'     => Str::title($record->registration_status),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('registration_status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('registration_status', 'pending')->count() > 0 ? 'warning' : null;
    }
}
