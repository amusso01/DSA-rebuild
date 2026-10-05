<?php
/**
 * Template Name: Second Full Page
 * 
 *
 *
 * @author Jonathan Soto
 * @package foundry
 */ 

get_header(); ?>

<?php  get_template_part( 'template-parts/banner/content', 'banner-page' ); ?>

<section id="primary" class="content-area full-second-area grey-section" > 
    <main id="main" class="site-main" role="main">
        <div class="container second">
            <?php
                        while ( have_posts() ) : the_post();
                            the_content();
                        endwhile; 
            ?>
        </div>
    </main>
</section>

<section style="margin-bottom:50px;">
    <div class="grid-gallery style-gallery">
        <?php
        if( have_rows('grid') ):
            while( have_rows('grid') ) : the_row();
                echo '<div id="item-'.get_row_index().'" >';

                    if( get_sub_field('background')){
                        echo '<div class="item-box-info background">
                                    <img src="'.get_sub_field('background').'" />
                            </div>';
                    }else{ 
                        $class="";
                        $style="";
                        if(get_sub_field('color')){
                            $class= 'color';
                            $style="background-color: ". get_sub_field('color');
                        }
                        
                        echo '<div class="item-box-info '.$class.'" style="'. $style.'"> 
                                    <img src="'.get_sub_field('icon').'" />
                                    <h3>'.get_sub_field('title').'</h3>
                                    <p>'.get_sub_field('description').'</p>
                            </div>';
                    }
                echo '</div>';
            endwhile;
        endif; 
        ?>
    </div>
</section>
 
<section class="white-section full-second-area grey-section">
    <div class="container second">
        <?php 
            if( get_field('second_content') ){
                the_field('second_content'); 
            }
            if ( has_post_thumbnail() ) {
                $attachment_image = wp_get_attachment_url( get_post_thumbnail_id() );
                echo '<br><img src="' . esc_attr( $attachment_image ) . '" alt="'.get_the_title().'">'; 	
             } 
        ?>
    </div>
</section>




<?php if( get_field('contact_information') ){ ?> 
    <section class="white-section no-padding-top" >
        <div class="container">
            <a href="<?php echo get_the_permalink(16); ?>" class="btn-blue"> 
                <h5><?php echo get_field('contact_information'); ?></h5>
            </a>
        </div>
    </section>
<?php } ?>


<?php get_footer(); ?>
