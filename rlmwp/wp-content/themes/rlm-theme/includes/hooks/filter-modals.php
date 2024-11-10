<?php

if (!defined('ABSPATH'))
{
   exit;
}

add_action('wp_footer', 'rlm_render_modals');
function rlm_render_modals()
{
   $modals = apply_filters('rlm_modals', $modals = []);

   if (!empty($modals))
   {
      foreach ($modals as $modal_options)
      {
         get_template_part('components/modal', null, $modal_options);
      }
   }
}

add_filter('rlm_modals', 'rlm_custom_modals');
function rlm_custom_modals($modals)
{
   // $modals[] = [
   //    'id'          => 'update-user-access',
   //    'icon'        => 'lock-contained',
   //    'title'       => esc_html__('Editar dados de acesso', 'rlm_theme'),
   //    'description' => esc_html__(
   //       'Altere os dados de acesso que você utiliza para entrar na plataforma',
   //       'rlm_theme'
   //    ),
   //    'content_template_path' => 'pages/page-profile/components/form-update-user-access',
   // ];

   return $modals;
}
