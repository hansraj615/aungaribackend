<?php

namespace App\Filament\Resources\AboutResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\AboutResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\AboutResource\Api\Transformers\AboutTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = AboutResource::class;
    public static bool $public = true;

    /**
     * Show About
     *
     * @param Request $request
     * @return AboutTransformer
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

        return new AboutTransformer($query);
    }
}
