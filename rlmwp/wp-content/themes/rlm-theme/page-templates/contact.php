<?php

/**
 * Template Name: Contact
 *
 * The template for displaying the Contact page.
 *
 * @package rlm_theme
 */

get_header();

?>

<main id="main-content">
   <section class="contact-form">
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
