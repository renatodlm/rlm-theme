<?php

/**
 * Template for displaying search forms in a WordPress theme.
 *
 * This template is used when calling the get_search_form() function.
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
   <label for="search-form-input">
      <span class="screen-reader-text"><?php echo _x('Search for:', 'label'); ?></span>
   </label>
   <input type="search" id="search-form-input" class="search-field"
      placeholder="<?php echo esc_attr_x('Search &hellip;', 'placeholder'); ?>"
      value="<?php echo get_search_query(); ?>" name="s" />
   <button type="submit" class="search-submit"><?php echo esc_attr_x('Search', 'submit button'); ?></button>
</form>
