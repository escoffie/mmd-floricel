<!DOCTYPE html>
<html lang="es-MX">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <div class="page-wrapper">

        <header class="site-header">
            <div class="container">
                <div class="nav-bar">
                    <div class="main-logo">
                        <?php the_custom_logo(); ?>
                    </div>
                    <div class="burger-open">
                        <i class="far fa-ellipsis-v"></i>
                    </div>
                    <?php
                    $args = array(
                        'theme_location'    =>  'menu-social-1',
                        'container'         =>  'nav',
                        'container_class'   =>  'social-nav',
                    );
                    wp_nav_menu($args);
                    ?>
                </div>
            </div>
        </header>

        <main class="site-main container">

            <div class="main-nav">
                <div class="logo-mobile">
                    <?php the_custom_logo(); ?>
                    <div class="burger-close">
                        <i class="far fa-times"></i>
                    </div>
                </div>
                <?php
                $args = array(
                    'theme_location'    =>  'menu-header-1',
                    'container'         =>  'nav',
                    'container_class'   =>  'main-nav-ul',
                );
                wp_nav_menu($args);
                ?>
            </div>

            <section class="main-section">
                <?php
                if ( function_exists('yoast_breadcrumb') and !is_front_page() ) {
                    yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
                }
                ?>