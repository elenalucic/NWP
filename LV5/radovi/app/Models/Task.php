<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'title_en',
        'description',
        'study_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function students()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }
    
    public function acceptedStudent()
{
    return $this->belongsTo(User::class, 'accepted_student_id');
}
}