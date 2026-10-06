<?php

/**
 * The template for displaying all single posts and attachments
 *
 *
 * @author Jonathan Soto
 * @package foundry
 */

get_header(); ?>


<section id="primary" class="content-area page-area">
    <main id="main" class="site-main container second" role="main">


        <h1><?php the_title() ?></h1>

        <?php
        while (have_posts()) : the_post();
            the_content();
        endwhile;
        ?>
    </main>
</section>



<?php get_footer(); ?>