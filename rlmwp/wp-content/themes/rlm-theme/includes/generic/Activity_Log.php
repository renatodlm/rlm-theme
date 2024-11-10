<?php

/**
 * Name: Activity Log
 * Description: Registra eventos padrões do WP e personalizados, indicando com detalhes sobre origem e período.
 * Type: In-house
 */

if (!defined('ABSPATH'))
{
   exit;
}

class RLM_Activity_Log_Register
{
   public function __construct()
   {
      add_action('init', [$this, 'create_table']);

      $types = rlm_get_activity_log_types();

      foreach ($types as $type)
      {
         if (!isset($type['default']))
         {
            continue;
         }

         add_action(
            $type['default']['hook'],
            [$this, $type['default']['hook']],
            11,
            $type['default']['args'] ?? 1
         );
      }
   }

   public function create_table()
   {
      if (!!get_option('rlm_activity_log_tables_created'))
      {
         return;
      }

      global $wpdb;

      $table_name      = $wpdb->prefix . 'rlm_activity_log';
      $charset_collate = $wpdb->get_charset_collate();

      $log_query = "CREATE TABLE IF NOT EXISTS $table_name (
         `activity_ID` BIGINT(30) NOT NULL AUTO_INCREMENT,
         `user_ID` BIGINT(20),
         `post_ID` BIGINT(20),
         `term_ID` BIGINT(20),
         `activity_type` VARCHAR(191) NOT NULL,
         `activity_time` DATETIME NOT NULL,
         `activity_time_gmt` DATETIME NOT NULL,
         `activity_value` TEXT,
         `current_user_ID` INT DEFAULT '0',
         `current_IP` VARCHAR(39),
         `current_user_agent` VARCHAR(255),
         PRIMARY KEY (activity_ID),
         INDEX (activity_ID, user_ID, post_ID, term_ID, activity_type)
      ) $charset_collate ENGINE=InnoDB;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($log_query);

      if (empty($wpdb->last_error))
      {
         update_option('rlm_activity_log_table_created', current_time('mysql', true));
      }
   }

   public function delete_post($post_ID, $WP_Post)
   {
      $post = (array) $WP_Post;

      unset($post['comment_status']);
      unset($post['filter']);
      unset($post['ID']);
      unset($post['ping_status']);
      unset($post['pinged']);
      unset($post['post_author']);
      unset($post['post_content_filtered']);
      unset($post['post_content']);
      unset($post['post_date_gmt']);
      unset($post['post_excerpt']);
      unset($post['post_modified_gmt']);
      unset($post['post_status']);
      unset($post['to_ping']);

      rlm_add_activity_log('post_deleted', $post, $WP_Post->post_author, $post_ID);
   }

   public function delete_term($term_ID, $_tt_ID, $_taxonomy, $WP_Term)
   {
      $term = (array) $WP_Term;

      unset($term['term_group']);
      unset($term['filter']);

      rlm_add_activity_log('term_deleted', $term, 0, 0, [
         'term_ID' => $term_ID
      ]);
   }

   public function delete_user($user_ID, $reassign, $wp_user)
   {
      $user = (array) $wp_user->data;
      unset($user['ID']);
      unset($user['user_pass']);
      unset($user['user_url']);
      unset($user['user_status']);
      $user['reassign'] = $reassign;
      $user['roles'] = $wp_user->roles;

      rlm_add_activity_log('user_deleted', $user, $user_ID);
   }

   public function profile_update($user_ID, $wp_user, $userdata)
   {
      $old_userdata = (array) $wp_user->data;

      if ($userdata['user_pass'] !== $old_userdata['user_pass'])
      {
         $diff[] = 'user_pass';
      }

      if ($userdata['user_email'] !== $old_userdata['user_email'])
      {
         $diff[] = 'user_email';
      }

      if ($userdata['display_name'] !== $old_userdata['display_name'])
      {
         $diff[] = 'display_name';
      }

      if (is_array($wp_user->roles) && !empty($wp_user->roles))
      {
         if (empty($userdata['role']) || $userdata['role'] !== $wp_user->roles[0])
         {
            $diff[] = 'role';
         }
      }
      else if (!empty($userdata['role']))
      {
         $diff[] = 'role';
      }

      if (empty($diff))
      {
         return;
      }

      rlm_add_activity_log('user_updated', $diff, $user_ID);
   }

   public function user_register($user_ID, $userdata)
   {
      unset($userdata['user_url']);
      unset($userdata['locale']);
      unset($userdata['comment_shortcuts']);
      unset($userdata['use_ssl']);
      unset($userdata['user_pass']);

      rlm_add_activity_log('user_registered', $userdata, $user_ID);
   }

   public function wp_login_failed($username, $wp_error)
   {
      $details = [
         'username' => $username,
         'error'    => array_map('strip_tags', $wp_error->get_error_messages()),
      ];

      rlm_add_activity_log('user_login_failed', $details);
   }

   public function wp_login($_username, $wp_user)
   {
      $user = (array) $wp_user->data;
      unset($user['ID']);
      unset($user['user_pass']);
      unset($user['user_url']);
      unset($user['user_status']);
      unset($user['user_nicename']);
      unset($user['user_registered']);
      unset($user['user_activation_key']);

      $user['roles'] = $wp_user->roles;

      rlm_add_activity_log('user_login', $user, $wp_user->data->ID);
   }

   public function wp_logout($user_ID)
   {
      rlm_add_activity_log('user_logout', null, $user_ID);
   }
}

