<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialMediaResource\Pages;
use App\Models\SocialMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SocialMediaResource extends Resource
{
    protected static ?string $model = SocialMedia::class;
    protected static ?string $navigationIcon = 'heroicon-o-share';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Social Media Links';
    protected static ?string $modelLabel = 'Social Media Link';
    protected static ?string $pluralModelLabel = 'Social Media Links';
    protected static ?string $slug = 'social-media-links';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'url'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Social Media Information')
                    ->description('Manage your social media platform details here.')
                    ->icon('heroicon-o-share')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignorable: fn (?Model $record): ?Model => $record)
                            ->placeholder('Enter social media name')
                            ->helperText('Example: Facebook, Twitter, Instagram'),

                        Forms\Components\TextInput::make('icon')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Enter icon class')
                            ->helperText('Example: fab fa-facebook, fab fa-twitter')
                            ->prefix('fab fa-')
                            ->suffixIcon('heroicon-m-information-circle'),

                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->url()
                            ->maxLength(255)
                            ->placeholder('Enter social media URL')
                            ->helperText('Must include https://')
                            ->prefix('https://')
                            ->suffixIcon('heroicon-o-link'),
                    ])
                    ->columns(1)
                    ->collapsible()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->tooltip('Social Media Platform Name')
                    ->formatStateUsing(fn (string $state): string => Str::title($state)),

                Tables\Columns\TextColumn::make('icon')
                    ->searchable()
                    ->sortable()
                    ->tooltip('Icon Class')
                    ->copyable()
                    ->copyMessage('Icon class copied')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->sortable()
                    ->tooltip('Social Media URL')
                    ->copyable()
                    ->copyMessage('URL copied')
                    ->copyMessageDuration(1500)
                    ->openUrlInNewTab(),

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
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->tooltip('Edit this social media'),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Delete this social media'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->emptyStateHeading('No social media links yet')
            ->emptyStateDescription('Start by creating your first social media link.')
            ->emptyStateIcon('heroicon-o-share');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialMedia::route('/'),
            'create' => Pages\CreateSocialMedia::route('/create'),
            'edit' => Pages\EditSocialMedia::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'URL' => $record->url,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Settings';
    }
}
