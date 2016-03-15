<?php

/**
* String random
* @param Int (number of string to return)
* @return String (random string)
*/
if ( ! function_exists('str_random')) {
    function str_random($n = 60) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randString = '';
        for ($i = 0; $i < $n; $i++) {
            $randString .= $characters[rand(0,62)];
        } return $randString;
    }
}