new RLM_Activity_Log_Register();

class RLM_Activity_Log
{
   public function add(string $type, mixed $value = null, int|WP_User $user = 0, int|WP_Post $post = 0, $others = [])
   {
      $this->checks_type($type);

      $i['activity_type'] = $type;
      $i['activity_value'] = maybe_serialize($value);

      if (is_a($user, 'WP_User'))
      {
         $i['user_ID'] = $user->ID;
      }
      elseif (is_numeric($user))
      {
         $i['user_ID'] = (int) $user;
      }
      else
      {
         throw new Exception('User inválido.');
      }

      if (is_a($post, 'WP_Post'))
      {
         $i['post_ID'] = $post->ID;
      }
      elseif (is_numeric($post))
      {
         $i['post_ID'] = (int) $post;
      }
      else
      {
         throw new Exception('Post inválido.');
      }

      $i['term_ID']            = $others['term_ID']           ?? null;
      $i['activity_time']      = $others['activity_time']     ?? current_time('mysql');
      $i['activity_time_gmt']  = $others['activity_time_gmt'] ?? current_time('mysql', true);
      $i['current_user_ID']    = $others['current_user_ID']   ?? get_current_user_id();
      $i['current_user_agent'] = $others['user_agent']        ?? $_SERVER['HTTP_USER_AGENT'] ?? null;
      $i['current_IP']         = $others['current_IP']        ?? $this->current_IP();

      $i = array_filter($i);

      if (empty($i))
      {
         return;
      }

      $check = apply_filters("add_{$type}_activity_log", null, $value, $user, $post, $others);

      if (null !== $check)
      {
         return (bool) $check;
      }

      global $wpdb;

      $table_name = $wpdb->prefix . 'rlm_activity_log';

      $success = $wpdb->insert($table_name, $i);

      if ($success === false)
      {
         throw new Exception('Item não inserido.');
      }

      do_action('rlm_activity_log_added', $type, $value, $user, $post, $others);

      return $i;
   }

   public function get(string $type, $query = [], $columns = [])
   {
      $type_a       = $this->checks_type($type);
      $columns      = $this->parse_columns($columns, $type_a['columns'] ?? []);
      $where        = $this->parse_where($type, $query);
      $column_value = $columns['array']['activity_value'];

      global $wpdb;
      $table_name = $wpdb->prefix . 'rlm_activity_log';
      $activities = $wpdb->get_results("SELECT {$columns['string']} FROM $table_name WHERE $where", ARRAY_A);

      if (empty($activities))
      {
         return [];
      }

      foreach ($activities as $activity)
      {
         $activity[$column_value] = maybe_unserialize($activity[$column_value]);
         $activities_r[] = $activity;
      }

      return $activities_r;
   }

