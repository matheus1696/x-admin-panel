<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaskTypeActivity extends Model
{
    /** @use HasFactory<\Database\Factories\Task\TaskTypeActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'task_type_id',
        'title',
        'filter',
        'order',
        'deadline_days',
        'required',
    ];

    protected $casts = [
        'required' => 'boolean',
        'deadline_days' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Task type (process template)
     */
    public function taskType()
    {
        return $this->belongsTo(TaskType::class);
    }

    //Criação do Filter
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['filter'] = Str::ascii(strtolower($value));
    }
}
