<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'task_id' => $this->task_id,
            'prompt' => $this->prompt,
            'negative_prompt' => $this->negative_prompt,
            'prompt_en' => $this->prompt_en,
            'negative_prompt_en' => $this->negative_prompt_en,
            'steps' => $this->steps,
            'cfg_scale' => $this->cfg_scale,
            'seed' => $this->seed,
            'denoising_strength' => $this->denoising_strength,
            'width' => $this->width,
            'height' => $this->height,
            'act' => $this->act,
            'sampler_name' => $this->sampler_name,
            'style_id' => $this->style_id,
            'engine' => $this->engine,
            'user_id' => $this->user_id,
            'cover_url' => $this->cover_url,
            'image_num' => $this->image_num,
            'init_img' => $this->init_img,
            'model_name' => $this->model_name,
            'lora_name' => $this->lora_name,
            'model_hash_id' => $this->model_hash_id,
            'lora_hash_id' => $this->lora_hash_id,
            'created_at' => $this->created_at,
            // 'images' => TaskImageResource::collection($this->images),
        ];
    }
}