   public function set(string $type, array $unique_query, mixed $value = null, int|WP_User $user = 0, int|WP_Post $post = 0, $others = [])
   {
      $this->checks_type($type);

      $existing_entry = $this->get($type, $unique_query);

      if (!empty($existing_entry && is_array($existing_entry)))
      {
         $deleted = $this->del($existing_entry[0]['ID']);

         if (false === $deleted)
         {
            return;
         }
      }

      return $this->add($type, $value, $user, $post, $others);
   }

   public function add_unique(string $type, array $unique_query, mixed $value = null, int|WP_User $user = 0, int|WP_Post $post = 0, $others = [])
   {
      $this->checks_type($type);

      $existing_entry = $this->get($type, $unique_query);

      if (!empty($existing_entry))
      {
         return;
      }

      return $this->add($type, $value, $user, $post, $others);
   }

   public function combine_spent_time_daily(int $user_ID, int $post_ID, int $spent_time = 0)
   {
      global $wpdb;

      $table_name = $wpdb->prefix . 'rlm_activity_log';

      $post = \get_post($post_ID);
      if (!$post)
      {
         throw new Exception('Post não encontrado.');
      }

      $post_type = $post->post_type;
      if ($post_type === 'page')
      {
         $post_type = $post->post_name;
      }

      $activity_type = "spent_time_{$post_type}";

      $current_date  = \current_time('Y-m-d');
      $activity_time = "$current_date 12:00:00";

      $updated = $wpdb->query(
         $wpdb->prepare(
            "UPDATE $table_name
            SET term_ID = term_ID + %d, activity_time_gmt = %s
            WHERE user_ID = %d
            AND post_ID = %d
            AND activity_type = %s
            AND DATE(activity_time) = %s",
            $spent_time,
            \current_time('mysql', true),
            $user_ID,
            $post_ID,
            $activity_type,
            $current_date
         )
      );

      if ($updated === 0)
      {
         $wpdb->insert($table_name, [
            'user_ID'            => $user_ID,
            'post_ID'            => $post_ID,
            'term_ID'            => $spent_time,
            'activity_type'      => $activity_type,
            'activity_value'     => \maybe_serialize([]),
            'activity_time'      => $activity_time,
            'activity_time_gmt'  => \current_time('mysql', true),
            'current_user_ID'    => \get_current_user_id(),
            'current_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'current_IP'         => $this->current_IP()
         ]);
      }
   }

   private function del(int $activity_ID)
   {
      global $wpdb;
      $table_name = $wpdb->prefix . 'rlm_activity_log';

      return $wpdb->query("DELETE FROM $table_name WHERE activity_ID = $activity_ID", ARRAY_A);
   }

   public function get_last(string $type, $query = [], $columns = [])
   {
      $type_a       = $this->checks_type($type);
      $columns      = $this->parse_columns($columns, $type_a['columns'] ?? []);
      $where        = $this->parse_where($type, $query);
      $column_value = $columns['array']['activity_value'];

      global $wpdb;
      $table_name = $wpdb->prefix . 'rlm_activity_log';

      $activity = $wpdb->get_row(
         $wpdb->prepare(
            "SELECT {$columns['string']} FROM $table_name WHERE $where ORDER BY activity_time DESC LIMIT 1"
         ),
         ARRAY_A
      );

      if (empty($activity))
      {
         return [];
      }

      $activity[$column_value] = \maybe_unserialize($activity[$column_value]);

      return $activity;
   }

   private function checks_type($type)
   {
      $types = rlm_get_activity_log_types();

      if (!in_array($type, array_keys($types)))
      {
         throw new Exception('Tipo não presente na lista.');
      }

      if (191 < strlen($type))
      {
         throw new Exception('Tipo inválido.');
      }

      return $types[$type];
   }

   private function current_IP()
   {
      return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
   }

