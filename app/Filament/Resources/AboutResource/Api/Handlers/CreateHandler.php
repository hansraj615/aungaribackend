<?php
namespace App\Filament\Resources\AboutResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\AboutResource;
use App\Filament\Resources\AboutResource\Api\Requests\CreateAboutRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = AboutResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create About
     *
     * @param CreateAboutRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateAboutRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}