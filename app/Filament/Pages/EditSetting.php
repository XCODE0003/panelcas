<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use App\Models\NotifySetting;
use App\Models\Setting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Action;
use Filament\Forms\Get; 
use Filament\Notifications\Notification;

class EditSetting extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.pages.edit-setting';
    protected static ?string $navigationLabel = 'Настройки';
    protected static ?string $pluralNavigationLabel = 'Настройки';
    protected static ?string $pluralLabel = 'Настройки';
    protected static ?string $title = 'Настройки';
    
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'notify' => auth()->user()->notify,
            'tg_id' => auth()->user()->tg_id,
            'notify_new_visit' => auth()->user()->notify_settings->notify_new_visit ?? false,
            'notify_activate_promo' => auth()->user()->notify_settings->notify_activate_promo ?? false,
            'notify_new_payment' => auth()->user()->notify_settings->notify_new_payment ?? false,
            'notify_new_order' => auth()->user()->notify_settings->notify_new_order ?? false,

            'min_withdraw_worker' => Setting::first()->min_withdraw_worker ?? 100,
            'percent_profit_worker' => Setting::first()->percent_profit_worker ?? 75,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основные настройки')
                    ->description('Основные настройки аккаунта')
                    ->schema([
                        Toggle::make('notify')
                        ->label('Уведомления')
                        ->live(),
                        Grid::make(2)
                            ->schema([
                            
                                TextInput::make('tg_id')
                                    ->required()
                                    ->label('ID телеграмма')
                                    ->placeholder('ID телеграмма')
                                    ->visible(fn (Get $get): bool => $get('notify')),
                                    TextInput::make('bot_token')
                                    ->label('Токен телеграм бота')
                                    ->placeholder('Токен телеграм бота')
                                    ->visible(fn (Get $get): bool => $get('notify')),
                                    Grid::make(2)
                                    ->visible(fn (Get $get): bool => $get('notify'))
                                    ->schema([
                                    
                                        Toggle::make('notify_new_visit')
                                            ->label('Уведомление о новом посещении')
                                            ->default(false),
                                            Toggle::make('notify_activate_promo')
                                            ->label('Уведомление о активации промокода')
                                            ->default(false),
                                            Toggle::make('notify_new_order')
                                            ->label('Уведомление о переходе на оплату')
                                            ->default(false),
                                            Toggle::make('notify_new_payment')
                                            ->label('Уведомление о новом платеже')
                                            ->default(false),

                                    ]),
                            ]),
                    ]),

                    Section::make('Настройки тимы')
                    ->schema([
                        TextInput::make('min_withdraw_worker')
                            ->label('Минимальная сумма вывода')
                            ->default(Setting::first()->min_withdraw_worker ?? 100),
                            TextInput::make('percent_profit_worker')
                            ->label('Процент прибыли')
                            ->default(Setting::first()->percent_profit_worker ?? 75),
                    ])->visible(fn (Get $get): bool => auth()->user()->is_admin),
                    
                // Section::make('SEO настройки')
                //     ->description('Настройки для поисковых систем')
                //     ->collapsed()
                //     ->schema([
                //         TextInput::make('meta_title')
                //             ->label('Meta Title')
                //             ->maxLength(60),
                            
                //         TextInput::make('meta_description')
                //             ->label('Meta Description')
                //             ->maxLength(160),
                //     ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Сохранить')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $user = auth()->user();
        $data = $this->form->getState();
        if(isset($data['tg_id']) && $user->tg_id != $data['tg_id']){
            $user->tg_id = $data['tg_id'];
        }
        $user->notify = $data['notify'];
        $user->save();
        unset($data['tg_id']);
        unset($data['notify']);
        $data['user_id'] = $user->id;
        $notify = NotifySetting::where('user_id', $user->id)->first();
        if($notify){
            $notify->update($data);
        }else{
            NotifySetting::create($data);
        }
        if(auth()->user()->is_admin){
            Setting::first()->update([
                'min_withdraw_worker' => $data['min_withdraw_worker'],
                'percent_profit_worker' => $data['percent_profit_worker'],
            ]);
        }
        
        Notification::make()
            ->title('Настройки сохранены')
            ->success()
            ->send();
    }
}
