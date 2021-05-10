<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $attributesMapping = [];
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $hidden = ['deleted_at'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        foreach ($this->dates as $dateAttribute)
        {
            $this->casts[$dateAttribute] = 'datetime:' . $this->dateFormat;
        }
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute($this->attributesMapping[$key] ?? $key, $value);
    }

    protected function formatDateTime($value)
    {
        return (new Carbon($value))->format($this->dateFormat);
    }
}
