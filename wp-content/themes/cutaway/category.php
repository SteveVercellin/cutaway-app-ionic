<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package larion
 */

get_header(); ?>

<div  id="wrapper-main" class="l-main">

	<div id="content" class="content">

			<main class="site-main" id="main"> <?php 
				if (have_posts()) :
                        /* Start the Loop */
	                while (have_posts()) : the_post(); ?>
	                	<article class="post-<?php the_ID();?> article-item">
	                		<h3 class="article-title"><?php the_title(); ?></h3>
	                		<div class="article-thumbnail"><img src="<?php the_post_thumbnail(); ?>" class="thumbnail" /></div>
	                		<div class="article-content"><?php the_content(); ?></div>
	                	</article>
	                <?php    

	                endwhile;
                else :
                    get_template_part('loop-templates/content', 'none');
                endif; ?>

			</main><!-- #main -->

	</div><!-- Content end -->

</div><!-- Wrapper main end -->

<?php
get_footer();
?>