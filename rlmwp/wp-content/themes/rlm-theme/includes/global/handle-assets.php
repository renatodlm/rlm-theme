<?php

if (!defined('ABSPATH'))
{
   exit;
}

add_action('wp_enqueue_scripts', 'rlm_enqueue_main_assets');
function rlm_enqueue_main_assets()
{
   wp_enqueue_style('all', get_theme_file_uri('assets/css/all.min.css'));

   wp_enqueue_script('all', get_theme_file_uri('assets/js/all.min.js'));

   wp_localize_script('all', 'ajax', [
      'ajaxNonce'     => wp_create_nonce('defaultNonce'),
      'ajaxUrl'       => admin_url('admin-ajax.php'),
      'home_url'      => home_url('dashboard'),
      'currentPostID' => get_the_ID(),
      'currentUserID' => get_current_user_id(),
      'env'           => wp_get_environment_type(),
      'api'           => site_url(rest_get_url_prefix() . '/v1'),
      'api_nonce'     => wp_create_nonce('wp_rest'),
   ]);
}

add_action('admin_enqueue_scripts', 'rlm_enqueue_admin_assets');
function rlm_enqueue_admin_assets($current_page)
{
   $allowed_pages = [
      'post-new.php',
      'user-edit.php',
      'post.php',
      'profile.php',
      'toplevel_page_report'
   ];

   if (!in_array($current_page, $allowed_pages))
   {
      return;
   }

   wp_enqueue_style('admin', get_theme_file_uri('assets/css/admin.min.css'));
   wp_enqueue_script('admin', get_theme_file_uri('assets/js/admin.min.js'));

   wp_localize_script('admin', 'ajax', [
      'ajaxNonce' => wp_create_nonce('defaultNonce'),
      'ajaxUrl'   => admin_url('admin-ajax.php'),
   ]);
}
