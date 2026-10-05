<section class="white-section partner">
    <div class="container">
        <div class="certification">
                <?php if( get_field('cert_1', 'options')){
                 echo ' <img alt="iso" src="'.get_field('cert_1', 'options').'" >';
              } ?>
              <?php if( get_field('cert_2', 'options')){
                 echo ' <img alt="iso" src="'.get_field('cert_2', 'options').'" >';
              } ?>
              <?php if( get_field('cert_3', 'options')){
                 echo ' <img alt="iso" src="'.get_field('cert_3', 'options').'" >';
              } ?>
        </div>		
        
        
        <?php 
        if( get_field('cert_text', 'options') ){
            echo '<p>'.get_field('cert_text', 'options').'</p>';
        }
        ?>

        <div class="logos-list">
            <?php
              if( have_rows('logos', 'options') ):
                  while( have_rows('logos', 'options') ) : the_row();
                    echo ' <img alt="environment-agency-logo" src="'.get_sub_field('logo').'">';
                     
                  endwhile;
              endif;
              ?>
        </div>
	</div>
</section>