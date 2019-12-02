<?php

// https://martinlea.com/how-to-catch-the-referer-page-with-contact-form-7/

/**
 * Para que funcione, hay que añadir el campo [hidden referer-page default:get] al formulario de contact form 7 donde se quiere usar
 */

function getRefererPage($form_tag)
{
    if (isset($_SERVER['HTTP_REFERER']) && $form_tag['name'] == 'referer-page') {
        $form_tag['values'][] = htmlspecialchars($_SERVER['HTTP_REFERER']);
    }
    return $form_tag;
}
if (!is_admin()) {
    add_filter('wpcf7_form_tag', 'getRefererPage');
}
