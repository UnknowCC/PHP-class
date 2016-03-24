<?php

/**
 * 日期格式设置
 */
class DateFormat
{
    private static $format = 'Y-m-d H:i:s';

    public static function format($date, $format = null)
    {
        if (is_null($format)) {
            $format = static::$format;
        }

        $date = new DateTime($date, new DateTimeZone('GMT'));
        $date->setTimezone(new DateTimeZone('Asia/Shanghai'));

        return $date->format($format);
    }
}
