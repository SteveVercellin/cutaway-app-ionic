<?php
/**
 * Single post partial template.
 *
 * @package understrap
 */

?>
<img style="" src="<?php echo get_template_directory_uri() . '/img/BG_02.svg' ?>" alt="" class="article-news-bg">
<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php echo get_the_post_thumbnail($post->ID, '960_350', array('class' => 'post_image')); ?>
	<header class="entry-header">
		<div class="entry-meta">

			<?php echo get_the_date(); ?>

		</div><!-- .entry-meta -->
		<h1 class="entry-title"><?php echo format_text(get_the_title()) ?></h1>

    <div class="entry-social my-4">
      <a class="facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= home_url( $wp->request ) ?>"><i class="fab fa-facebook-f"></i></a>
      <a class="linkedin" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?= home_url( $wp->request ) ?>"><i class="fab fa-linkedin-in"></i></a>
      <a class="twitter" target="_blank" href="https://twitter.com/home?status=<?= home_url( $wp->request ) ?>"><i class="fab fa-twitter"></i></a>
    </div>

	</header><!-- .entry-header -->
	<div class="entry-content">

		<?php the_content(); ?>

	</div><!-- .entry-content -->


</article><!-- #post-## -->
