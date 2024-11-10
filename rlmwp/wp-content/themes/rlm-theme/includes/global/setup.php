<?php

if (!defined('ABSPATH'))
{
   exit;
}

if (is_user_logged_in() && !current_user_can('edit_posts'))
{
   add_filter('show_admin_bar', '__return_false');
}

add_post_type_support('page', 'excerpt');

// add_action('admin_menu', 'rlm_hide_unused_menus');
function rlm_hide_unused_menus()
{
   remove_menu_page('edit.php');
   remove_menu_page('edit-comments.php');

   if (!current_user_can('manage_options'))
   {

      remove_menu_page('upload.php');
      remove_menu_page('tools.php');
      remove_menu_page('options-general.php');
   }

   if (!current_user_can('manage_options') && !current_user_can('manager'))
   {
      remove_menu_page('users.php');
   }
}

add_filter('user_row_actions', 'rlm_remove_view_link_from_user_list');
function rlm_remove_view_link_from_user_list($actions)
{
   unset($actions['view']);
   return $actions;
}

function rlm_get_palette_styles_inline(array $colors)
{
   if (empty($colors))
   {
      return '';
   }

   $custom_css = '';

   foreach ($colors as $color)
   {
      $custom_css .= "
       .has-{$color['slug']}-color { color: {$color['color']}; }
       .has-{$color['slug']}-background-color { background-color: {$color['color']}; }
       .has-{$color['slug']}-border-color { border-color: {$color['color']}; }";
   }

   return $custom_css;
}

// add_action('after_setup_theme', 'rlm_custom_colors');
function rlm_custom_colors()
{
   $colors = [
      [
         'name'  => 'Black',
         'slug'  => 'black',
         'color' => '#000000',
      ],
      [
         'name'  => 'White',
         'slug'  => 'white',
         'color' => '#ffffff',
      ],
      [
         'name'  => 'Gray',
         'slug'  => 'gray',
         'color' => '#cccccc',
      ],
      [
         'name'  => 'Blue',
         'slug'  => 'blue',
         'color' => '#0073aa',
      ],
      [
         'name'  => 'Dark Blue',
         'slug'  => 'dark-blue',
         'color' => '#005177',
      ],
      [
         'name'  => 'Green',
         'slug'  => 'green',
         'color' => '#21759b',
      ],
      [
         'name'  => 'Red',
         'slug'  => 'red',
         'color' => '#dc3232',
      ],
      [
         'name'  => 'Orange',
         'slug'  => 'orange',
         'color' => '#ffb900',
      ],
   ];

   add_theme_support('editor-color-palette', $colors);

   $inline_styles = rlm_get_palette_styles_inline($colors);

   add_action('wp_enqueue_scripts', function () use ($inline_styles)
   {
      if (!is_singular())
      {
         return;
      }

      wp_add_inline_style('all', $inline_styles);
   });

   add_action('admin_enqueue_scripts', function () use ($inline_styles)
   {
      $screen = get_current_screen();

      if ($screen->base !== 'post')
      {
         return;
      }

      wp_add_inline_style('admin', $inline_styles);
   });
}

add_action('after_setup_theme', 'rlm_theme_setup');
function rlm_theme_setup()
{
   // Make theme available for translation
   load_theme_textdomain('rlm_theme', get_template_directory() . '/languages');

   // Add default posts and comments RSS feed links to head.
   add_theme_support('automatic-feed-links');

   // Enable support for post thumbnails.
   add_theme_support('post-thumbnails');

   add_theme_support('title-tag');

   // Add support for HTML5 markup.
   add_theme_support(
      'html5',
      [
         'search-form',
         'comment-form',
         'comment-list',
         'gallery',
         'caption',
      ]
   );

   add_theme_support(
      'custom-logo',
      [
         'height'      => 100,
         'width'       => 400,
         'flex-height' => true,
         'flex-width'  => true,
      ]
   );

   register_nav_menus(
      [
         'menu-1' => esc_html__('Primary', 'rlm_theme'),
      ]
   );
}

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
add_action('widgets_init', 'rlm_theme_widgets_init');
function rlm_theme_widgets_init()
{
   register_sidebar(
      [
         'name'          => esc_html__('Sidebar', 'rlm_theme'),
         'id'            => 'sidebar-1',
         'description'   => esc_html__('Add widgets here.', 'rlm_theme'),
         'before_widget' => '<section id="%1$s" class="widget %2$s">',
         'after_widget'  => '</section>',
         'before_title'  => '<h2 class="widget-title">',
         'after_title'   => '</h2>',
      ]
   );
}
