<?php

namespace App\Filament\Resources\HomeResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\HomeResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\HomeResource\Api\Transformers\HomeTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = HomeResource::class;
    public static bool $public = true;

    public static function getMiddleware(): array
    {
        return [];
    }
    /**
     * Show Home
     *
     * @param Request $request
     * @return HomeTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new HomeTransformer($query);
    }
}
