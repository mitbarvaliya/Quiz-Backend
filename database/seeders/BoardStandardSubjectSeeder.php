<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Standard;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class BoardStandardSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $config = [
            'GSEB' => [
                1 => ['અંગ્રેજી', 'કલરવ', 'ગુજરાતી', 'ગણિત ગમ્મત'],
                2 => ['અંગ્રેજી', 'ગણિત ગમ્મત', 'ગુજરાતી'],
                3 => ['પર્યાવરણ', 'ગણિત', 'ગુજરાતી', 'અંગ્રેજી'],
                4 => ['ગુજરાતી', 'હિન્દી', 'અંગ્રેજી', 'ગણિત', 'આસપાસ'],
                5 => ['ગુજરાતી', 'હિન્દી', 'અંગ્રેજી', 'ગણિત', 'આસપાસ'],
                6 => ['ગણિત', 'વિજ્ઞાન', 'સમાજ વિજ્ઞાન', 'ગુજરાતી', 'હિન્દી', 'અંગ્રેજી', 'સંસ્કૃત'],
                7 => ['ગણિત', 'વિજ્ઞાન', 'સમાજ વિજ્ઞાન', 'ગુજરાતી', 'હિન્દી', 'અંગ્રેજી', 'સંસ્કૃત'],
                8 => ['ગણિત', 'વિજ્ઞાન', 'સમાજ વિજ્ઞાન', 'ગુજરાતી', 'હિન્દી', 'અંગ્રેજી', 'સંસ્કૃત'],
                9 => ['ગણિત', 'વિજ્ઞાન', 'સમાજ વિજ્ઞાન', 'ગુજરાતી', 'હિન્દી', 'અંગ્રેજી', 'સંસ્કૃત', 'કમ્પ્યુટર'],
                10 => ['ગણિત', 'વિજ્ઞાન', 'સમાજ વિજ્ઞાન', 'ગુજરાતી', 'હિન્દી', 'અંગ્રેજી', 'સંસ્કૃત', 'કમ્પ્યુટર'],
                11 => ['ગુજરાતી', 'હિન્દી', 'અંગ્રેજી', 'અર્થશાસ્ત્ર', 'ઇતિહાસ', 'ભૂગોળ', 'તત્ત્વજ્ઞાન', 'મનોવિજ્ઞાન', 'સમાજશાસ્ત્ર', 'રાજ્યશાસ્ત્ર', 'કમ્પ્યુટર'],
                12 => ['ગુજરાતી', 'હિન્દી', 'અંગ્રેજી', 'અર્થશાસ્ત્ર', 'ઇતિહાસ', 'ભૂગોળ', 'તત્ત્વજ્ઞાન', 'મનોવિજ્ઞાન', 'સમાજશાસ્ત્ર', 'રાજ્યશાસ્ત્ર', 'કમ્પ્યુટર'],
                'streams' => [
                    'Science' => [
                        11 => ['ભૌતિક વિજ્ઞાન', 'રસાયણ વિજ્ઞાન', 'ગણિત', 'જીવ વિજ્ઞાન', 'અંગ્રેજી'],
                        12 => ['ભૌતિક વિજ્ઞાન', 'રસાયણ વિજ્ઞાન', 'ગણિત / જીવ વિજ્ઞાન', 'અંગ્રેજી'],
                    ],
                    'Commerce' => [
                        11 => ['એકાઉન્ટન્સી', 'આંકડાશાસ્ત્ર', 'અર્થશાસ્ત્ર', 'વ્યવસાય વહીવટ', 'અંગ્રેજી', 'સેક્રેટેરિયલ પ્રેક્ટિસ', 'ગુજરાતી'],
                        12 => ['એકાઉન્ટન્સી', 'આંકડાશાસ્ત્ર', 'અર્થશાસ્ત્ર', 'વ્યવસાય વહીવટ', 'અંગ્રેજી', 'સેક્રેટેરિયલ પ્રેક્ટિસ', 'ગુજરાતી'],
                    ],
                ],
            ],

            'CBSE' => [
                1 => ['English', 'Mathematics'],
                2 => ['English', 'Mathematics'],
                3 => ['Environment', 'Mathematics', 'English'],
                4 => ['Hindi', 'English', 'Mathematics', 'Environment'],
                5 => ['Mathematics', 'Computer Science', 'English', 'Environmental Studies'],
                6 => ['Mathematics', 'Science', 'Social Science', 'Hindi', 'English', 'Sanskrit'],
                7 => ['Mathematics', 'Science', 'Social Science', 'Hindi', 'English', 'Sanskrit'],
                8 => ['Mathematics', 'Science', 'Social Science', 'Hindi', 'English', 'Sanskrit'],
                9 => ['Mathematics', 'Science', 'Social Science', 'Hindi', 'English', 'Sanskrit', 'Computer'],
                10 => ['Mathematics', 'Science', 'Social Science', 'Hindi', 'English', 'Sanskrit', 'Computer'],
                11 => ['Hindi', 'English', 'Economics', 'History', 'Geography', 'Philosophy', 'Psychology', 'Sociology', 'Political Science', 'Computer'],
                12 => ['Hindi', 'English', 'Economics', 'History', 'Geography', 'Philosophy', 'Psychology', 'Sociology', 'Political Science', 'Computer'],
                'streams' => [
                    'Science' => [
                        11 => ['Physics', 'Chemistry', 'Mathematics / Biology', 'English', 'Computer Science'],
                        12 => ['Physics', 'Chemistry', 'Mathematics / Biology', 'English'],
                    ],
                    'Commerce' => [
                        11 => ['Accountancy', 'Business Studies', 'Economics', 'English', 'Mathematics / Informatics Practices'],
                        12 => ['Accountancy', 'Business Studies', 'Economics', 'English', 'Mathematics / Informatics Practices'],
                    ],
                ],
            ],
        ];

        $prefixMap = [
            'GSEB' => 'Standard',
            'CBSE' => 'Grade',
        ];

        foreach ($config as $boardName => $data) {
            $board = Board::firstOrCreate(['name' => $boardName]);
            $prefix = $prefixMap[$boardName] ?? '';

            $streams = $data['streams'] ?? [];
            unset($data['streams']);

            foreach ($data as $std => $subjects) {
                $name = $prefix ? "$prefix $std" : (string) $std;
                $standard = Standard::firstOrCreate([
                    'board_id' => $board->id,
                    'name' => $name,
                ]);

                foreach ($subjects as $subject) {
                    Subject::firstOrCreate([
                        'board_id' => $board->id,
                        'standard_id' => $standard->id,
                        'name' => $subject,
                        'stream' => null,
                    ]);
                }
            }

            foreach ($streams as $streamName => $stdSubjects) {
                foreach ($stdSubjects as $std => $subjects) {
                    $name = $prefix ? "$prefix $std" : (string) $std;
                    $standard = Standard::firstOrCreate([
                        'board_id' => $board->id,
                        'name' => $name,
                    ]);

                    foreach ($subjects as $subject) {
                        $exists = Subject::where([
                            'board_id' => $board->id,
                            'standard_id' => $standard->id,
                            'name' => $subject,
                            'stream' => $streamName,
                        ])->exists();

                        if (!$exists) {
                            Subject::create([
                                'board_id' => $board->id,
                                'standard_id' => $standard->id,
                                'name' => $subject,
                                'stream' => $streamName,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
