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

<main id="main-content" class="py-10">
   <section class="about-content">
      <div class="container">
         <h1><?php the_title(); ?></h1>
         <div class="content">
            <?php

            while (have_posts()) :
               the_post();
               the_content();
            endwhile;

            ?>
         </div>
      </div>
   </section>
</main>

<?php

get_footer();

?>
