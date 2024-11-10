<?php

/**
 * Main template file
 *
 * @package rlm_theme
 */

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

      <h3 class="text-lg mt-4 mb-2">
         <?php esc_html_e('AlpineJS Example:', 'rlm_theme'); ?>
      </h3>
      <div x-data="{ open: false }" class="text-gray-500">
         <button class="btn btn-primary" @click="open = ! open">Click to toggle content</button>

         <div x-show="open">
            Content...
         </div>
      </div>
   </div>
</main>

<?php

get_footer();

?>
