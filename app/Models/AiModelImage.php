<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiModelImage extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = ['id'];

    public function model():BelongsTo
    {
        return $this->belongsTo(AiModel::class,'model_id', 'model_id');
    }
}
