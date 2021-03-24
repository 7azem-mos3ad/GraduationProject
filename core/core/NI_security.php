<?php
class NI_security
{
    public static function anti_XSS($str)
    {
        if (empty($str)) {
            return ' ';
        } else {
            $output = '';
            $str = str_split($str);
    
            for ($i=0;$i<count($str);$i++) {
                $chrNum = ord($str[$i]);
                $chr = $str[$i];
    
                if ($chrNum === 226) {
                    if (isset($str[$i+1]) && ord($str[$i+1]) === 128) {
                        if (isset($str[$i+2]) && ord($str[$i+2]) === 168) {
                            $output .= '\u2028';
                            $i += 2;
                            continue;
                        }
                        if (isset($str[$i+2]) && ord($str[$i+2]) === 169) {
                            $output .= '\u2029';
                            $i += 2;
                            continue;
                        }
                    }
                }
    
                switch ($chr) {
                    case "\n":
                    case "\r":
                    case "\\":
                    case "&":
                    case '"':
                    case "'":
                    case "<":
                    case ">":
                        $output .= sprintf("\\u%04x", $chrNum);
                        break;
                    default:
                        $output .= $str[$i];
                        break;
                }
            }
    
            return $output;
        }
    }

    public static function check($var)
    {
        $var=htmlspecialchars($var) ;
        $var=trim($var) ;
        $var=stripcslashes($var) ;
        return $var;
    }

    public static function Filterus($factory, $data)
    {
        $Filter = Filterus\Filter::factory($factory);
        return [$Filter->validate($data),$Filter->filter($data)];
    }

    public static function PoolFilterus(array $factory, $data)
    {
        $Filter = Filterus\Filter::pool(... $factory);
        return [$Filter->validate($data),$Filter->filter($data)];
    }

    public static function authorized($session, $value, $path)
    {
        if (!isset($_SESSION[$session]) || $_SESSION[$session] !== $value) {
            NI_redirect::path($path);
            exit;
        }
    }
    public static function unauthorized($session, $value, $path)
    {
        if (isset($_SESSION[$session]) && $_SESSION[$session] === $value) {
            NI_redirect::path($path);
            exit;
        }
    }
}
