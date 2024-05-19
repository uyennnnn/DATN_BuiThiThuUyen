<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        dump('Seeding user');
        User::where('id', '>', 0)->forceDelete();
        User::factory([
            'id' => 1,
            'email' => 'sontt@yopaz.vn',
            'password' => 'sontt@yopaz.vn',
            'role' => 1,
        ])->create();

        Attendance::truncate();
        for ($i = 1; $i <= 20; $i++) {
            $user = User::factory(
                [
                    'role' => 2,
                ]
            )->create();
            $user_id = $user->id;
            for ($day = 1; $day <= 29; $day++) {
                if ($day == 8) {
                    continue;
                }
                Attendance::insert([
                    [
                        'user_id' => $user_id,
                        'date_time' => "2024-03-$day 07:00:00",
                        'action_type' => 'checkIn',
                    ],
                    [
                        'user_id' => $user_id,
                        'date_time' => "2024-03-$day 11:00:00",
                        'action_type' => 'startBreak',
                    ],
                    [
                        'user_id' => $user_id,
                        'date_time' => "2024-03-$day 14:00:00",
                        'action_type' => 'endBreak',
                    ],
                    [
                        'user_id' => $user_id,
                        'date_time' => "2024-03-$day 17:00:00",
                        'action_type' => 'startBreak',
                    ],
                    [
                        'user_id' => $user_id,
                        'date_time' => "2024-03-$day 19:00:00",
                        'action_type' => 'endBreak',
                    ],
                    [
                        'user_id' => $user_id,
                        'date_time' => "2024-03-$day 23:00:00",
                        'action_type' => 'checkOut',
                    ],
                ]);
            }
        }

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        return;

        for ($i = 1; $i < 21; $i++) {
            User::create([
                'name' => 'loc_'.$i,
                'full_name' => 'vu_loc_'.$i,
                'email' => 'locvv'.$i.'@yopaz.vn',
                'password' => Hash::make('11111111'),
                'role' => 2,
            ]);
        }

        for ($i = 1; $i < 31; $i++) {
            $datas = [
                [
                    'user_id' => 11,
                    'date_time' => Carbon::create(null, 3, $i, 8, 0, 0),
                    'action_type' => 'checkIn',
                ],
                [
                    'user_id' => 11,
                    'date_time' => Carbon::create(null, 3, $i, 11, 0, 0),
                    'action_type' => 'startBreak',
                ],
                [
                    'user_id' => 11,
                    'date_time' => Carbon::create(null, 3, $i, 12, 0, 0),
                    'action_type' => 'endBreak',
                ],
                [
                    'user_id' => 11,
                    'date_time' => Carbon::create(null, 3, $i, 16, 0, 0),
                    'action_type' => 'checkOut',
                ],
            ];
            for ($j = 11; $j < 31; $j++) {
                foreach ($datas as $data) {
                    $data['user_id'] = $j;
                    Attendance::create($data);
                }
            }
        }
    }
}
