<?php
namespace App\Filament\Resources\HomeResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\HomeResource;
use Illuminate\Routing\Router;


class HomeApiService extends ApiService
{
    protected static string | null $resource = HomeResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
