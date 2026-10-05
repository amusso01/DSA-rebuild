<section class="page-banner">
    <div class="filter-opacity">
        
        
        <div class="container">
            <svg class="dsa-whatermark" xmlns="http://www.w3.org/2000/svg" width="142.485" height="57.082" viewBox="0 0 142.485 57.082">
                <g id="dsa-logo" transform="translate(294.392 -13.182)" opacity="0.5">
                    <g id="Group_161" data-name="Group 161" transform="translate(-294.392 13.182)">
                    <rect id="Rectangle_80" data-name="Rectangle 80" width="14.265" height="14.265" transform="translate(13.77 14.267)" fill="#fff"/>
                    <rect id="Rectangle_81" data-name="Rectangle 81" width="14.265" height="14.265" transform="translate(0 42.797)" fill="#fff"/>
                    <g id="Group_160" data-name="Group 160" transform="translate(42.625 0)">
                        <path id="Path_117" data-name="Path 117" d="M135.445,0h-26.98V14.266H94.2V28.531h14.265V42.815H94.2V57.079h41.245a1.476,1.476,0,0,0,1.55-1.55V1.552A1.476,1.476,0,0,0,135.445,0Z" transform="translate(-37.137 0.001)" fill="#fff"/>
                        <rect id="Rectangle_82" data-name="Rectangle 82" width="14.265" height="14.265" transform="translate(28.53 28.532)" fill="#fff"/>
                        <rect id="Rectangle_83" data-name="Rectangle 83" width="14.265" height="14.265" transform="translate(42.798 0.002)" fill="#fff"/>
                        <rect id="Rectangle_84" data-name="Rectangle 84" width="14.265" height="14.265" transform="translate(0 42.797)" fill="#fff"/>
                        <rect id="Rectangle_85" data-name="Rectangle 85" width="14.265" height="14.265" transform="translate(14.265 14.267)" fill="#fff"/>
                    </g>
                    </g>
                </g>
            </svg>
            <div class="title-page">
                <h1>
                    <?php 
                        if( get_field('subtitle')){
                            echo '<span>'.get_field('subtitle').'</span> ';
                        }
 
                        echo get_the_title();
                    ?>
                </h1>
                <!--<p><?php echo get_field('short_description'); ?></p>-->
            </div>
            
                <?php
                    if( get_field('banner_description')){
                        echo '<div class="banner-content">';
                        the_field('banner_description');
                        echo '</div>';
                    }
                ?>
            <?php 
                if( get_the_ID() == '15'){
                    echo '<br><br>'; 
                    echo do_shortcode('[ajax_load_more loading_style="white" post_type="post" posts_per_page="6"]');
                }else if(get_the_ID() == '16'){
                    echo do_shortcode('[my_contact]'); 
                }else if( get_the_ID() == '1766' ){
                    echo do_shortcode('[data_destruction]');
                }
            ?>
        </div>
    </div>
   
</section>
