<?php

if (!defined('ABSPATH'))
{
   exit;
}

add_filter('login_headerurl', 'rlm_login_logo_url');
function rlm_login_logo_url()
{
   return home_url();
}

// add_action('login_footer', 'rlm_sign_footer_bar');
function rlm_sign_footer_bar()
{
   if (!empty($_GET['action']) || !empty($_GET['checkemail']))
   {
      return;
   }
}

// add_action('login_form', 'rlm_custom_wp_login_welcome');
function rlm_custom_wp_login_welcome()
{
   $welcome_msg = esc_html__('Welcome back!', 'rlm_theme');

   printf('<h2 class="text-lg text-center leading-6 font-semibold text-black font-secondary mb-6 order-1 uppercase">%s</h2>', $welcome_msg);
}

add_action('login_message', 'rlm_custom_wp_login_message');
function rlm_custom_wp_login_message()
{
   if (empty($_GET['action']) && empty($_GET['checkemail']))
   {
      return;
   }

   if (!empty($_GET['action']) && $_GET['action'] === 'lostpassword' || !empty($_GET['action']) && $_GET['action'] === 'resetpass' || !empty($_GET['action']) && $_GET['action'] === 'rp' || !empty($_GET['checkemail']))
   {
      printf('<h2 class="text-lg text-center leading-6 font-bold text-gray-900 font-secondary mb-[1.9375rem] order-0">%s</h2>', esc_html__('Recuperação de senha', 'rlm_theme'));
   }

   if (!empty($_GET['action']) && $_GET['action'] === 'resetpass')
   {
      echo '<p class="message" id="login-message">' . esc_html__('Senha alterada com sucesso!', 'rlm_theme') . '</p>';

      echo '<a class="btn btn-primary order-3 mt-6" href="' . wp_login_url() . '">' . esc_html__('Entrar', 'rlm_theme') . '</a>';
   }

   if (!empty($_GET['checkemail']))
   {
      echo '<a class="login-message-link order-3" href="' . wp_login_url() . '">' . esc_html__('Voltar', 'rlm_theme') . '</a>';
   }
}

// add_filter('gettext', 'rlm_login_labels', 10, 3);
function rlm_login_labels($translated_text, $_text, $domain)
{
   global $pagenow;
   if ('wp-login.php' !== $pagenow)
   {
      return $translated_text;
   }

   $is_lostpassword = !empty($_GET['action']) && $_GET['action'] === 'lostpassword' || !empty($_GET['action']) && $_GET['action'] === 'rp';

   if ('default' === $domain)
   {
      switch ($translated_text)
      {
         case 'Nome de usuário ou endereço de e-mail':
            $translated_text = $is_lostpassword ? esc_html__('Digite seu e-mail', 'rlm_theme') : esc_html__('Seu login ou e-mail', 'rlm_theme');
            break;

         case 'Obter nova senha':
            if ($is_lostpassword)
            {
               $translated_text =  esc_html__('Enviar Link', 'rlm_theme');
            }
            break;

         case 'Senha':
            $translated_text = esc_html__('Sua senha', 'rlm_theme');
            break;

         case 'Perdeu a senha?':
            $translated_text = esc_html__('Esqueci minha senha', 'rlm_theme');
            break;

         case 'Acessar':
            $translated_text = esc_html__('Entrar', 'rlm_theme');
            if ($is_lostpassword)
            {
               $translated_text = esc_html__('Voltar', 'rlm_theme');
            }
            break;

         case 'Nova senha':
            if ($is_lostpassword)
            {
               $translated_text = esc_html__('Digite uma nova senha', 'rlm_theme');
            }
            break;

         case 'Salvar senha':
            if ($is_lostpassword)
            {
               $translated_text = esc_html__('Confirmar', 'rlm_theme');
            }
            break;

         default:
            break;
      }
   }

   return $translated_text;
}

// add_filter('wp_login_errors', 'rlm_custom_retrieve_password_message', 10);
function rlm_custom_retrieve_password_message($message)
{
   global $pagenow;

   if ('wp-login.php' !== $pagenow)
   {
      return;
   }

   if (!empty($message->errors['confirm'][0]))
   {
      $message->errors['confirm'][0] = esc_html__('O link de recuperação foi enviado para o seu e-mail!', 'rlm_theme');
   }

   if (!empty($message->errors['invalid_username'][0]))
   {
      $message->errors['invalid_username'][0] = '<strong>Erro: </strong>' . esc_html__('Usuário ou senha inválidos!', 'rlm_theme');
   }

   if (!empty($message->errors['incorrect_password'][0]))
   {
      $message->errors['incorrect_password'][0] = '<strong>Erro: </strong>' . esc_html__('Usuário ou senha inválidos!', 'rlm_theme');
   }

   return $message;
}
