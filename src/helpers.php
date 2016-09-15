<?php

/**
 * Define helper for easy use
 */
if ( ! function_exists('dotnot')) {
    function dotnot($root, $path = NULL)
    {
        $dotnot = new DotNot\DotNot($root);
        
        return $path === NULL ? $dotnot : $dotnot->get($path);
    }
}