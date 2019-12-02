        </section><!-- /.main-section-->
    </main> <!-- /.site-main .container -->

    <footer class="site-footer">
        <div class="footer-mobile">
            <div class="call">
                <a href="tel:+<?php echo preg_replace('/[^0-9]/', '', do_shortcode('[floricel-contact type="phone" text-only=true]')); ?>">
                    <i class="fas fa-3x fa-mobile-android-alt"></i> <?php echo __('Llamar', 'floricel'); ?>
                </a>
            </div>
            <div class="whats">
                <a target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo preg_replace('/[^0-9]/', '', do_shortcode('[floricel-contact type="whatsapp" text-only=true]')); ?>">
                    <i class="fab fa-3x fa-whatsapp"></i> <?php echo __('Whatsapp', 'floricel'); ?>
                </a>
            </div>
            <div class="contact">
                <a href="<?php echo preg_replace('/[^0-9]/', '', do_shortcode('[floricel-contact type="url" text-only=true]')); ?>">
                    <i class="fal fa-3x fa-comment-lines"></i> <?php echo __('Contactar', 'floricel'); ?>
                </a>
            </div>
        </div>
        <div class="container">
            <p><?php echo __('Derechos reservados', 'floricel'); ?> &copy; <?php bloginfo('name'); ?> 2014-<?php echo date('Y'); ?></p> 
            <p class="footer-desktop">
                <?php echo do_shortcode('[floricel-contact type="phone" fa-icon="phone"]'); ?> 
                <?php echo do_shortcode('[floricel-contact type="mobile" fa-icon="mobile"]'); ?> 
                <?php echo do_shortcode('[floricel-contact type="whatsapp" fa-icon="whatsapp" content="Whatsapp"]'); ?> 
            </p>
        </div>
    </footer>

</div> <!-- /.page-wrapper -->

<?php wp_footer(); ?>

</body>

</html>