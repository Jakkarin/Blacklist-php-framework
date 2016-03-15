<?php

/**
* String random
* @param Int (number of string to return)
* @return String (random string)
*/
if ( ! function_exists('str_random')) {
    function str_random($_n = 60) {
        $_characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $_randString = '';
        for ($_i = 0; $_i < $_n; $_i++) {
            $_randString .= $_characters[rand(0,62)];
        } return $_randString;
    }
}
