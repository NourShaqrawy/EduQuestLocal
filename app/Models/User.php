<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

 
    protected $fillable = [
        'user_name',
        'email',
        'address',
        'role',
        'password',
        'language', 
        'dark_mode' 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPublisher(): bool
    {
        return $this->role === 'publisher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
   
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

   
    public function publishedCourses()
    {
        return $this->hasMany(Course::class, 'publisher_id');
    }

   
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments', 'student_id', 'course_id')
            ->withTimestamps();
    }

    
    public function exerciseSubmissions()
    {
        return $this->hasMany(Exercise_Submissions::class, 'student_id');
    }

    public function videoReactions()
    {
        return $this->hasMany(Video_Reactions::class, 'user_id');
    }
    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollments::class, 'student_id');
    }
    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'student_id');
    }
}
