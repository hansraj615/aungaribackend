<?php
namespace App\Filament\Resources\HomeResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Home;

/**
 * @property Home $resource
 */
class HomeTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
