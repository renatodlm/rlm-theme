<footer class="py-10 bg-gray-500 text-white">
   <div class="container">
      <p class="m-0">
         <?php

         bloginfo('name');
         echo ' &copy; ' . date('Y');

         ?>
      </p>
   </div>
</footer>
<?php

wp_footer();

?>
</body>

</html>
