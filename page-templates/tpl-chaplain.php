<?php
/**
 * Template Name: Chaplain Template
 *
 * @link https://developer.wordpress.org/themes/template-files-section/page-template-files/
 *
 * @package awps
 */

get_header(); ?>

<div class="container">

	<div class="row">

		<div class="col-sm-12">

			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">

					<?php
					/* Start the Loop */
					while ( have_posts() ) :
						the_post();

						get_template_part( 'views/content', 'page' );

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

          endwhile;
          
          echo "Codea aquí!";

					?>

				</main><!-- #main -->
			</div><!-- #primary -->

		</div><!-- .col- -->

		<!-- <div class="col-sm-4"> -->
			<!-- <?php // get_sidebar(); ?> -->
		<!-- </div> --><!-- .col- -->

	</div><!-- .row -->

</div><!-- .container -->

<?php
get_footer();
