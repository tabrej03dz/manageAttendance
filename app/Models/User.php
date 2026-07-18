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

    //  public function activeOfficeId()
    // {
    //     if ($this->hasRole('super_admin')) {
    //         return session('active_office_id');
    //     }

    //     if ($this->hasRole('owner')) {
    //         return \App\Models\Office::where('owner_id', $this->id)->value('id');
    //     }

    //     return $this->office_id;
    // }

    public function activeOfficeId(): ?int
    {
        if (session()->has('active_office_id')) {
            return (int) session('active_office_id');
        }

        return $this->office_id ? (int) $this->office_id : null;
    }

    public function switchableOffices()
    {
        if (!$this->can('switch offices')) {
            return Office::query()->whereRaw('1 = 0');
        }

        if ($this->hasRole('super_admin')) {
            return Office::query()->orderBy('name');
        }

        if ($this->hasRole('owner')) {
            return $this->offices()->orderBy('name');
        }

        /*
            Normal user:
            user.office_id se current office milega.
            current office ke owner_id se owner milega.
            fir us owner ki saari offices milengi.
        */
        $currentOffice = $this->office;

        if (!$currentOffice || !$currentOffice->owner_id) {
            return Office::query()->whereRaw('1 = 0');
        }

        return Office::query()
            ->where('owner_id', $currentOffice->owner_id)
            ->orderBy('name');
    }

    public function canSwitchToOffice(Office $office): bool
    {
        if (!$this->can('switch offices')) {
            return false;
        }

        if ($this->hasRole('super_admin')) {
            return true;
        }

        if ($this->hasRole('owner')) {
            return (int) $office->owner_id === (int) $this->id;
        }

        /*
            Normal user sirf apne owner ki offices me switch kar sakta hai.
        */
        $currentOffice = $this->office;

        if (!$currentOffice || !$currentOffice->owner_id) {
            return false;
        }

        return (int) $office->owner_id === (int) $currentOffice->owner_id;
    }

    public function activities()
    {
        return $this->hasMany(
            \App\Models\UserActivity::class
        );
    }

    public function activityPages()
    {
        return $this->hasMany(
            \App\Models\UserActivityPage::class
        );
    }

}
