<?php

namespace App\Modules\Pais\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

/*
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
*/
        return parent::toArray($request);
    }
}
