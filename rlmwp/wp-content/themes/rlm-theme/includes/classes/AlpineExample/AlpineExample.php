<?php

namespace RLM_Theme\AlpineExample;

if (!defined('ABSPATH'))
{
   exit;
}

class AlpineExample
{
   protected $title;
   protected $button_text;
   protected $content;

   function __construct()
   {
      $this->title       = esc_html__('AlpineJS Example:', 'rlm_theme');
      $this->button_text = esc_html__('Click to toggle content', 'rlm_theme');
      $this->content = esc_html__('Content...', 'rlm_theme');
   }

   public function render()
   {

?>
      <h3 class="text-lg mt-4 mb-2">
         <?php echo $this->title; ?>
      </h3>
      <div x-data="{ open: false }" class="text-gray-500">
         <button class="btn btn-primary" @click="open = ! open">
            <?php echo $this->button_text; ?>
         </button>

         <div x-show="open">
            <?php echo $this->content; ?>
         </div>
      </div>
<?php

   }
}
