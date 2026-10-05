<?php
/**
 * Template Name: Sectors page
 * 
 *
 *
 * @author Camilo Suarez
 * @package foundry
 */ 

get_header(); ?>

<style type="text/css">
    .sectorspage .page-banner .filter-opacity .title-page{max-width: 1057px;}
    .sectorspage .page-banner .filter-opacity .banner-content {max-width: 100%;text-align: center; margin-top: 48px;}
    .sectorspage .btn{display: inline-block;background-color: #051033;color: #fff !important;border-radius: 8px;padding: 16px 52px;position: relative;width: 100%; font-size: 24px; max-width: 270px; font-weight: 600; margin-top: 18px;}
    .sectorspage .btn:hover{background-color: #0fc6bf;}

    .infopage .container{max-width: 787px!important; padding: 70px 0 50px;}

    .contentbox{padding: 68px 120px;}
    .greybox .contentbox{background-color: #F4F6FC;}
    .blackbox .contentbox{background-color: #212529;}

    .greybox .contentbox h2{color: #10C6BF;}
    .greybox .contentbox p{color: #000000;}

    .blackbox .contentbox h2{color: #10C6BF;}
    .blackbox .contentbox p{color: #FFFFFF;}

    .twocols img{height: 100%; min-height: 512px; object-fit: cover;width: 105%;max-width: 105%;position: relative;}
    .twocols .container{max-width: 100%!important;}

    .twocolsbox{margin-bottom: 70px;}

    .sectorspage section.white-section{padding: 0 0 70px;}

    .allbtns{width: 95%; margin: 0 auto; text-align: center; padding-bottom: 35px;}
    .allbtns .btn{padding: 16px 20px; max-width: max-content; font-size: 24px; margin: 0 25px 20px;}

    .page-template-page-sectors .white-section.partner{display: none!important;}

    .explore h2{color: #071031; font-size: 40px; margin-bottom: 70px;}

    @media(max-width:  991px){
        .twocolsbox {margin-bottom: 40px;}
        .twocols img {height: 100%;min-height: 512px;object-fit: cover;width: 100%;max-width: 100%;position: relative;}
        .contentbox {padding: 5%;}
        .allbtns{width: 100%;}

        .explore h2 {margin-bottom: 40px;}
    }
    @media(max-width:  650px){
        .allbtns {width: 100%;margin: 0 auto;text-align: center;padding-bottom: 35px;}
        .allbtns .btn {padding: 16px 0px;max-width: 100%;font-size: 24px;margin: 0 auto 20px;display: block;}
    }
</style>

<?php 
    $parent_id = wp_get_post_parent_id(get_the_ID()); 
    if ( $parent_id !== 0 ) {
        
    }else{
        $parent_id = get_the_ID();
    }
?>

<section class="sectorspage">
    <div class="page-banner">
        <div class="filter-opacity">
            <div class="container">
                <div class="title-page">
                    <h1><?php echo get_the_title(); ?></h1>
                </div>
                <div class="banner-content">
                    <?php echo get_field('banner_information'); ?>
                    <a href="<?php echo get_the_permalink(16); ?>" class="btn">GET IN TOUCH</a>
                </div>
            </div>
        </div>
    </div>

    <div class="infopage">
        <div class="container">
            <?php echo get_the_content(); ?>
        </div>
    </div>

    <div class="twocols">
        <div class="container">
            <?php
                if( have_rows('two_columns', $parent_id) ):
                    while( have_rows('two_columns', $parent_id) ) : the_row(); ?>

                        <?php $boxstyle = get_sub_field('style_box'); ?>
                        <?php if ( $boxstyle == "Grey box" ) { ?>
                            <div class="row gx-0 twocolsbox greybox">
                                <div class="col-lg-6 col-12">
                                    <img src="<?php echo get_sub_field('image'); ?>">
                                </div>
                                <div class="col-lg-6 col-12 contentbox">
                                    <?php echo get_sub_field('content'); ?>
                                    <div class="style-btn second"><a href="<?php echo get_sub_field('url_button'); ?>" data-type="link" data-id="<?php echo get_sub_field('url_button'); ?>">FIND OUT MORE</a></div>
                                </div>
                            </div>
                        <?php }elseif ( $boxstyle == "Black box" ) { ?>
                            <div class="row gx-0 twocolsbox blackbox">
                                <div class="col-lg-6 col-12 contentbox">
                                    <?php echo get_sub_field('content'); ?>
                                    <div class="style-btn second"><a href="<?php echo get_sub_field('url_button'); ?>" data-type="link" data-id="<?php echo get_sub_field('url_button'); ?>">FIND OUT MORE</a></div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <img src="<?php echo get_sub_field('image'); ?>">
                                </div>
                            </div>
                        <?php } ?>
            <?php
                    endwhile;
                endif;
            ?>
        </div>
    </div>

    <section class="white-section">
        <div class="container">
            <a href="<?php echo get_the_permalink(16); ?>" class="btn-blue"> 
                <h5>
                    
                    <?php echo get_field('label_button', $parent_id); ?>
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
                        <path id="Union_8" data-name="Union 8" class="cls-1" d="m26.06,1h0L1,24.27,26.06,1Zm0,0h0Z"></path>
                        <path id="Union_8-2" data-name="Union 8" class="cls-1" d="m26.06,1h0v18.41V1Zm0,0h0Z"></path>
                        <path id="Union_8-3" data-name="Union 8" class="cls-1" d="m26.06,1H7.66h18.41Zm0,0h0Z"></path>
                        </svg>
                
                </h5>
                
                
            </a>
        </div>
    </section>

    <div class="explore">
        <div class="container">
            <h2>Explore our other sectors</h2>

            <div class="allbtns">
                <?php
                    if( have_rows('other_sectors', $parent_id) ):
                        while( have_rows('other_sectors' , $parent_id) ) : the_row(); ?>


                        <a href="<?php echo get_the_permalink(get_sub_field('sector')); ?>" class="btn"><?php echo get_sub_field('button_label'); ?></a>

                <?php
                        endwhile;
                    endif;
                ?>
            </div>
        </div>
    </div>
</section>




<?php get_footer(); ?>
