<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class UserImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->skip(1)->each(function ($row) {

            if (!$row[1]) return; // email wajib

            $user = User::where('email', $row[1])->first();

            $data = [
                'name'    => $row[0],
                'role'    => $row[3],
                'team_id' => $row[4] ?: null,
            ];

            if (!empty($row[2])) {
                $data['password'] = Hash::make($row[2]);
            }

            if ($user) {
                // 🔁 UPDATE
                $user->update($data);
            } else {
                // ➕ INSERT
                User::create(array_merge($data, [
                    'email'    => $row[1],
                    'password' => Hash::make($row[2] ?? 'password'),
                ]));
            }
        });
    }
}
