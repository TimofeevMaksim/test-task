<?php

namespace App\Orchid\Resources;

use Orchid\Crud\ResourceRequest;
use App\Models\Post;
use App\Models\User;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\TD;
use Illuminate\Database\Eloquent\Model;

class PostResource extends Resource
{
    public static $model = Post::class;

    public function fields(): array
    {
        return [
            Input::make('title')->title('Заголовок')->required(),
            TextArea::make('text')->title('Текст')->required(),
            Select::make('user_id')
                ->fromModel(User::class, 'name')
                ->title('Автор')
                ->required(),
        ];
    }

    public function columns(): array
    {
        return [
            TD::make('id')->sort(),
            TD::make('title')->sort(),
            TD::make('user.name', 'Автор')->sort(),
            TD::make('created_at', 'Дата создания')->sort(),
        ];
    }

    public function rules(Model $model): array
    {
        return [
            'title' => 'required|string|max:255',
            'text'  => 'required|string',
        ];
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
