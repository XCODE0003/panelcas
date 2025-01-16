<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerResource\Pages;
use App\Filament\Resources\WorkerResource\RelationManagers;
use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Админ панель';
    protected static ?string $navigationLabel = 'Работники';

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('username')->label('Пользователь')->required(),
                Forms\Components\TextInput::make('tg_username')->label('Telegram')->required(),
                Forms\Components\TextInput::make('tg_id')->label('Telegram ID')->required(),
                Forms\Components\Toggle::make('is_ban')->label('Бан'),
                Forms\Components\Toggle::make('is_admin')->label('Админ'),
                Forms\Components\Toggle::make('is_support')->label('Сотрудник'),
                Forms\Components\Toggle::make('notify')->label('Уведомления'),
                Forms\Components\TextInput::make('balance')->label('Баланс')->required()->mask(RawJs::make('$money($input)')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')->label('Пользователь')->searchable(),
                Tables\Columns\TextColumn::make('balance')->label('Баланс')->money('USD'),
                Tables\Columns\TextColumn::make('tg_username')->label('Telegram')->searchable(),
                Tables\Columns\TextColumn::make('tg_id')->label('Telegram ID')->searchable(),
                Tables\Columns\ToggleColumn::make('is_ban')->label('Бан'),
                Tables\Columns\ToggleColumn::make('is_admin')->label('Админ'),
                Tables\Columns\ToggleColumn::make('is_support')->label('Сотрудник'),
                Tables\Columns\ToggleColumn::make('notify')->label('Уведомления'),
            ])
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
            ]);
    }
    public static function canAccess(): bool
    {
        return auth()->user()->is_admin;
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
            'index' => Pages\ListWorkers::route('/'),
            'create' => Pages\CreateWorker::route('/create'),
            'edit' => Pages\EditWorker::route('/{record}/edit'),
        ];
    }
}
