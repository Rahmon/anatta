<?php
/*
Template Name: Simple Page
Template Post Type: page
*/
get_header( 'simple-page' );
?>

    <main id="main" class="site-main" role="main">
      <article>
        <?php
          if ( have_posts() ) :
          	while ( have_posts() ) : the_post();
          		the_content();
          	endwhile;
          endif;
        ?>
      </article>
    </main>
  </div><!-- #content -->
  <footer></footer>

<?php wp_footer() ?>
</body>
</html>
