<?php

if (!defined('ABSPATH'))
{
   exit;
}

add_filter('wp_mail_from_name', 'rlm_mail_change_name');
function rlm_mail_change_name()
{
   return get_bloginfo('name');
}

add_filter('wp_mail_from', 'rlm_email_change_email');
function rlm_email_change_email()
{
   return get_bloginfo('admin_email');
}

add_filter('retrieve_password_notification_email', 'rlm_mail_retrieve_password_change_body_mail', 10, 4);
function rlm_mail_retrieve_password_change_body_mail(array $defaults, string $key, $user_login, object $user_data)
{
   $template_replaces = [
      "{{redirect}}"    => wp_login_url() . "?action=rp&key=$key&login={$user_login}&wp_lang=" . get_bloginfo('language'),
      "{{buttonTitle}}" => esc_html__('Redefinir senha', 'rlm_theme'),
   ];

   $profile = get_user_meta($user_data->ID);
   $company = get_user_meta($user_data->ID, 'company_id', true); // Supondo que 'company_id' seja um campo meta que armazena o ID da empresa associada

   $owner_id = get_user_meta($user_data->ID, 'owner_id', true); // Supondo que 'owner_id' seja um campo meta
   $owner_profile = get_user_meta($owner_id);

   $company_name = 'English Pass';
   $owner_name   = isset($owner_profile['full_name'][0]) ? $owner_profile['full_name'][0] : '';

   if (!empty($company))
   {
      $company_name = get_post($company)->post_title;
   }

   $content_replaces = [
      '{{studentName}}' => isset($profile['first_name'][0]) ? $profile['first_name'][0] : '',
      '{{companyName}}' => $company_name,
      '{{ownerName}}'   => $owner_name,
   ];

   $new_mail = rlm_filter_mail_defaults('reset_user_password', $template_replaces, $content_replaces);

   if (empty($new_mail))
   {
      return $defaults;
   }

   return $new_mail;
}

function rlm_mail_send_email_confirmation(object $user_data, string $company_name)
{
   $template_replaces = [
      "{{redirect}}"    => wp_login_url(),
      "{{buttonTitle}}" => esc_html__('Confirmar meu e-mail', 'rlm_theme'),
   ];

   $profile = get_user_meta($user_data->ID);

   $content_replaces = [
      '{{studentName}}' => isset($profile['first_name'][0]) ? $profile['first_name'][0] : '',
      '{{companyName}}' => $company_name,
   ];

   $new_mail = rlm_filter_mail_defaults('check_user_email', $template_replaces, $content_replaces);

   if (empty($new_mail))
   {
      return false;
   }

   wp_mail($user_data->user_email, $new_mail['subject'], $new_mail['message'], $new_mail['headers']);
}

add_action('rlm_signup_payment_confirmed', 'rlm_mail_send_payment_confirmed', 10, 2);
function rlm_mail_send_payment_confirmed(object $user_data, string $company_name)
{
   $template_replaces = [
      "{{redirect}}"    => wp_login_url(),
      "{{buttonTitle}}" => esc_html__('Entrar na Plataforma', 'rlm_theme'),
   ];

   $profile = get_user_meta($user_data->ID);

   $content_replaces = [
      '{{studentName}}' => isset($profile['first_name'][0]) ? $profile['first_name'][0] : '',
      '{{companyName}}' => $company_name,
   ];

   $new_mail = rlm_filter_mail_defaults('welcome_user', $template_replaces, $content_replaces);

   if (empty($new_mail))
   {
      return false;
   }

   wp_mail($user_data->user_email, $new_mail['subject'], $new_mail['message'], $new_mail['headers']);
}

add_action('rlm_mail_send_invite_dependent', 'rlm_mail_send_invite_dependent_cb', 10, 2);
function rlm_mail_send_invite_dependent_cb(string $mail_to, string $register_link)
{
   $template_replaces = [
      "{{redirect}}"    => $register_link,
      "{{buttonTitle}}" => esc_html__('Junte-se agora', 'rlm_theme'),
   ];

   $content_replaces = [];

   $new_mail = rlm_filter_mail_defaults('invite_user', $template_replaces, $content_replaces);

   if (empty($new_mail))
   {
      return false;
   }

   wp_mail($mail_to, $new_mail['subject'], $new_mail['message'], $new_mail['headers']);
}

add_action('rlm_mail_send_certificate_generated', 'rlm_mail_send_certificate_generated_cb');
function rlm_mail_send_certificate_generated_cb(int $user_ID)
{
   $template_replaces = [
      "{{redirect}}"    => home_url('dashboard/profile/?menu=certificados'),
      "{{buttonTitle}}" => esc_html__('Ver meus certificados', 'rlm_theme'),
   ];

   $profile = get_user_meta($user_ID);

   $content_replaces = [
      '{{studentName}}' => isset($profile['first_name'][0]) ? $profile['first_name'][0] : '',
   ];

   $new_mail = rlm_filter_mail_defaults('certificate_generated', $template_replaces, $content_replaces);

   if (empty($new_mail))
   {
      return false;
   }

   $user_email = get_userdata($user_ID)->user_email;
   wp_mail($user_email, $new_mail['subject'], $new_mail['message'], $new_mail['headers']);
}
