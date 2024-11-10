<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentRegistrationAcademicYearResource\Pages;
use App\Models\StudentRegistrationAcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentRegistrationAcademicYearResource extends Resource
{
    protected static ?string $model = StudentRegistrationAcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Tahun Ajaran PPDB';
    protected static ?string $navigationGroup = 'Student Management';

    protected static ?string $modelLabel = 'Tahun Ajaran PPDB';
    protected static ?string $pluralModelLabel = 'Data Tahun Ajaran PPDB';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Tahun Ajaran')
                    ->schema([
                        Forms\Components\TextInput::make('academic_year')
                            ->required()
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2100)
                            ->label('Tahun Ajaran')
                            ->placeholder('Masukkan tahun ajaran (contoh: 2024)'),

                        Forms\Components\Section::make('Biaya Pendaftaran')
                            ->schema([
                                Forms\Components\Repeater::make('registrationFees')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Nama Biaya')
                                            ->placeholder('Masukkan nama biaya'),

                                        Forms\Components\TextInput::make('amount')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->mask('999,999,999')
                                            ->label('Jumlah')
                                            ->placeholder('Masukkan jumlah biaya'),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->reorderable()
                                    ->addActionLabel('Tambah Biaya Baru')
                                    ->cloneable()
                            ])
                    ])
                    ->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('registrationFees.name')
                    ->label('Daftar Biaya')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('registrationFees.amount')
                    ->label('Jumlah Biaya')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->money('idr'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Biaya')
                    ->money('idr')
                    ->getStateUsing(function ($record): int {
                        return $record->registrationFees->sum('amount');
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('academic_year', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year')
                    ->options(fn() => StudentRegistrationAcademicYear::pluck('academic_year', 'academic_year'))
                    ->label('Tahun Ajaran')
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('7xl'),
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
            'index' => Pages\ListStudentRegistrationAcademicYears::route('/'),
            'create' => Pages\CreateStudentRegistrationAcademicYear::route('/create'),
            'edit' => Pages\EditStudentRegistrationAcademicYear::route('/{record}/edit'),
        ];
    }
}
