<?php get_header(); ?>
<!--    INDEX -->

<div class="blog-list">
    
    <?php while( have_posts(  ) ):  the_post(); ?>
    <div class="blog-entry">
        <h1 class="blog-entry-title" id="<?= get_the_ID(); ?>"><?php the_title(); ?></h1>
        <?php the_content(); ?>
    </div>
    <?php endwhile;?>

</div>


<?php get_footer(  );