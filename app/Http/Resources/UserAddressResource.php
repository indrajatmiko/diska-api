<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'recipientName' => $this->recipient_name,
            'phoneNumber' => $this->phone_number,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'subDistrict' => $this->sub_district,
            'postalCode' => $this->postal_code,
            'addressDetail' => $this->address_detail,
            'isPrimary' => $this->is_primary,
        ];
    }
}