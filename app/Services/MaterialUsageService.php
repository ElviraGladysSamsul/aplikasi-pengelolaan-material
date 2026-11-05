<?php

namespace App\Services;

use App\Models\MaterialUsage;
use App\Models\Team;
use App\Models\TeamStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialUsageService
{
    public function store(array $data): void
    {
        DB::transaction(function () use ($data) {

            $user = Auth::user();

            // tentukan team
            if ($user->role === 'superadmin') {
                $teamId = $data['team_id'] ?? null;
            } else {
                $teamId = $user->team_id;
            }

            if (!$teamId) {
                throw new \Exception('Team tidak valid');
            }

            $team = Team::findOrFail($teamId);

            // simpan material usage
            $usage = MaterialUsage::create([
                'team_id'       => $team->id,
                'technician_id' => $user->id,
                'mitra'         => $team->mitra,

                'no_wo'     => $data['no_wo'],
                'jenis_psb' => $data['jenis_psb'],
                'sn_ont'    => $data['sn_ont'],

                'barcode_dc'    => $data['barcode_dc'] ?? null,
                'sn_stb'        => $data['sn_stb'] ?? null,
                'no_reservasi'  => $data['no_reservasi'] ?? null,
                'valins_id'     => $data['valins_id'] ?? null,
                'power_rx_ont'  => $data['power_rx_ont'] ?? null,
                'catatan_mitra' => $data['catatan_mitra'] ?? null,

                'odp_lat'        => $this->cleanDecimal($data['odp_lat']),
                'odp_long'       => $this->cleanDecimal($data['odp_long']),
                'pelanggan_lat'  => $this->cleanDecimal($data['pelanggan_lat']),
                'pelanggan_long' => $this->cleanDecimal($data['pelanggan_long']),
            ]);

            // proses material
            foreach ($data['materials'] as $item) {

                $stock = TeamStock::where('team_id', $team->id)
                    ->where('material_id', $item['material_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($stock->quantity < $item['quantity']) {
                    throw new \Exception('Stok material tidak cukup');
                }

                $stock->decrement('quantity', $item['quantity']);

                $usage->items()->create([
                    'material_id' => $item['material_id'],
                    'qty'         => $item['quantity'],
                ]);
            }
        });
    }

    // =============================
    // 🔥 DELETE MATERIAL USAGE
    // =============================
    public function delete(MaterialUsage $usage): void
    {
        DB::transaction(function () use ($usage) {

            $usage->load('items');

            foreach ($usage->items as $item) {

                $stock = TeamStock::where('team_id', $usage->team_id)
                    ->where('material_id', $item->material_id)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $stock->increment('quantity', $item->qty);
                }
            }

            $usage->items()->delete();
            $usage->delete();
        });
    }

    private function cleanDecimal($value)
    {
        if (!$value) return null;

        return str_replace(',', '.', str_replace('.', '', $value));
    }
}
