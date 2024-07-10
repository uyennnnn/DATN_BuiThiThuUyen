<?php
use PHPUnit\Framework\TestCase;
use HolidayJp\HolidayJp;

class HolidaysTest extends TestCase
{
    public function testIsHoliday()
    {
        $date = new DateTime('2024-04-30');
        $this->assertFalse(HolidayJp::isHoliday($date));
    }
}
