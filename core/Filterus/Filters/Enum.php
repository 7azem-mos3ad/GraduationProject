<?php

namespace Filterus\Filters;

class Enum extends \Filterus\Filter
{
    protected $defaultOptions = array(
        'val' => 0
    );

    public function filter($var)
    {
        if (is_object($var) && method_exists($var, '__toString')) {
            $var = (string) $var;
        }
        if (!is_scalar($var)) {
            return null;
        }
        $var = (string) $var;

        $ArrayOfValues = explode(';', $this->options['val']);
        if (!in_array($var, $ArrayOfValues)) {
            return null;
        }
        return $var;
    }

    public function validate($var)
    {
        if (is_object($var) && method_exists($var, '__toString')) {
            $var = (string) $var;
        }
        return parent::validate($var);
    }
}
