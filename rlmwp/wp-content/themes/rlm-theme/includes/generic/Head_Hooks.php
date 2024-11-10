<?php

if (!defined('ABSPATH'))
{
   exit;
}

/**
 * Changes the order of `wp_head` hooks. Also creates new ones to keep this order.
 *
 * To include specific content to head, use:
 *
 * - Resource hints to browsers (dns-prefetch, preconnect, prefetch, prerender)
 * `wp_resource_hints` (wp filter)
 *
 * - Preload:
 * `wp_preload_resources` (wp filter)
 *
 * - SEO Metas:
 * `rlm_head_metas` (custom action)
 *
 * - External CSS (that can't be done with wp_enqueue_style):
 * `wp_print_styles` (wp action)
 *
 * - Inline CSS (that can't be done with wp_add_inline_style):
 * `wp_get_custom_css` (wp filter)
 *
 * - Scripts (that can't be done with wp_enqueue_script):
 * `rlm_head_scripts` (custom action)
 */
class Head_Hooks
{
   /**
    * Initiate class.
    *
    * @param array $not_reorder Optional. Allows choose which defaults hooks will not be reorder.
    *                Accepts 'title', 'preload', 'resource', 'styles', 'scripts' and 'feeds'.
    */
   public function __construct(array $not_reorder = ['title', 'feeds'])
   {
      if (!in_array('title', $not_reorder) && !$this->has_yoast())
      {
         remove_action('wp_head', '_wp_render_title_tag', 1);

         add_action('wp_head', '_wp_render_title_tag', 9);
      }

      if (!in_array('preload', $not_reorder))
      {
         remove_action('wp_head', 'wp_preload_resources', 1);

         add_action('wp_head', 'wp_preload_resources', 6);
      }

      if (!in_array('resource', $not_reorder))
      {
         remove_action('wp_head', 'wp_resource_hints', 2);

         add_action('wp_head', 'wp_resource_hints', 5);
      }

      if (!in_array('styles', $not_reorder))
      {
         remove_action('wp_head', 'wp_print_styles', 8);
         remove_action('wp_head', 'locale_stylesheet');

         add_action('wp_head', 'wp_print_styles', 15);
         add_action('wp_head', 'locale_stylesheet', 16);
      }

      if (!in_array('scripts', $not_reorder))
      {
         remove_action('wp_head', 'wp_print_head_scripts', 9);

         add_action('wp_head', 'wp_print_head_scripts', 20);
      }

      if (!in_array('feeds', $not_reorder))
      {
         remove_action('wp_head', 'feed_links', 2);
         remove_action('wp_head', 'feed_links_extra', 3);

         add_action('wp_head', 'feed_links', 10);
         add_action('wp_head', 'feed_links_extra', 10);
      }

      add_action('wp_head', [$this, 'prints_meta_charset'], 1);
      add_action('wp_head', [$this, 'prints_metas'], 10);
      add_action('wp_head', [$this, 'prints_scripts'], 20);
   }

   function prints_meta_charset()
   {
      echo '<meta charset="' . get_bloginfo('charset') . '" />';
      echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
      echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
   }

   function prints_metas()
   {
      do_action('rlm_head_metas');
   }

   function prints_scripts()
   {
      do_action('rlm_head_scripts');
   }

   private function has_yoast()
   {
      include_once ABSPATH . 'wp-admin/includes/plugin.php';

      return (
         is_plugin_active('wordpress-seo/wp-seo.php') ||
         is_plugin_active('wordpress-seo-premium/wp-seo-premium.php')
      );
   }
}

new Head_Hooks();
