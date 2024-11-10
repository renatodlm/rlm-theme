<?php

if (!defined('ABSPATH'))
{
   exit;
}

require_once implode(DIRECTORY_SEPARATOR, [get_template_directory(), 'includes', 'generic', 'TGM_Plugin_Activation.php']);

add_action('tgmpa_register', 'rlm_register_required_plugins');
function rlm_register_required_plugins()
{
   $plugins = [
      [
         'name'             => 'Advanced Custom Fields PRO',
         'slug'             => 'advanced-custom-fields-pro',
         'source'           => get_template_directory() . '/plugins/advanced-custom-fields-pro.zip',
         'version'          => '6.3.9',
         'required'         => true,
         'force_activation' => false,
      ]
   ];

   $config = [
      'id'           => 'rlm_theme',
      'default_path' => '',
      'menu'         => 'tgmpa-install-plugins',
      'parent_slug'  => 'themes.php',
      'capability'   => 'edit_theme_options',
      'has_notices'  => true,
      'dismissable'  => true,
      'dismiss_msg'  => '',
      'is_automatic' => true,
      'message'      => '',
   ];

   tgmpa($plugins, $config);
}
