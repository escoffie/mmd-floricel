<?php get_header(); ?>
<!--    PAGE -->

<?php while (have_posts()) :  the_post(); ?>

    <?php if (!is_front_page() && !is_home()) : ?>
    <h1 class="the-title"><?php the_title(); ?></h1>
    <?php endif; ?>

    <?php the_post_thumbnail(); ?>

    <?php the_content(); ?>
    
    
    <?php
        $children = get_page_children(get_the_ID(), get_pages(array('sort_column' => 'menu_order')));
        if (count($children) > 0) {
            ?>
            <h2 class="page-children-title"><?php echo __('Sigue explorando', 'floricel'); ?></h2>
            <div class="page-children">
            <?php
            foreach ($children as $child) {
                ?>
            <a href="<?= get_permalink($child); ?>" class="page-child">
                <h2 class="page-child-title"><?= $child->post_title; ?></h2>
                <p class="page-child-excerpt"><?= get_the_excerpt($child) ?></p>
                <button class="page-child-button"><span><img src="<?php echo get_template_directory_uri(  ); ?>/img/deco-left-white.png" alt=""><?php echo __('Leer más', 'floricel'); ?><img src="<?php echo get_template_directory_uri(  ); ?>/img/deco-right-white.png" alt=""></span></button>
            </a>
    <?php
            }
            ?>
            </div><!--/.page-children-->
            <?php

        }

    ?>

<?php endwhile; ?>

<?php get_footer();
