<?php

if (!defined('ABSPATH'))
{
   exit;
}

function rlm_data_replacement(string $text, array $options)
{
   $search = array_keys($options);
   $replace = array_values($options);

   return str_replace($search, $replace, $text);
}

function rlm_get_template_mail_fields(string $template_id)
{
   $template_fields = [];

   if (have_rows('template_mails', 'general_emails'))
   {
      while (have_rows('template_mails', 'general_emails'))
      {
         the_row();

         if ($template_id === get_sub_field('id'))
         {
            $template_fields['subject'] = get_sub_field('subject');
            $template_fields['content'] = get_sub_field('content');
            $template_fields['image']   = get_sub_field('image')['sizes']['large'] ?? '';
         }
      }
   }

   return $template_fields;
}

function rlm_get_mail_template(string $mail_template_path)
{
   return file_get_contents(get_theme_file_path("modules/mails/templates/{$mail_template_path}.html"));
}

function rlm_filter_mail_defaults($template_key, $template_replaces, $content_replaces = [])
{
   $template_fields = rlm_get_template_mail_fields($template_key);

   if (empty($template_fields))
   {
      return null;
   }

   $template = rlm_get_mail_template('default');

   if (empty($content_replaces))
   {
      $mail_message = $template_fields['content'];
   }
   else
   {
      $mail_message = rlm_data_replacement($template_fields['content'], $content_replaces);
   }

   $default_replaces = [
      "{{subject}}"     => $template_fields['subject'],
      "{{image}}"       => $template_fields['image'],
      "{{brandName}}"   => get_bloginfo('name'),
      "{{mailContent}}" => $mail_message,
      "{{currentYear}}" => current_time('Y'),
   ];

   $message = rlm_data_replacement($template, $default_replaces);
   $message = rlm_data_replacement($message, $template_replaces);

   $defaults['message'] = $message;
   $defaults['headers'] = ['Content-Type: text/html; charset=UTF-8'];
   $defaults['subject'] = $template_fields['subject'];

   return $defaults;
}
