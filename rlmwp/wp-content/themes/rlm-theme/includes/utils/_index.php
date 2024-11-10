<?php

if (!defined('ABSPATH'))
{
   exit;
}

class RLM__Utils
{
   /**
    * Identify if the current URL is a custom login page and return the current company if set.
    *
    * @return bool|string If `true` or `string`, it is a login page. If `string`, it is the current company. If `false`, it is not a login page.
    */
   public static function is_login()
   {
      $request = parse_url(rawurldecode($_SERVER['REQUEST_URI']));
      $login_endpoint = LOGIN_ENDPOINT;

      preg_match("/^\/([a-zA-Z0-9_-]+)?\/?{$login_endpoint}\/?$/i", $request['path'], $matches);

      if (empty($matches))
      {
         return false;
      }

      if (str_starts_with($request['path'], "/$login_endpoint"))
      {
         return true;
      }

      if (empty($matches[1]))
      {
         return false;
      }

      return $matches[1];
   }

   /**
    * Check if a table exists in the database.
    *
    * @param string $table The name of the table to check.
    * @return bool True if the table exists, false otherwise.
    */
   public static function checks_table_exist($table)
   {
      global $wpdb;
      $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table));

      return $wpdb->get_var($query) === $table;
   }

   /**
    * Get a list of states in Brazil.
    *
    * @param bool $without_none Whether to include an option for 'None'.
    * @return array The list of states.
    */
   public static function get_states($without_none = true)
   {
      $none = ['' => 'Nenhum'];

      $states = [
         'AC' => 'Acre',
         'AL' => 'Alagoas',
         'AP' => 'Amapá',
         'AM' => 'Amazonas',
         'BA' => 'Bahia',
         'CE' => 'Ceará',
         'DF' => 'Distrito Federal',
         'ES' => 'Espírito Santo',
         'GO' => 'Goiás',
         'MA' => 'Maranhão',
         'MT' => 'Mato Grosso',
         'MS' => 'Mato Grosso do Sul',
         'MG' => 'Minas Gerais',
         'PA' => 'Pará',
         'PB' => 'Paraíba',
         'PR' => 'Paraná',
         'PE' => 'Pernambuco',
         'PI' => 'Piauí',
         'RJ' => 'Rio de Janeiro',
         'RN' => 'Rio Grande do Norte',
         'RS' => 'Rio Grande do Sul',
         'RO' => 'Rondônia',
         'RR' => 'Roraima',
         'SC' => 'Santa Catarina',
         'SP' => 'São Paulo',
         'SE' => 'Sergipe',
         'TO' => 'Tocantins'
      ];

      if ($without_none)
      {
         return $states;
      }

      return array_merge($none, $states);
   }

   /**
    * Convert seconds to a human-readable format of hours and minutes.
    * If the time is less than one hour, it returns only the minutes.
    *
    * @param integer $seconds The time duration in seconds.
    * @return string The formatted time in hours and minutes or just minutes if less than an hour.
    */
   public static function format_seconds($seconds, $resume_minutes = false)
   {
      $seconds = (int) $seconds;

      if (empty($seconds))
      {
         return 0;
      }

      $minutes          = (int) ($seconds / 60);
      $hours            = (int) ($minutes / 60);
      $remainingMinutes = $minutes % 60;

      if ($hours > 0)
      {
         return sprintf('%dh %dm', $hours, $remainingMinutes);
      }

      $minutes_label = $resume_minutes ? 'm' : _n(' Minuto', ' Minutos', $remainingMinutes, 'rlm_theme');

      return sprintf('%d%s', $remainingMinutes, $minutes_label);
   }

   /**
    * Includes a class file, from the classes plugin folder.
    *
    * @param string $class_name The class file name
    * @param string $method     The method to include the file. Accepts `include_once` or `require_once`. Default: `require_once`.
    */
   public static function load_class($class_name, $method = 'require_once')
   {
      $class_path = self::path_resolve([dirname(__FILE__), 'includes', 'classes', "{$class_name}.php"]);

      if (file_exists($class_path))
      {
         if ('require_once' === $method)
         {
            require_once $class_path;
         }
         elseif ('include_once' === $method)
         {
            include_once $class_path;
         }
         else
         {
            trigger_error(sprintf(esc_attr__('%s não é include_once ou require_once.', 'rlm_theme'), $method), E_USER_ERROR);
         }
      }
   }

   /**
    * Resolves an array of directories, with the correct slash for the current operational system.
    *
    * @param array $paths array of string of directories names.
    *
    * @return string Returns the full path.
    */
   public static function path_resolve(array $paths)
   {
      if (!is_array($paths))
      {
         trigger_error(esc_attr__('Primeiro argumento precisa ser um array.', 'rlm_theme'), E_USER_ERROR);
      }

      return implode(DIRECTORY_SEPARATOR, $paths);
   }

   /**
    * @see debug() See the alias for full documentation.
    */
   public static function debug()
   {
      $args       = func_get_args();
      $file_index = 0;

      if (isset($args[1]) && 'DEBUG_ARRAY_IN_SECOND_LEVEL' === $args[1])
      {
         $args       = $args[0];
         $file_index = 1;
      }

      if (!WP_DEBUG)
      {
         return $args[0];
      }

      $files = debug_backtrace();
      $log   = "\n[DEBUG] {$files[$file_index]['file']}:{$files[$file_index]['line']}";

      foreach ($args as $key => $arg)
      {
         $key  = str_pad($key, 3, '0', STR_PAD_LEFT);

         $log .= "\n[ {$key} ] ";
         $log .= var_export($arg, 1);
      }

      if (WP_DEBUG_LOG)
      {
         error_log($log);
      }

      if (
         WP_DEBUG_DISPLAY &&
         !defined('XMLRPC_REQUEST') &&
         !defined('REST_REQUEST') &&
         !defined('MS_FILES_REQUEST') &&
         !(defined('WP_INSTALLING') && WP_INSTALLING) &&
         !wp_doing_ajax() &&
         !wp_is_json_request()
      )
      {
         print_r("<pre class=\"rlm_theme_debug\">$log</pre>");
      }

      do_action('rlm_theme_debug', $log);

      return $args[0];
   }
}


if (!function_exists('debug'))
{
   /**
    * Prints on the default debug file or shows in screen all arguments variables passed.
    *
    * ##### Description
    *
    * Also can be used in hooks. Code example:
    * ```php
    * add_action('edit_comment', 'debug', 10, 2);
    * add_filter('the_content', 'debug', 10);
    * ```
    *
    * Configuration:
    *
    * - If `WP_DEBUG` is false, does not do anything.
    * - If `WP_DEBUG_LOG` is false, does not print on the debug file.
    * - If `WP_DEBUG_DISPLAY` is false, does not print on screen.
    *
    * @param mixed $args Any quantity of arguments to debug.
    *
    * @return mixed The first argument passed.
    *
    * @since 1.0.0
    */
   function debug(...$args)
   {
      return RLM__Utils::debug(func_get_args(), 'DEBUG_ARRAY_IN_SECOND_LEVEL');
   }
}
