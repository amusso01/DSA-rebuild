<div class="post-info alm-item<?php if (! has_post_thumbnail() ) { echo ' no-img'; } ?>">
    <a href="<?php the_permalink(); ?>">
        <?php if ( has_post_thumbnail() ) { the_post_thumbnail('full'); } ?>
    </a>
   <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
   <?php //the_excerpt(); ?>
</div>