<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, softDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function members(){
        return $this->hasMany(User::class, 'team_leader_id');
    }

    public function teamLeader(){
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function leaves(){
        return $this->hasMany(Leave::class, 'user_id');
    }

    public function visits(){
        return $this->hasMany(Visit::class, 'user_id');
    }

    public function offices(){
        return $this->hasMany(Office::class, 'owner_id');
    }

    public function plans(){
        return $this->hasMany(Plan::class, 'user_id');
    }

    public function userNotes(){
        return $this->hasMany(NoteUser::class, 'user_id');
    }

    public function latestAttendance()
    {
        return $this->hasOne(AttendanceRecord::class, 'user_id');
    }

    public function userSalary(){
        return $this->hasOne(UserSalary::class, 'user_id');
    }

    public function getAllTeamMembers()
    {
        $members = collect();

        foreach ($this->members()->get() as $member) {
            $members->push($member);
            $members = $members->merge($member->getAllTeamMembers());
        }

        return $members;
    }

    public function department(){
        return $this->belongsTo(Department::class, 'department_id');
    }

}
