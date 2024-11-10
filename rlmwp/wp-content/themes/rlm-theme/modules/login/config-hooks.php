<?php

if (!defined('ABSPATH')) exit;

add_action('wp_loaded', 'rlm_rewrite_url');
function rlm_rewrite_url()
{
   global $pagenow;

   if (!RLM__Utils::is_login())
   {
      return;
   }

   $pagenow = 'wp-login.php';

   global $error, $interim_login, $action, $user_login;

   @require_once ABSPATH . 'wp-login.php';
   exit;
}

add_filter('site_url', 'rlm_replace_login_url');
add_filter('login_url', 'rlm_replace_login_url');
add_filter('logout_url', 'rlm_replace_login_url');
add_filter('lostpassword_url', 'rlm_replace_login_url');
add_filter('network_site_url', 'rlm_replace_login_url');
add_filter('wp_redirect', 'rlm_replace_login_url');
function rlm_replace_login_url($url)
{
   if (strrpos($url, 'wp-login.php') === false)
   {
      return $url;
   }

   $company = false;

   $slug = '';

   if (is_array($company))
   {
      if ('ID' === $company['type'])
      {
         $company_obj = get_post($company['term']);
         $slug = $company_obj->post_name;
      }
      elseif ('slug' === $company['type'])
      {
         $slug = $company['term'];
      }

      $slug = "$slug/";
   }

   if (strrpos($url, $slug) === false)
   {
      return str_replace('wp-login.php', $slug . LOGIN_ENDPOINT, $url);
   }

   return str_replace('wp-login.php', LOGIN_ENDPOINT, $url);
}

add_action('login_enqueue_scripts', 'rlm_enqueue_login_assets');
function rlm_enqueue_login_assets()
{
   wp_deregister_style('dashicons');
   wp_deregister_style('buttons');
   wp_deregister_style('forms');
   wp_deregister_style('l10n');
   wp_deregister_style('login');

   wp_enqueue_style('login', get_theme_file_uri('assets/css/login.min.css'));
   wp_enqueue_script('login', get_theme_file_uri('assets/js/login.min.js'));

   wp_localize_script('login', 'loginMessages', [
      'loginInputPlaceholder' => esc_html__('Digite seu e-mail', 'rlm_theme'),
      'passInputPlaceholder'  => esc_html__('Digite sua senha', 'rlm_theme'),
   ]);
}
