<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiModelResource extends JsonResource
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
            'hash' => $this->hash,
            'name' => $this->name,
            'sd_name' => $this->sd_name,
            'type' => $this->type,
            'nsfw' => $this->nsfw,
            'base_model' => $this->base_model,
            'thumb' => strstr(strtolower($this->thumb), 'http') === false ? '/'.$this->thumb : $this->thumb,
            'is_service' => $this->is_service,
            'favored' => $this->favored,
            'engine' => $this->engine,
            'is_download' => $this->is_download,
            'prompt' => $this->prompt,
            'base_size' =>  $this->base_size??1024,
            'tags' => $this->tags,
            'images' => new AiModelImageCollection($this->images),
        ];
    }
}
