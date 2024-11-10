<?php

/**
 * Template Name: Home
 *
 * The template for displaying the homepage.
 *
 * @package rlm_theme
 */

get_header();

?>

<main id="main-content">
   <section class="hero">
      <h1><?php esc_html_e('Welcome to Our Homepage', 'rlm_theme'); ?></h1>
      <p><?php esc_html_e('This is the homepage of the RLM Theme.', 'rlm_theme'); ?></p>
   </section>

   <section class="recent-posts">
      <h2><?php esc_html_e('Recent Posts', 'rlm_theme'); ?></h2>
      <?php

      $recent_posts = new WP_Query(array(
         'posts_per_page' => 5,
      ));

      if ($recent_posts->have_posts()) :
         while ($recent_posts->have_posts()) :
            $recent_posts->the_post();

      ?>
            <article>
               <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
               <p><?php the_excerpt(); ?></p>
            </article>
         <?php

         endwhile;
         wp_reset_postdata();

      else :

         ?>
         <p><?php esc_html_e('No recent posts available.', 'rlm_theme'); ?></p>
      <?php

      endif;

      ?>
   </section>
</main>

<?php

get_footer();

?>
