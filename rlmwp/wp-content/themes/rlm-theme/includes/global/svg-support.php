<?php

if (!defined('ABSPATH'))
{
   exit;
}

add_filter('upload_mimes', 'rlm_svg_support');
function rlm_svg_support($mimes)
{
   $mimes['svg'] = 'image/svg+xml';
   return $mimes;
}
