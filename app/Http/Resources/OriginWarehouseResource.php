<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OriginWarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'warehouseId' => $this->id,
            'warehouseName' => $this->name,
            // Ini adalah data paling penting untuk frontend
            'originCityId' => $this->city_id,
            'originDistrictId' => $this->district_id,
        ];
    }
}