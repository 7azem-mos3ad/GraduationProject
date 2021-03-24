<?php
class NI_lang
{
    public static $arr_lang = [];

    public static function set($arr)
    {
        self::$arr_lang = $arr;
    }
    public static function index($word)
    {
        return self::$arr_lang[$word];
    }
}
