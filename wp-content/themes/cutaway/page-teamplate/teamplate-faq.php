<?php
/*
 * Template name: FAQ Page
 */

get_header(); ?>

<div  id="wrapper-main" class="l-main">

	<div id="content" class="content">

			<main class="site-main" id="main"> 
                <?php the_post(); ?>
                <div class="top-content">
                    <div class="wrap-page-title">
                        <a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/registrati-icon.png"></a>
                        <h1 class="page-title"><?php the_title(); ?></h1>
                    </div>
                </div>

                <div class="wrapper-faq-content"> <?php                    
                    $faq_list = get_field('faq_for_barber');
                    $i = 1;

                    $class = '';
                    $accordions = [];

                    foreach ($faq_list as $faq) {
                        $accordions[] = [ 'header' => $faq['question'], 'content' => $faq['answer'] ];
                    }
                    // var_dump($accordions);
                    $params = [
                        'class' => $class,
                        'accordions' => $accordions,
                    ];
                    echo cutaway_accordion($params); ?>

                    <div class="email-and-phone">
                        <?php echo get_the_content(); ?>
                    </div>
                </div>
			</main><!-- #main -->

	</div><!-- Content end -->

</div><!-- Wrapper main end -->

<?php
get_footer();
?>