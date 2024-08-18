<?php

namespace App\Http\Resources;

use App\Services\Common;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskImageResource extends JsonResource
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
            'show_url' => Common::getCdnUrl($this->show_url, $this->task->storage??'local'),
            'thumb' => Common::getCdnUrl($this->thumb, $this->task->storage??'local'),
            'is_merge' => $this->is_merge,
            'buttonGroups' => $this->buttonGroups??[],
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'task' => new TaskResource($this->task),
        ];
    }
}
