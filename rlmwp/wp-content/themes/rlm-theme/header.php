<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
   <meta charset="<?php bloginfo('charset'); ?>">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="profile" href="https://gmpg.org/xfn/11">
   <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
   <header class="py-10 bg-blue-700 text-white">
      <div class="container">
         <?php

         if (has_custom_logo())
         {
            the_custom_logo();
         }
         else
         {

         ?>

            <a class="uppercase text-2xl font-bold" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
               <?php bloginfo('name'); ?>
            </a>
            <p>
               <?php bloginfo('description'); ?>
            </p>

         <?php

         }

         ?>
      </div>
   </header>
