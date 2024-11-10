<?php

if (!defined('ABSPATH'))
{
   exit;
}

class RequiredPages
{
   protected $pages_to_create;
   protected $pages_to_protect;

   public function __construct($pages_to_create)
   {
      $this->pages_to_create = $pages_to_create;

      if ($this->is_valid_pages())
      {

         $this->pages_to_protect = array_map(function ($page)
         {
            return $page['slug'];
         }, $this->pages_to_create);

         foreach ($this->pages_to_create as $page)
         {

            $this->create_page_if_not_exists($page);

            if (!empty($page['parent']))
            {
               $this->set_page_ascending($page['parent'], $page['slug']);
            }
         }

         // TODO: Habilitar depois de ajustar as páginas no painel admin
         // \add_action('before_delete_post', [$this, 'prevent_page_deletion']);
         // \add_action('wp_trash_post', [$this, 'prevent_page_deletion']);
         \add_filter('page_row_actions', [$this, 'remove_delete_action_for_specific_pages'], 10, 2);
      }
   }

   private function page_exists_by_slug($slug)
   {
      return \get_page_by_path($slug);
   }

   private function create_page_if_not_exists($page)
   {
      $title = !empty($page['title']) ? $page['title'] : $page['slug'];
      $slug  = !empty($page['parent']) ? $page['parent'] . '/' . $page['slug'] : $page['slug'];

      if (!$this->page_exists_by_slug($slug))
      {
         \wp_insert_post([
            'post_title'  => $title,
            'post_name'   => $page['slug'],
            'post_status' => 'publish',
            'post_type'   => 'page'
         ]);
      }
   }

   private function set_page_ascending($parent_slug, $child_slug)
   {
      $slug = $parent_slug . '/' . $child_slug;

      if (!$this->page_exists_by_slug($slug))
      {

         if (!empty($parent_slug) || !empty($child_slug))
         {
            $parent_page = \get_page_by_path($parent_slug);
            $child_page  = \get_page_by_path($child_slug);

            if ($child_page->post_parent === 0)
            {

               if ($parent_page && $child_page)
               {
                  \wp_update_post([
                     'ID'          => $child_page->ID,
                     'post_parent' => $parent_page->ID
                  ]);
               }
            }
         }
      }
   }

   public function prevent_page_deletion($post_ID)
   {
      $page = \get_post($post_ID);

      if (in_array($page->post_name, $this->pages_to_protect))
      {
         \wp_die(esc_html__('Desculpe, esta página não pode ser excluída.', 'rlm_theme'));
      }
   }

   public function remove_delete_action_for_specific_pages($actions, $post)
   {
      if (in_array($post->post_name, $this->pages_to_protect))
      {
         unset($actions['delete']);
         unset($actions['trash']);
      }

      return $actions;
   }

   private function is_valid_pages()
   {
      if (empty($this->pages_to_create) || !is_array($this->pages_to_create))
      {
         return false;
      }

      foreach ($this->pages_to_create as $page)
      {
         if (empty($page['slug']))
         {
            return false;
         }
      }

      return true;
   }
}

/**
 * Add pages
 */
new RequiredPages([
   // [
   //    'title' => 'Example',
   //    'slug'  => 'example'
   // ]
]);
