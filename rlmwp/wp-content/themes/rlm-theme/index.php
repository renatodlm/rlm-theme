<?php

/**
 * Main template file
 *
 * @package rlm_theme
 */

use RLM_Theme\AlpineExample\AlpineExample;

get_header();

?>

<main id="main-content" class="py-10">
   <div class="container">
      <h1 class="text-lg mb-2 text-black">
         <?php esc_html_e('Welcome to RLM Theme for Devs!', 'rlm_theme'); ?>
      </h1>
      <p class="text-base text-gray-300">
         <?php esc_html_e('This is a minimal WordPress theme template.', 'rlm_theme'); ?>
      </p>

      <?php

      $AlpineExample = new AlpineExample();
      $AlpineExample->render();

      ?>
   </div>
</main>

<?php

get_footer();

?>
