<?php
namespace App\Filament\Resources\HomeResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\HomeResource;
use App\Filament\Resources\HomeResource\Api\Requests\CreateHomeRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = HomeResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Home
     *
     * @param CreateHomeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateHomeRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}