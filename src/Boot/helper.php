<?php
if (!function_exists('d')) {
    function d(): void
    {
        call_user_func_array('dump', func_get_args());
    }
}
/**
 * Dump variables and die.
 */
if (!function_exists('dd')) {
    function dd(): void
    {
        call_user_func_array('dump', func_get_args());
        die();
    }
}