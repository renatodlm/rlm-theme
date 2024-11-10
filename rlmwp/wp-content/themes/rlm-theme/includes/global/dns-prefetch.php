<?php

if (!defined('ABSPATH'))
{
   exit;
}

add_filter('wp_resource_hints', 'rlm_add_resource_hints', 10, 2);
function rlm_add_resource_hints($urls, $relation_type)
{
   global $pagenow;

   if ($pagenow === 'wp-login.php')
   {
      return $urls;
   }

   if (!in_array($relation_type, ['dns-prefetch', 'preconnect']))
   {
      return $urls;
   }

   if (is_admin())
   {
      return $urls;
   }

   $dns_domains = [
      ['href' => 'https://f.vimeocdn.com'],
      ['href' => 'https://fresnel.vimeocdn.com'],
      ['href' => 'https://i.vimeocdn.com'],
      ['href' => 'https://player.vimeo.com'],
      ['href' => 'https://vimeo.com'],
      ['href' => 'https://www.googletagmanager.com'],
      ['href' => 'https://www.gstatic.com'],
      ['href' => 'https://www.youtube.com'],
      ['href' => 'https://youglish.com'],
   ];

   return array_merge($urls, $dns_domains);
}
