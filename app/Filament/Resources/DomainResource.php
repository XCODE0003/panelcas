<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DomainResource\Pages;
use App\Filament\Resources\DomainResource\RelationManagers;
use App\Services\Domain\CreateDomain;
use App\Models\Domain;
use Filament\Forms;
use Filament\Support\Enums\FontFamily;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel = 'Домены';
    protected static ?string $pluralNavigationLabel = 'Домены';
    protected static ?string $pluralLabel = 'Домены';
    protected static ?string $title = 'Домены';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
              TextInput::make('domain')
                    ->label('Домен')->disabled()
                    ->required(),
                TextInput::make('title')
                    ->label('Название казино')
                    ->required(),
                // TextInput::make('win_chance')
                //     ->label('Шанс выигрыша по стандарту')
                //     ->numeric()
                    
                //     ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')->searchable()->label('Домен'),
                TextColumn::make('ns_records')->label('NS записи')->html()->getStateUsing(fn($record) => implode("<br>", $record->ns_records))->fontFamily(FontFamily::Mono)->size('sm')->copyableState(fn($record) => implode(", ", $record->ns_records))->badge()->color('gray')->copyable(),
                TextColumn::make('status')->label('Статус')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'active' => 'success',
                    }),
            ])
            ->modifyQueryUsing(fn($query) => $query->where('user_id', auth()->user()->id))->searchable(false)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->headerActions([
                Tables\Actions\Action::make('create')->label('Привязать домен')
                    ->form([
                        TextInput::make('domain')
                            ->required()
                            ->label('Домен')
                            ->maxLength(255),
                            TextInput::make('title')
                            ->required()
                            ->label('Название казино')
                            ->maxLength(255),
                            // TextInput::make('win_chance')
                            // ->required()
                            // ->label('Шанс выигрыша по стандарту')
                            // ->numeric()
                    ])
                    ->modalWidth('sm')
                    ->action(fn (array $data) => (new CreateDomain())->create($data)),
            ]);
    }
    public static function canCreate(): bool
    {
        return false;
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
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
}
