<?php

namespace App\Orchid\Resources;

use App\Models\User;
use Orchid\Crud\Resource;
use Orchid\Crud\ResourceRequest;
use Illuminate\Database\Eloquent\Model;   // <-- ОБЯЗАТЕЛЬНО
use Orchid\Screen\Fields\Input;
use Orchid\Screen\TD;

class UserResource extends Resource
{
    public static $model = User::class;

    public function fields(): array
    {
        return [
            Input::make('name')->title('Имя')->required(),
            Input::make('email')->title('Email')->required(),
            Input::make('password')->title('Пароль')->type('password'),
            Input::make('is_admin')->title('Администратор')->type('checkbox')->value(0),
        ];
    }

    public function columns(): array
    {
        return [
            TD::make('id')->sort(),
            TD::make('name')->sort(),
            TD::make('email')->sort(),
            TD::make('is_admin', 'Администратор')->render(fn($user) => $user->is_admin ? 'Да' : 'Нет'),
            TD::make('created_at', 'Дата регистрации')->sort(),
        ];
    }

    /**
     * Правила валидации.
     */
    public function rules(Model $model): array
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'is_admin' => 'boolean',
        ];

        if (!$model->exists) {
            // Создание нового пользователя
            $rules['email']    = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:6';
        } else {
            // Обновление существующего
            $rules['email']    = 'required|email|unique:users,email,' . $model->id;
            $rules['password'] = 'nullable|string|min:6';
        }

        return $rules;
    }

    /**
     * Сохранение с хэшированием пароля.
     */
    public function persist($model, array $attributes): void
    {
        if (!empty($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        } else {
            unset($attributes['password']);
        }
        parent::persist($model, $attributes);
    }

    public function onSave(ResourceRequest $request, Model $model)
    {
        $model->forceFill($request->all())->save();
    }

    public function onDelete(Model $model)
    {
        $model->delete();
    }

    public function legend(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [];
    }
}
