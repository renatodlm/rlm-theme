<?php

if (!defined('ABSPATH')) exit;

// add_action('phpmailer_init', 'rlm_phpmailer_init');
function rlm_phpmailer_init($phpmailer)
{
   if (!defined('SMTP_HOST') || !defined('SMTP_USER') || !defined('SMTP_PASS'))
   {
      return;
   }

   if (empty(SMTP_HOST) || empty(SMTP_USER) || empty(SMTP_PASS))
   {
      return;
   }

   $phpmailer->Mailer     = 'smtp';
   $phpmailer->Host       = SMTP_HOST;
   $phpmailer->Username   = SMTP_USER;
   $phpmailer->Password   = SMTP_PASS;
   $phpmailer->SMTPAuth   = true;
   $phpmailer->Port       = 587;
   $phpmailer->SMTPSecure = 'tls';
   $phpmailer->setFrom(SMTP_USER, \get_bloginfo('name'));
   // $phpmailer->SMTPDebug  = 3;
}

// add_action('wp_mail_failed', 'rlm_phpmailer_report_errors');
function rlm_phpmailer_report_errors($error)
{
   if (!WP_DEBUG || !WP_DEBUG_LOG)
   {
      return;
   }

   error_log($error->get_error_message());
}
