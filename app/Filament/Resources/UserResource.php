<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Split;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Мамонты';
    protected static ?string $pluralNavigationLabel = 'Мамонты';
    protected static ?string $pluralLabel = 'Мамонты';
    protected static ?string $title = 'Мамонты';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    TextInput::make('name')->label('Имя'),
                    TextInput::make('email')->label('Email')->disabled(),
                    TextInput::make('new_password')->label('Новый пароль'),
                    TextInput::make('win_chance')->label('Шанс выигрыша'),

                    TextInput::make('total_balance')
                        ->label('Баланс')
                        ->afterStateHydrated(function ($component, $state, $record) {
                            $component->state($record?->wallet?->total_balance ?? 0);
                        }),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Имя'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('win_chance')->label('Шанс выигрыша'),
                Tables\Columns\TextColumn::make('inviterUser.username')->label('Кто пригласил')->visible(auth()->user()->is_admin),
                Tables\Columns\TextColumn::make('wallet.total_balance')->label('Баланс'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if(!auth()->user()->is_admin){
                    $query->where('inviter', auth()->id());
                }
            })
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                ->mutateRecordDataUsing(function (array $data): array {
                    $user = User::find($data['id']);
                    if($data['new_password']){
                        $user->update(['password' => Hash::make($data['new_password'])]);
                    }
                    if($data['total_balance']){
                        $user->wallet->update(['balance' => $data['total_balance'],'balance_bonus' => 0,'balance_withdrawal' => 0]);
                    }


                    $user->update($data);
                   
                    return $data;
                })
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
