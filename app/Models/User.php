<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime', 'password' => 'hashed',
        'check_in_time' => 'datetime', 'check_out_time' => 'datetime', 'dob' => 'date',
    ];

    public function designationDetails() { return $this->belongsTo(Designation::class, 'designation_id'); }
    public function office() { return $this->belongsTo(Office::class, 'office_id'); }
    public function members() { return $this->hasMany(self::class, 'team_leader_id'); }
    public function teamLeader() { return $this->belongsTo(self::class, 'team_leader_id'); }
    public function leaves() { return $this->hasMany(Leave::class, 'user_id'); }
    public function visits() { return $this->hasMany(Visit::class, 'user_id'); }
    public function offices() { return $this->hasMany(Office::class, 'owner_id'); }
    public function plans() { return $this->hasMany(Plan::class, 'user_id'); }
    public function userNotes() { return $this->hasMany(NoteUser::class, 'user_id'); }
    public function latestAttendance() { return $this->hasOne(AttendanceRecord::class, 'user_id'); }
    public function userSalary() { return $this->hasOne(UserSalary::class, 'user_id'); }
    public function department() { return $this->belongsTo(Department::class, 'department_id'); }
    public function activities() { return $this->hasMany(UserActivity::class); }
    public function activityPages() { return $this->hasMany(UserActivityPage::class); }
    public function leaveAuthority() { return $this->belongsTo(self::class, 'leave_authority_id'); }
    public function leaveAuthorityEmployees() { return $this->hasMany(self::class, 'leave_authority_id'); }
    public function educationalQualifications() { return $this->hasMany(EmployeeEducationalQualification::class, 'user_id')->orderBy('passing_year'); }
    public function familyMembers() { return $this->hasMany(EmployeeFamilyMember::class, 'user_id'); }

    public function getAllTeamMembers()
    {
        $members = collect();
        $seen = [(int) $this->id => true];
        $frontier = [(int) $this->id];
        while ($frontier) {
            $next = [];
            foreach (self::query()->whereIn('team_leader_id', $frontier)->get() as $member) {
                $id = (int) $member->id;
                if (isset($seen[$id])) { continue; }
                $seen[$id] = true;
                $members->push($member);
                $next[] = $id;
            }
            $frontier = $next;
        }
        return $members;
    }

    public function activeOfficeId(): ?int
    {
        if (session()->has('active_office_id')) { return (int) session('active_office_id'); }
        return $this->office_id ? (int) $this->office_id : null;
    }

    public function switchableOffices()
    {
        if (!$this->can('switch offices')) { return Office::query()->whereRaw('1 = 0'); }
        if ($this->hasRole('super_admin')) { return Office::query()->orderBy('name'); }
        if ($this->hasRole('owner')) { return $this->offices()->orderBy('name'); }
        $currentOffice = $this->office;
        if (!$currentOffice || !$currentOffice->owner_id) { return Office::query()->whereRaw('1 = 0'); }
        return Office::query()->where('owner_id', $currentOffice->owner_id)->orderBy('name');
    }

    public function canSwitchToOffice(Office $office): bool
    {
        if (!$this->can('switch offices')) { return false; }
        if ($this->hasRole('super_admin')) { return true; }
        if ($this->hasRole('owner')) { return (int) $office->owner_id === (int) $this->id; }
        $currentOffice = $this->office;
        if (!$currentOffice || !$currentOffice->owner_id) { return false; }
        return (int) $office->owner_id === (int) $currentOffice->owner_id;
    }
}
