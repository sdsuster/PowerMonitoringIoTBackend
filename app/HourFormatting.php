<?php

namespace App;

use Carbon\Carbon;

class HourFormatting
{
    public static function getFormatedHour(Carbon $carbon)
    {

        return ((int) ($carbon->getTimestamp() / 3600)) - 434417;
        //434417 = Total HOURS diferent between UNIX begin time to 24-Juli-2019

    }
    public static function getFormatedDayHour(Carbon $carbon)
    {

        return (((int) (HourFormatting::getFormatedHour($carbon) / 24)) * 24);
    }
    public static function getFormatedWeekHour(Carbon $carbon)
    {

        return (int) (HourFormatting::getFormatedDayHour($carbon) - 24 * 7);
    }
    public static function getFormatedMonthHour(Carbon $carbon)
    {
        return (int) (HourFormatting::getFormatedDayHour($carbon) - 24 * 30);
    }
}
