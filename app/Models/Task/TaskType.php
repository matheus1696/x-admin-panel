<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaskType extends Model
{
    /** @use HasFactory<\Database\Factories\Task\TaskTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'filter',
        'description',
        'status',
    ];

    public function activities(){
        return $this->hasMany(TaskTypeActivity::class)->orderBy('order');
    }

    public function toggleStatus(): self
    {
        $this->update(['status' => !$this->status]);
        return $this;
    }

    //Criação do Filter
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['filter'] = Str::ascii(strtolower($value));
    }
}
