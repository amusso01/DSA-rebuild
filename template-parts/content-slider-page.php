<!--offer section area-->
<section class="offer-area section">
        <div class="offer-tabs tab-content">
            <div class="offerslide tab-pane fade active show" id="SOLUTIONS">
                <?php
                    $class= "no-img";
                    $back = "";
                    if(get_field('reporting_image')){
                        $back = 'background-image: url('.get_field('reporting_image').');';
                        $class = "has-img";
                    }

                    $bottom =  "";
                    $link = get_field('reporting_image_buttom');
                    if( $link ): 
                        $link_url = $link['url'];
                        $link_title = $link['title'];
                        $link_target = $link['target'] ? $link['target'] : '_self';
                        $bottom = '<a class="read-more" href="'.esc_url( $link_url ).'" target="'.esc_attr( $link_target ) .'">'.esc_html( $link_title ) .'</a>';
                   endif;
                ?>
                <div id="row-1" class="offerItem <?php echo $class; ?>"  style="<?php echo $back; ?>" >   
                    <h4 class="offerItemTitle">Reporting</h4>
                    <div class="offer-detail">
                        <div class="offer-content">
                            <?php the_field('reporting'); ?>
                            <?php echo $bottom; ?>
                            <div class="arrow next" att-step="row-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="133.5" height="38" viewBox="0 0 133.5 38">
                                <g id="Group_39" data-name="Group 39" transform="translate(0.5 0.5)">
                                    <path id="Union_3" data-name="Union 3" d="M20,18.5,0,0ZM0,37,20,18.5Z" transform="translate(20 37) rotate(180)" stroke="#f7f7f7" stroke-linejoin="round" stroke-width="1"/>
                                    <line id="Line_13" data-name="Line 13" x2="131" transform="translate(1.5 18.5)" fill="none" stroke="#f7f7f7" stroke-linecap="round" stroke-width="1"/>
                                </g>
                                </svg>
                                <span></span>
                            </div> 
                        </div>
                    </div>
                </div>
                <?php
                    $class= "no-img";
                    $back = "";
                    if(get_field('recycle_image')){
                        $back = 'background-image: url('.get_field('recycle_image').');';
                        $class = "has-img";
                    }

                    $bottom =  "";
                    $link = get_field('recycle_image_buttom');
                    if( $link ): 
                        $link_url = $link['url'];
                        $link_title = $link['title'];
                        $link_target = $link['target'] ? $link['target'] : '_self';
                        $bottom = '<a class="read-more" href="'.esc_url( $link_url ).'" target="'.esc_attr( $link_target ) .'">'.esc_html( $link_title ) .'</a>';
                   endif;
                ?>
                <div id="row-2" class="offerItem  <?php echo $class; ?>"  style="<?php echo $back; ?>">
                    <h4 class="offerItemTitle">Recycle</h4>
                    <div class="offer-detail">
                        <div class="offer-content">
                            <?php the_field('recycle'); ?>
                            <?php echo $bottom; ?>
                            
                            <div class="arrow next" att-step="row-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="133.5" height="38" viewBox="0 0 133.5 38">
                                <g id="Group_39" data-name="Group 39" transform="translate(0.5 0.5)">
                                    <path id="Union_3" data-name="Union 3" d="M20,18.5,0,0ZM0,37,20,18.5Z" transform="translate(20 37) rotate(180)" stroke="#f7f7f7" stroke-linejoin="round" stroke-width="1"/>
                                    <line id="Line_13" data-name="Line 13" x2="131" transform="translate(1.5 18.5)" fill="none" stroke="#f7f7f7" stroke-linecap="round" stroke-width="1"/>
                                </g>
                                </svg>
                                <span></span>
                            </div> 
                        </div>
                    </div> 
                </div>
                
                <?php
                    $class_reuse = "no-img";
                    $back_reuse = "";
                    if(get_field('reuse_image')){
                        $back_reuse = 'background-image: url('.get_field('reuse_image').');';
                        $class_reuse = "has-img";
                    } 

                    $bottom =  "";
                    $link = get_field('reuse_image_buttom');
                    if( $link ): 
                        $link_url = $link['url'];
                        $link_title = $link['title'];
                        $link_target = $link['target'] ? $link['target'] : '_self';
                        $bottom = '<a class="read-more" href="'.esc_url( $link_url ).'" target="'.esc_attr( $link_target ) .'">'.esc_html( $link_title ) .'</a>';
                   endif;
                ?>
                <div id="row-3" class="offerItem   <?php echo $class_reuse; ?>"  style="<?php echo $back_reuse; ?>" >
                    <h4 class="offerItemTitle">Reuse</h4>
                    <div class="offer-detail"> 
                        <div class="offer-content">
                            <?php the_field('reuse'); ?> 
                            <?php echo $bottom; ?>
                            <div class="arrow next" att-step="row-4">
                                
                                <svg xmlns="http://www.w3.org/2000/svg" width="133.5" height="38" viewBox="0 0 133.5 38">
                                <g id="Group_39" data-name="Group 39" transform="translate(0.5 0.5)">
                                    <path id="Union_3" data-name="Union 3" d="M20,18.5,0,0ZM0,37,20,18.5Z" transform="translate(20 37) rotate(180)" stroke="#f7f7f7" stroke-linejoin="round" stroke-width="1"/>
                                    <line id="Line_13" data-name="Line 13" x2="131" transform="translate(1.5 18.5)" fill="none" stroke="#f7f7f7" stroke-linecap="round" stroke-width="1"/>
                                </g>
                                </svg>
                                <span>Find out more</span>
                            </div> 
                        </div>
                    </div>
                </div>
                
                <?php
                    $class_reuse = "no-img";
                    $back_reuse = "";
                    if(get_field('dsa_services')){
                        $back_reuse = 'background-image: url('.get_field('dsa_services').');';
                        $class_reuse = "has-img";
                    } 
                ?>
                <div id="row-4" class="offerItem active  <?php echo $class_reuse; ?>"  style="<?php echo $back_reuse; ?>" >
                    <h4 class="offerItemTitle">DSA Connect Services</h4> 
                    <div class="offer-detail"> 
                        <div class="offer-content">
                            <div class="arrow prev" att-step="row-3"> 
                                
                                <svg xmlns="http://www.w3.org/2000/svg" width="133.5" height="38" viewBox="0 0 133.5 38">
                                <g id="Group_39" data-name="Group 39" transform="translate(0.5 0.5)">
                                    <path id="Union_3" data-name="Union 3" d="M20,18.5,0,0ZM0,37,20,18.5Z" transform="translate(20 37) rotate(180)" stroke="#f7f7f7" stroke-linejoin="round" stroke-width="1"/>
                                    <line id="Line_13" data-name="Line 13" x2="131" transform="translate(1.5 18.5)" fill="none" stroke="#f7f7f7" stroke-linecap="round" stroke-width="1"/>
                                </g>
                                </svg>
                                <span>Find out more</span> 
                            </div> 
                        </div>
                    </div>
                </div> 

            </div>
           
        </div>
    </section>
    <!--offer section area end-->