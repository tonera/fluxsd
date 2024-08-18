<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiModelImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'model_id' => $this->model_id,
            'thumb' => strstr(strtolower($this->thumb), 'http') === false ? '/'.$this->thumb : $this->thumb,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
