<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price','completed_tasks',  'start_date', 'end_date', 'user_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

   

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    

    
}
