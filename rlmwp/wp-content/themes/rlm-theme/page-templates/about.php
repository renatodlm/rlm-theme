<?php

/**
 * Template Name: About
 *
 * The template for displaying the About page.
 *
 * @package rlm_theme
 */

get_header();

?>

<main id="main-content">
   <section class="about-content">
      <h1><?php the_title(); ?></h1>
      <div class="content">
         <?php

         while (have_posts()) :
            the_post();
            the_content();
         endwhile;

         ?>
      </div>
   </section>
</main>

<?php

get_footer();

?>
