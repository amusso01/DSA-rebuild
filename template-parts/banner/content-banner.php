<div class="video-modal">
    <div class="container">
            <a href="javascript:void(0)" class="play-video close-modal">X</a>
            <div class="container-video"> 
                <iframe class="responsive-iframe" src="<?php echo get_field('video', 'options'); ?>&title=0&byline=0&portrait=0" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
               
            </div>
    </div>
</div>
<section class="principal-banner">
    <div class="filter-opacity">
            <div class="opacity-content">
                    <img src="<?php echo get_field('inactive_imagen', 'options'); ?>" alt="opacity image" class="bg-image">
                    <?php echo the_field('inactive_content', 'options'); ?>
                    <a href="javascript:void(0)" class="play-video">
                        <svg xmlns="http://www.w3.org/2000/svg" width="77" height="77" viewBox="0 0 77 77">
                            <g id="Group_166" data-name="Group 166" transform="translate(-929 -502)">
                                <path id="Polygon_1" data-name="Polygon 1" d="M9.255,0,18.51,18.51H0Z" transform="translate(979.346 531.245) rotate(90)" fill="#f7f7f7"/>
                                <g id="Ellipse_3" data-name="Ellipse 3" transform="translate(929 502)" fill="none" stroke="#f7f7f7" stroke-width="3">
                                <circle cx="38.5" cy="38.5" r="38.5" stroke="none"/>
                                <circle cx="38.5" cy="38.5" r="37" fill="none"/>
                                </g>
                            </g>
                        </svg>
                    </a>
            </div>
       
    </div>
   
</section>
<section class="grey-section banner-p">
    <div class="container"> 
            <h2 class="text-align-center"> DSA Connect </h2> 
            <div class="row gx-5">
                <?php
                    if( have_rows('information', 'options') ):
                        while( have_rows('information', 'options') ) : the_row();
                            echo '<div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-item">
                                            <div class="title">
                                                <img src="'.get_sub_field('icon').'" alt="'.get_sub_field('title').'" />
                                                <h5>'.get_sub_field('title').'</h5>
                                            </div>
                                            <p>'.get_sub_field('description').'</p>
                                        </div>
                                </div>';
                        endwhile;
                    endif;
                ?>
            </div>
        </div>
</section>
