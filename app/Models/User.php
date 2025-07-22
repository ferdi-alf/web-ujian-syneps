<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'avatar',
        'name',
        'email',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNamaLengkapAttribute(): string
    {
        return match ($this->role) {
            'siswa' => $this->siswaDetail->nama_lengkap ?? 'Siswa',
            'pengajar' => $this->pengajarDetail->nama_lengkap ?? 'Pengajar',
            default => $this->name ?? 'Admin', // Ambil dari tabel user atau fallback
        };
    }


    // Relasi ke siswa_details
    public function siswaDetail(): HasOne
    {
        return $this->hasOne(SiswaDetail::class, 'siswa_id');
    }

    // Relasi ke pengajar_details
    public function pengajarDetail(): HasOne
    {
        return $this->hasOne(PengajarDetail::class, 'pengajar_id');
    }

    // Relasi ke jawaban_siswas
    public function jawabanSiswa(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class, 'siswa_id');
    }

    // Relasi ke hasil_ujians
    public function hasilUjian(): HasMany
    {
        return $this->hasMany(HasilUjian::class, 'siswa_id');
    }

    // Scope untuk role tertentu
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopePengajar($query)
    {
        return $query->where('role', 'pengajar');
    }

    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    public function getAvatarHtml()
    {
        $defaultAvatar = 'images/avatar/default.jpg';
        $avatarPath = $this->avatar ? 'images/avatar/' . $this->avatar : $defaultAvatar;
        
        $nameParts = explode(' ', trim($this->name));
        $initials = '';
        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            if (strlen($initials) >= 2) break;
        }
        if (empty($initials)) {
            $initials = 'U'; 
        }

        if ($this->avatar && file_exists(public_path($avatarPath))) {
            return '<div class="flex items-center">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-300 flex items-center justify-center relative">
                            <img src="' . asset($avatarPath) . '" 
                                alt="' . htmlspecialchars($this->name) . '" 
                                class="w-full h-full object-cover"
                                onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">
                            <div class="absolute inset-0 bg-blue-500 text-white text-xs font-semibold rounded-full hidden items-center justify-center">
                                ' . $initials . '
                            </div>
                        </div>
                    </div>';
        } else {
            return '<div class="flex items-center">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-blue-500 text-white text-xs font-semibold flex items-center justify-center">
                            ' . $initials . '
                        </div>
                    </div>';
        }
    }
    
    public function getAvatarUrl() {
            if ($this->avatar && file_exists(public_path('images/avatar/' . $this->avatar))) {
                return asset('images/avatar/' . $this->avatar);
            }
            
            return asset('images/avatar/default.jpg');
        }

    /**
     * Get user initials
     */
    public function getInitials() {
        $nameParts = explode(' ', trim($this->name));
        $initials = '';
        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            if (strlen($initials) >= 2) break;
        }
        
        return empty($initials) ? 'U' : $initials;
    }
}