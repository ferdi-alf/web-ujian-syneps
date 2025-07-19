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
}