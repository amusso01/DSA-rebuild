<?php
/**
 * Template Name: Full Page
 * 
 *
 *
 * @author Jonathan Soto
 * @package foundry
 */ 
$columns = get_field('gallery_columns');
get_header(); ?>

<?php  get_template_part( 'template-parts/banner/content', 'banner-page' ); ?>

<section id="primary" class="content-area full-area"> 
    <main id="main" class="site-main" role="main">
        <?php 
            if( get_field('introduction') ){
                echo '<h3 class="introduction">'.get_field('introduction').'</h3>';
            }
        ?>
        <div class="row gx-0">
 
            <div class="col-lg-6 col-12 gallery-section" style="position:relative">

                

                <div class="gallery-box <?php echo $columns; ?>">
                    <?php 
                        if ( has_post_thumbnail() ) {
                            $attachment_image = wp_get_attachment_url( get_post_thumbnail_id() );
                            echo '<img src="' . esc_attr( $attachment_image ) . '" alt="'.get_the_title().'">'; 	
                        } 
                    ?>
                    <?php 
                        $images = get_field('gallery_page');
                        $size = 'full'; // (thumbnail, medium, large, full or custom size)
                        
                        if( $images ): 
                            foreach( $images as $image_id ): 
                                echo wp_get_attachment_image( $image_id, $size ); 
                            endforeach;
                        endif; 
                    ?>

                </div>
            </div>
            <div class="col-lg-6 col-12 information  "  style="position:relative">
                <div class="container sticky-box" >
                    <?php
                        while ( have_posts() ) : the_post();
                            the_content();
                        endwhile; 
                    ?>
                     
                    <?php
                        if( have_rows('logos') ):
                            echo '<div class="part-logos">';
                            while( have_rows('logos') ) : the_row();
                    
                                echo '<img alt="environment-agency-logo" src="'.get_sub_field('logo').'">';
                                
                            endwhile;
                            
                            echo '</div>';
                        endif; 
                    ?>
                </div>
            </div>
        </div>
        <?php if( get_field('second_column_1')){ ?>
            <div class="container second-section">
                <div class="row gx-5">
                    <div class="col-lg-6 col-12">
                        <div class="first">
                             <?php the_field('second_column_1'); ?>
                         </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <?php the_field('second_column_2'); ?>

                    </div>
                </div>
            </div>
        <?php } ?>
    </main>
</section>


<?php  
    if( get_field('active_slider') ){ 
        get_template_part( 'template-parts/content', 'slider-page' );
    } 
?>

<?php if( get_field('contact_information') ){ ?> 
    <section class="white-section">
        <div class="container">
            <a href="<?php echo get_the_permalink(16); ?>" class="btn-blue"> 
                <h5>
                    <?php echo get_field('contact_information'); ?>
                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27.06 25.27">
                        <defs>
                            <style>
                            .cls-1 {
                                fill: #051033;
                                stroke: #f7f7f7;
                                stroke-linejoin: round;
                                stroke-width: 2px;
                            }
                            </style>
                        </defs>
                        <path id="Union_8" data-name="Union 8" class="cls-1" d="m26.06,1h0L1,24.27,26.06,1Zm0,0h0Z"/>
                        <path id="Union_8-2" data-name="Union 8" class="cls-1" d="m26.06,1h0v18.41V1Zm0,0h0Z"/>
                        <path id="Union_8-3" data-name="Union 8" class="cls-1" d="m26.06,1H7.66h18.41Zm0,0h0Z"/>
                        </svg>
                
                </h5>
                
                
            </a>
        </div>
    </section>
<?php } ?>


<?php get_footer(); ?>
