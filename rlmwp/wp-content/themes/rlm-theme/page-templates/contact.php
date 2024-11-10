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

<main id="main-content" class="py-10">
   <section class="contact-form">
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
