<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
   use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
   protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Helper methods for roles
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTechnicalAdmin()
    {
        return $this->role === 'technical_admin';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAuthRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'technical_admin']);
    }

    /**
     * Get user's job function role (if staff)
     */
    public function getJobRole()
    {
        if ($this->isStaff()) {
            $staff = $this->getStaffRecord();
            return $staff ? $staff->role : null;
        }
        return null;
    }

    /**
     * Get staff record for staff users
     */
    public function getStaffRecord()
    {
        if ($this->isStaff()) {
            // Find staff by matching email or staff_code pattern
            return Staff::where('email', $this->email)
                       ->orWhere('staff_code', str_replace('@staff.local', '', $this->email))
                       ->first();
        }
        return null;
    }

    /**
     * Find user by email or staff code (for dual login)
     */
    public static function findByIdentifier(string $identifier): ?self
    {
        // First try to find by email
        $user = self::where('email', $identifier)->first();

        // If not found, try to find staff user by staff_code
        if (!$user) {
            $staff = Staff::where('staff_code', $identifier)->first();
            if ($staff) {
                // Find user with matching email or staff-generated email
                $user = self::where('role', 'staff')
                           ->where(function($query) use ($staff) {
                               $query->where('email', $staff->email)
                                     ->orWhere('email', $staff->staff_code . '@staff.local');
                           })
                           ->first();
            }
        }

        return $user;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
