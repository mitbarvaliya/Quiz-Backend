<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Standard;
use Illuminate\Database\Seeder;

class UpdateStandardNamesSeeder extends Seeder
{
    public function run(): void
    {
        $gseb = Board::where('name', 'GSEB')->first();
        if ($gseb) {
            for ($i = 1; $i <= 12; $i++) {
                Standard::updateOrCreate(
                    ['board_id' => $gseb->id, 'name' => (string) $i],
                    ['name' => "Standard $i"]
                );
            }
        }

        $cbse = Board::where('name', 'CBSE')->first();
        if ($cbse) {
            for ($i = 1; $i <= 12; $i++) {
                Standard::updateOrCreate(
                    ['board_id' => $cbse->id, 'name' => (string) $i],
                    ['name' => "Grade $i"]
                );
            }
        }
    }
}
