<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role_id',
        'ci',
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'estado' => 'boolean',
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function nombreCompleto(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function tieneRol(string $rol): bool
    {
        return $this->role && $this->role->nombre === $rol;
    }

    public function esAdministrativo(): bool
    {
        return in_array($this->role?->nombre, ['propietario', 'secretaria']);
    }

    public function propietarioDetalle(): HasOne
    {
        return $this->hasOne(PropietarioDetalle::class);
    }

    public function secretariaDetalle(): HasOne
    {
        return $this->hasOne(SecretariaDetalle::class);
    }

    public function docenteDetalle(): HasOne
    {
        return $this->hasOne(DocenteDetalle::class);
    }

    public function alumnoDetalle(): HasOne
    {
        return $this->hasOne(AlumnoDetalle::class);
    }

    public function detalleSegunRol()
    {
        return match ($this->role?->nombre) {
            'propietario' => $this->propietarioDetalle,
            'secretaria' => $this->secretariaDetalle,
            'docente' => $this->docenteDetalle,
            'alumno' => $this->alumnoDetalle,
            default => null,
        };
    }
}