   private function parse_columns($columns = [], $type_columns = [])
   {
      $default_columns = [
         'activity_ID'        => 'ID',
         'user_ID'            => 'user_ID',
         'post_ID'            => 'post_ID',
         'term_ID'            => 'term_ID',
         'activity_type'      => 'type',
         'activity_time'      => 'time',
         'activity_time_gmt'  => false,
         'activity_value'     => 'value',
         'current_user_ID'    => 'current_user',
         'current_IP'         => 'IP',
         'current_user_agent' => 'user_agent',
      ];

      $columns = wp_parse_args($columns, wp_parse_args($type_columns, $default_columns));

      foreach ($columns as $name => $rename)
      {
         if (empty($rename))
         {
            continue;
         }

         $columns_s[] = "`$name` AS `$rename`";
      }

      return [
         'array'  => $columns,
         'string' => implode(', ', $columns_s),
      ];
   }

   private function parse_where(string $type, $args = [])
   {
      $where[] = '`activity_type` = ' . '"' . $type . '"';

      foreach ($args as $filter)
      {
         $column  = $filter['key'] ?? null;
         $compare = $filter['compare'] ?? '=';

         if (empty($column) || empty($filter['value']))
         {
            continue;
         }

         switch (true)
         {
            case $compare === 'BETWEEN' && is_numeric($filter['value'][0]):
               $value = $filter['value'][0] . ' AND ' . $filter['value'][1];
               break;

            case $compare === 'BETWEEN':
               $value = '"' . $filter['value'][0] . '" AND "' . $filter['value'][1] . '"';
               break;

            case is_numeric($filter['value']):
               $value = $filter['value'];
               break;

            case is_array($filter['value']):
               $value = '("' . implode('","', $filter['value']) . '")';
               break;

            default:
               $value = '"' . $filter['value'] . '"';
               break;
         }

         $where[] = "`$column` $compare $value";
      }

      return implode(' AND ', $where);
   }
}

function rlm_get_activity_log_types()
{
   $default_types = [
      'post_deleted'      => [
         'default' => ['hook' => 'delete_post', 'args' => 2],
         'columns' => ['user_ID' => 'post_author', 'activity_value' => 'WP_Post'],
      ],
      'term_deleted'      => [
         'default' => ['hook' => 'delete_term', 'args' => 4],
         'columns' => ['activity_value' => 'WP_Term'],
      ],
      'user_deleted'      => [
         'default' => ['hook' => 'delete_user', 'args' => 3],
         'columns' => ['activity_value' => 'WP_User'],
      ],
      'user_login_failed' => [
         'default' => ['hook' => 'wp_login_failed', 'args' => 2],
         'columns' => ['activity_value' => 'details'],
      ],
      'user_login'        => [
         'default' => ['hook' => 'wp_login', 'args' => 2],
         'columns' => ['activity_value' => 'WP_User'],
      ],
      'user_logout'       => [
         'default' => ['hook' => 'wp_logout'],
      ],
      'user_updated'      => [
         'default' => ['hook' => 'profile_update', 'args' => 3],
         'columns' => ['activity_value' => 'changes'],
      ],
      'user_registered'   => [
         'default' => ['hook' => 'user_register', 'args' => 2],
         'columns' => ['activity_value' => 'userdata'],
      ],
   ];

   return apply_filters('rlm_activity_log_types', $default_types);
}

add_action('rlm_add_activity_log', 'rlm_add_activity_log', 10, 5);
function rlm_add_activity_log(string $type, mixed $value = null, int|WP_User $user = 0, int|WP_Post $post = 0, $others = [])
{
   $Activity_Log = new RLM_Activity_Log();
   return $Activity_Log->add($type, $value, $user, $post, $others);
}

add_action('rlm_add_unique_activity_log', 'rlm_add_unique_activity_log', 10, 6);
function rlm_add_unique_activity_log(string $type, array $unique_query, mixed $value = null, int|WP_User $user = 0, int|WP_Post $post = 0, $others = [])
{
   $Activity_Log = new RLM_Activity_Log();
   return $Activity_Log->add_unique($type, $unique_query, $value, $user, $post, $others);
}

add_action('rlm_combine_spent_time_daily', 'rlm_combine_spent_time_daily', 10, 3);
function rlm_combine_spent_time_daily($user_ID, $post_ID, $spent_time = 0)
{
   $Activity_Log = new RLM_Activity_Log();
   $Activity_Log->combine_spent_time_daily($user_ID, $post_ID, $spent_time);
}
