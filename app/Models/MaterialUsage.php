<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialUsage extends Model
{
    protected $fillable = [
        'team_id',
        'technician_id',
        'mitra',
        'no_wo',
        'jenis_psb',
        'barcode_dc',
        'sn_ont',
        'sn_stb',
        'no_reservasi',
        'valins_id',
        'power_rx_ont',
        'odp_lat',
        'odp_long',
        'pelanggan_lat',
        'pelanggan_long',
        'catatan_mitra',
    ];

    /* =====================
     |  RELATIONSHIP
     ===================== */
    public function items()
    {
        return $this->hasMany(MaterialUsageItem::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /* =====================
     |  MUTATOR DECIMAL
     ===================== */
    protected function normalizeDecimal($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = preg_replace('/[^0-9\-,.]/', '', $value);

        if (substr_count($value, ',') > 1 || substr_count($value, '.') > 1) {
            $value = str_replace([',', '.'], '', $value);
            $value = substr_replace($value, '.', -6, 0);
        } else {
            $value = str_replace(',', '.', $value);
        }

        return (float) $value;
    }

    public function setOdpLatAttribute($value)
    {
        $this->attributes['odp_lat'] = $this->normalizeDecimal($value);
    }

    public function setOdpLongAttribute($value)
    {
        $this->attributes['odp_long'] = $this->normalizeDecimal($value);
    }

    public function setPelangganLatAttribute($value)
    {
        $this->attributes['pelanggan_lat'] = $this->normalizeDecimal($value);
    }

    public function setPelangganLongAttribute($value)
    {
        $this->attributes['pelanggan_long'] = $this->normalizeDecimal($value);
    }

    public function setPowerRxOntAttribute($value)
    {
        $this->attributes['power_rx_ont'] = $this->normalizeDecimal($value);
    }
}
