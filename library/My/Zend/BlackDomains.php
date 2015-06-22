<?php

class My_Zend_BlackDomains
{
    private static $domains = array(
            '5giay',
            'enbac',
            'rongbay',
            'muare',
            'chodientu',
            'vatgia',
            'tinhte',
            'denthan',
            'nava',
            'batda'
    );

    private static $asterisks;

    /*
    private function vbstrlen($string)
    {
        return strlen(preg_replace('#&\#([0-9]+);#', '_', $string));
    }
    */

    function censor($text)
    {
        // ASCII character search 0-47, 58-64, 91-96, 123-127
        $symbols = '\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f';

        foreach (self::$domains as $domain)
        {
            // words are delimited by ASCII characters outside of A-Z, a-z and 0-9
            $text = preg_replace(
                    '#(?<=[' . $symbols . ']|^)' . $domain . '(?=[' . $symbols . ']|$)#si',
                    '',
                    $text
            );
        }
        return $text;
    }
}