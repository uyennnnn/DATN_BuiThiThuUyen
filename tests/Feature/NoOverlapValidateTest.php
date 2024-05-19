<?php

namespace Tests\Feature;

use Tests\TestCase;

class NoOverlapValidateTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test_example($baseStart, $baseEnd, $nightStart, $nightEnd, $overtimeStart, $overtimeEnd, $expectedResult): void
    {
        $data = [
            'salary_base' => [
                'time_start' => $baseStart,
                'time_end' => $baseEnd,
            ],
            'salary_night' => [
                'time_start' => $nightStart,
                'time_end' => $nightEnd,
            ],
            'salary_overtime' => [
                'time_start' => $overtimeStart,
                'time_end' => $overtimeEnd,
            ],
        ];
        $response = $this->postJson('/api/validate/no-overlap', $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'result' => $expectedResult,
            ]);
    }

    public static function dataProvider()
    {
        // $baseStart, $baseEnd, $nightStart, $nightEnd, $overtimeStart, $overtimeEnd, $expectedResult
        return [
            ['06:00', '17:00', '17:00', '21:00', '21:00', '00:00', true],
            ['06:00', '17:00', '', '', '17:00', '00:00', true],
            ['06:00', '17:00', '16:00', '21:00', '', '', false],
            ['06:00', '17:00', '', '', '16:00', '00:00', false],
            ['06:00', '17:00', '17:00', '06:00', '', '', true],
            ['06:00', '17:00', '', '', '17:00', '06:00', true],
            ['06:00', '17:00', '16:00', '06:00', '', '', false],
            ['06:00', '17:00', '', '', '16:00', '6:00', false],
            ['', '06:00', '17:00', '21:00', '21:00', '00:00', false],
            ['', '', '17:00', '21:00', '21:00', '00:00', false],
            ['', '', '', '', '21:00', '00:00', false],
            ['', '', '17:00', '21:00', '', '', false],
            ['6:00', '17:00', '21:00', '00:00', '17:00', '21:00', true],
            ['21:00', '00:00', '6:00', '17:00', '17:00', '21:00', true],
            ['21:00', '00:00', '17:00', '21:00', '06:00', '17:00', true],
            ['08:00', '17:00', '17:00', '00:00', '00:00', '7:00', true],
            ['13:00', '17:00', '17:00', '23:00', '23:00', '12:00', true],
            ['13:00', '17:00', '23:00', '12:00', '17:00', '23:00', true],
            ['06:00', '06:00', '', '', '', '', true],
        ];
    }
}
