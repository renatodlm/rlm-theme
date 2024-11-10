<?php

/**
 * Template for displaying single posts
 *
 * @package rlm_theme
 */

get_header();

?>

<main id="main-content">
   <?php

   while (have_posts())
   {
      the_post();
      the_title('<h1>', '</h1>');
      the_content();
   }

   ?>
</main>

<?php

get_footer();

?>
