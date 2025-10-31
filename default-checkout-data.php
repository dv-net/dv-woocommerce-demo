<?php
// This snippet provide a default billing and shipping addresses to prevent typing in demoshop
// Put this snippet via Code Snippets extensions and don't forget to activate it 🚀
add_filter( 'woocommerce_checkout_fields' , 'set_default_checkout_fields_for_demo' );

function set_default_checkout_fields_for_demo( $fields ) {
    
    $fields['billing']['billing_first_name']['default'] = 'Demo';
    $fields['billing']['billing_last_name']['default']  = 'User';
    $fields['billing']['billing_company']['default']    = 'Demo Company';
    $fields['billing']['billing_address_1']['default']  = '123 Demo Street';
    $fields['billing']['billing_address_2']['default']  = 'Appt 4B';
    $fields['billing']['billing_city']['default']       = 'DemoCity';
    $fields['billing']['billing_postcode']['default']   = '12345';
    $fields['billing']['billing_country']['default']    = 'US'; // Укажите код страны, например 'RU' для России
    $fields['billing']['billing_state']['default']      = 'CA'; // Укажите код штата/региона (если нужно)
    $fields['billing']['billing_phone']['default']      = '08004442424';
    $fields['billing']['billing_email']['default']      = 'demo@example.com';

    $fields['shipping']['shipping_first_name']['default'] = 'Demo';
    $fields['shipping']['shipping_last_name']['default']  = 'User';
    $fields['shipping']['shipping_company']['default']    = 'Demo Company';
    $fields['shipping']['shipping_address_1']['default']  = '123 Demo Street';
    $fields['shipping']['shipping_address_2']['default']  = 'Appt 4B';
    $fields['shipping']['shipping_city']['default']       = 'DemoCity';
    $fields['shipping']['shipping_postcode']['default']   = '12345';
    $fields['shipping']['shipping_country']['default']    = 'US';
    $fields['shipping']['shipping_state']['default']      = 'CA';

    return $fields;
}
