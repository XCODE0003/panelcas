<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawResource\Pages;
use App\Filament\Resources\WithdrawResource\RelationManagers;
use Filament\Notifications\Notification;
use App\Models\WithdrawWorker;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\RawJs;

class WithdrawResource extends Resource
{
    protected static ?string $model = WithdrawWorker::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Выводы';
    protected static ?string $pluralNavigationLabel = 'Выводы';
    protected static ?string $pluralLabel = 'Выводы';
    protected static ?string $title = 'Выводы';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('address')->label('Адрес')->disabled(),
                TextInput::make('amount')->label('Сумма')->disabled()->mask(RawJs::make('$money($input)')),
                TextInput::make('description')->label('Дополнительная информация')->disabled(),
                Select::make('status')->label('Статус')->native(false)->options([
                    'pending' => 'Ожидается',
                    'completed' => 'Выполнено',
                    'rejected' => 'Отклонено',
                ])->required(),
            ]);
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        
        return auth()->user()->is_admin;
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.username')->visible(fn ($record) => auth()->user()->role == 'admin')->label('Пользователь')->searchable(),
                Tables\Columns\TextColumn::make('address')->label('Адрес')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('status')->badge()
                ->color(fn ($state) => match ($state) {
                    'pending' => 'warning',
                    'completed' => 'success',
                    'rejected' => 'danger',
                })->label('Статус'),
                Tables\Columns\TextColumn::make('currency')->label('Валюта'),
                Tables\Columns\TextColumn::make('amount')->money('USD')->label('Сумма'),
                Tables\Columns\TextColumn::make('description')->label('Дополнительная информация'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])->headerActions([
                Tables\Actions\Action::make('createWithdraw')->label('Подать заявку на вывод')
                ->icon('heroicon-o-banknotes')
                ->form([
                    TextInput::make('amount')->label('Сумма. Ваш баланс: '.auth()->user()->balance.' USD')->required()->stripCharacters(',')->mask(RawJs::make('$money($input)'))->live(onBlur: true),
                    TextInput::make('address')->label('Адрес')->required(),
                    Select::make('currency')->label('Валюта')->native(false)->options([
                        'USDT TRC20' => 'USDT TRC20',
                        'ETH' => 'ETH',
                        'BTC' => 'BTC',
                    ])->required(),
                    TextInput::make('description')->label('Дополнительная информация'),
                ])
                ->action(function (array $data) {
                    if($data['amount'] > auth()->user()->balance) {
                        Notification::make()->title('Недостаточно средств')->danger()->send();
                        return;
                    }
                    if($data['amount'] < 10) {
                        Notification::make()->title('Минимальная сумма вывода 10 USD')->danger()->send();
                        return;
                    }
                    WithdrawWorker::create([
                        'user_id' => auth()->user()->id,
                        'address' => $data['address'],
                        'amount' => $data['amount'],
                        'currency' => $data['currency'],
                        'description' => $data['description'],
                    ]);
                    auth()->user()->balance -= $data['amount'];
                    auth()->user()->save();
                    Notification::make()->title('Заявка на вывод успешно создана')->success()->send();
                })->modalWidth('md'),
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
            'index' => Pages\ListWithdraws::route('/'),
            'create' => Pages\CreateWithdraw::route('/create'),
            'edit' => Pages\EditWithdraw::route('/{record}/edit'),
        ];
    }
}
