<?php
class NI_session
{
    public static function logout($args = array())
    {
        $args = func_get_args();
        session_unset();
        session_destroy() ;
        NI_redirect::path($args[0]);
        if (isset($args[0])) {
            NI_redirect::path($args[0]);
        } else {
            NI_redirect::path("/");
        }
    }

    public static function get($SessionKey, $defult = null)
    {
        return $_SESSION[$SessionKey] ?? $defult ;
    }

    public static function set($SessionKey, $SessionValue)
    {
        $_SESSION[$SessionKey] = $SessionValue;
    }

    public static function has($SessionKey)
    {
        return isset($_SESSION[$SessionKey]);
    }

    public static function remove($SessionKey)
    {
        unset($_SESSION[$SessionKey]);
    }

    public static function all()
    {
        return $_SESSION;
    }

    public static function pull($SessionKey)
    {
        $value = self::get($SessionKey);
        self::remove($SessionKey);
        return $value;
    }

    public static function destroy()
    {
        session_destroy();
        unset($_SESSION);
    }
}
