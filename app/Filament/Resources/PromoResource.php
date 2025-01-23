<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoResource\Pages;
use App\Filament\Resources\PromoResource\RelationManagers;
use App\Models\Promo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;


class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Промокоды';
    protected static ?string $pluralNavigationLabel = 'Промокоды';
    protected static ?string $pluralLabel = 'Промокоды';
    protected static ?string $title = 'Промокоды';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('promo_code')->label('Промокод'),
                TextInput::make('amount')->label('Сумма'),
                TextInput::make('win_chance')->label('Шанс выигрыша')->numeric(),
                TextInput::make('min_deposit_activation')->label('Минимальный депозит для активации аккаунта')->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('promo_code')->label('Промокод'),
                Tables\Columns\TextColumn::make('amount')->label('Сумма'),
                Tables\Columns\TextColumn::make('created_at')->label('Дата создания'),
                Tables\Columns\TextColumn::make('user.username')->label('Воркер')->visible(auth()->user()->is_admin),
                
            ])->modifyQueryUsing(fn($query) => $query->where('user_id', auth()->user()->id))->searchable(false)
            ->filters([
                //
            ])
            ->actions([
                
                Tables\Actions\EditAction::make(),
            ])->headerActions([
                Tables\Actions\Action::make('create')->label('Создать промокод')
                ->form([
                    TextInput::make('promo_code')
                        ->required()
                        ->label('Промокод')
                        ->maxLength(255),
                    TextInput::make('amount')
                        ->required()
                        ->label('Сумма')
                        ->maxLength(255),
                    TextInput::make('win_chance')
                        ->required()
                        ->label('Шанс выигрыша (%)')
                        ->numeric()
                        ->minValue(50)
                        ->maxValue(100),
                    TextInput::make('min_deposit_activation')
                        ->required()
                        ->label('Минимальный депозит для активации аккаунта. Минимальный депозит 50$')
                        ->numeric(),
                ])
                ->modalWidth('sm')
                ->action(fn (array $data) => self::create($data)),
            ]);
    }
    private static function create(array $data){
        if($data['win_chance'] > 100 || $data['win_chance'] < 50){
            Notification::make()
            ->title('Шанс выигрыша не может быть больше 100% или меньше 50%')
            ->danger()
            ->send();
            return;
        }
        $data['user_id'] = auth()->user()->id;
        Promo::create($data);
        
        Notification::make()
            ->title('Промокод создан')
            ->success()
            ->send();
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
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
        ];
    }
}
