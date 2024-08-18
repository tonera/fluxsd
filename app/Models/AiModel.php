<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = ['id'];

    public function images():HasMany
    {
        return $this->hasMany(AiModelImage::class,'model_id', 'model_id')->select(['id','model_id','thumb','width','height'])->orderBy('id');
    }
}
