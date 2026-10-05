<!--offer section area-->
<section class="offer-area section">
        <div class="offer-tabs tab-content">
            <div class="offerslide tab-pane fade active show" id="SOLUTIONS">


                <?php
                if( have_rows('slider','options') ):
                    $numrows = count(  get_field('slider','options') );
                    while( have_rows('slider','options') ) : the_row();
                        $back="";
                        $class= "no-img";
                        if(get_sub_field('icon')){
                            $back = 'background-image: url('.get_sub_field('icon').');';
                            $class = "has-img";
                        }

                        $active = '';
                        $hide = '';
                        $step = 'next';
                        if( get_row_index() == $numrows){
                            $active = 'active'; 
                            //$hide = 'hide'; 
                            $step = 'prev';
                            $nextStep = get_row_index()-1;
                        }else{
                            $nextStep = get_row_index()+1;
                        }
                        
                        $bottom =  "";
                        $link = get_sub_field('bottom');
                        if( $link ): 
                            $link_url = $link['url'];
                            $link_title = $link['title'];
                            $link_target = $link['target'] ? $link['target'] : '_self';
                            $bottom = '<a class="read-more" href="'.esc_url( $link_url ).'" target="'.esc_attr( $link_target ) .'">'.esc_html( $link_title ) .'</a>';
                       endif;
                        
                        
                    
                        echo '<div id="row-'.get_row_index().'" class="offerItem '.$active.' '.$class.'"  style="'. $back.'">
                                <h4 class="offerItemTitle '.$hide.'">'.get_sub_field('title').'</h4>
                                <div class="offer-detail">
                                    <div class="offer-content">
                                        '.get_sub_field('description').'
                                        '.$bottom.'
                                        <div class="arrow '.$step.'" att-step="row-'.$nextStep.'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="133.5" height="38" viewBox="0 0 133.5 38">
                                            <g id="Group_39" data-name="Group 39" transform="translate(0.5 0.5)">
                                                <path id="Union_3" data-name="Union 3" d="M20,18.5,0,0ZM0,37,20,18.5Z" transform="translate(20 37) rotate(180)" stroke="#f7f7f7" stroke-linejoin="round" stroke-width="1"/>
                                                <line id="Line_13" data-name="Line 13" x2="131" transform="translate(1.5 18.5)" fill="none" stroke="#f7f7f7" stroke-linecap="round" stroke-width="1"/>
                                            </g>
                                            </svg>
                                            <span>Find out more about our range of services</span>
                                        </div> 
                                    </div>
                                </div>
                            </div>';
                    endwhile;
                endif
                ?>

            </div>
             
        </div>
    </section>
    <!--offer section area end-->