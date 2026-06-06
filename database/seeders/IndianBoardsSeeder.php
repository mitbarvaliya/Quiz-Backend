<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;

class IndianBoardsSeeder extends Seeder
{
    public function run(): void
    {
        $boards = [
            // National Boards
            ['name' => 'CBSE', 'description' => 'Central Board of Secondary Education'],
            ['name' => 'ICSE', 'description' => 'Indian Certificate of Secondary Education (CISCE)'],
            ['name' => 'NIOS', 'description' => 'National Institute of Open Schooling'],

            // State Boards
            ['name' => 'GSEB', 'description' => 'Gujarat Secondary & Higher Secondary Education Board'],
            ['name' => 'MSBSHSE', 'description' => 'Maharashtra State Board of Secondary & Higher Secondary Education'],
            ['name' => 'UPMSP', 'description' => 'Uttar Pradesh Madhyamik Shiksha Parishad'],
            ['name' => 'RBSE', 'description' => 'Board of Secondary Education, Rajasthan'],
            ['name' => 'MPBSE', 'description' => 'Madhya Pradesh Board of Secondary Education'],
            ['name' => 'BSEB', 'description' => 'Bihar School Examination Board'],
            ['name' => 'KSEEB', 'description' => 'Karnataka Secondary Education Examination Board'],
            ['name' => 'TNBSE', 'description' => 'Tamil Nadu Board of Secondary Education'],
            ['name' => 'BSEAP', 'description' => 'Board of Secondary Education, Andhra Pradesh'],
            ['name' => 'TSBIE', 'description' => 'Telangana State Board of Intermediate Education'],
            ['name' => 'WBBSE', 'description' => 'West Bengal Board of Secondary Education'],
            ['name' => 'AHSEC', 'description' => 'Assam Higher Secondary Education Council'],
            ['name' => 'PSEB', 'description' => 'Punjab School Education Board'],
            ['name' => 'HBSE', 'description' => 'Haryana Board of School Education'],
            ['name' => 'HPBOSE', 'description' => 'Himachal Pradesh Board of School Education'],
            ['name' => 'JKBOSE', 'description' => 'Jammu and Kashmir Board of School Education'],
            ['name' => 'JAC', 'description' => 'Jharkhand Academic Council'],
            ['name' => 'CGBSE', 'description' => 'Chhattisgarh Board of Secondary Education'],
            ['name' => 'BSE', 'description' => 'Board of Secondary Education, Odisha'],
            ['name' => 'KBPE', 'description' => 'Kerala Board of Public Examinations'],
            ['name' => 'GBSHSE', 'description' => 'Goa Board of Secondary & Higher Secondary Education'],
            ['name' => 'UBSE', 'description' => 'Uttarakhand Board of School Education'],
            ['name' => 'MBOSE', 'description' => 'Meghalaya Board of School Education'],
            ['name' => 'NBSE', 'description' => 'Nagaland Board of School Education'],
            ['name' => 'MBSE', 'description' => 'Mizoram Board of School Education'],
            ['name' => 'COHSEM', 'description' => 'Council of Higher Secondary Education, Manipur'],
            ['name' => 'TBSE', 'description' => 'Tripura Board of Secondary Education'],
            ['name' => 'SBSES', 'description' => 'Sikkim Board of Secondary Education'],
        ];

        foreach ($boards as $board) {
            Board::firstOrCreate(
                ['name' => $board['name']],
                ['name' => $board['name']]
            );
        }

        $this->command->info('Indian educational boards seeded successfully!');
    }
}
