<?php

/* dataLayer.push({ */
/*   'event': 'add_to_cart', */
/*   'ecommerce': { */
/*     'items': [{ */
/*       'item_name': 'Donut Friday Scented T-Shirt', // Name or ID is required. */
/*       'item_id': '67890', */
/*       'price': '33.75', */
/*       'item_brand': 'Google', */
/*       'item_category': 'Apparel', */
/*       'item_category2': 'Mens', */
/*       'item_category3': 'Shirts', */
/*       'item_category4': 'Tshirts', */
/*       'item_variant': 'Black', */
/*       'item_list_name': 'Search Results', */
/*       'item_list_id': 'SR123', */
/*       'index': 1, */
/*       'quantity': '2' */
/*     }] */
/*   } */
/* }); */

add_action('woocommerce_add_to_cart', function () {
    add_action( 'wp_footer', function( ) {
        echo "
            <script>
                dataLayer.push({event: 'add_to_cart'});
            </script>
    ";
    });
});


add_action( 'wp_footer', function( ) {
    global $wp_query;

    if ( $wp_query->is_search() ) {
        $search = $wp_query->query['s'];
        echo "
            <script>dataLayer.push({ event: 'search', search_term: '$search' })</script>
        ";
    }

    if ( is_product_category() ) {
        $product_category = $wp_query->query['product_cat'];
        echo "
            <script>dataLayer.push({ event: 'view_category', category: '$product_category' })</script>
        ";
    }

    if( is_checkout() ) {
        echo "
            <script>dataLayer.push({ event: 'begin_checkout' })</script>
        ";
    }

    if( is_account_page() ) {
        global $current_user;
        $user_name = $current_user->display_name;
        echo "
            <script>dataLayer.push({ event: 'identify', name: '$user_name' })</script>
        ";
    }

    if( is_product() ) {
        global $post;

        $product = wc_get_product($post->ID);

        if(!empty($product)) {
            $item_sku = $product->get_sku();
            $item_title = $product->get_title();
            echo "
                <script>dataLayer.push(
                    {
                        event: 'view_item',
                        ecommerce: {
                            items: [
                                item_name: '$item_title',
                                item_sku: '$item_sku',
                            ]
                        }
                   });
                </script>
            ";
        }
    }

/* dataLayer.push({ */
/*   'event': 'purchase', */
/*   'ecommerce': { */
/*       'transaction_id': 'T12345', */
/*       'affiliation': 'Online Store', */
/*       'value': '59.89', */
/*       'tax': '4.90', */
/*       'shipping': '5.99', */
/*       'currency': 'EUR', */
/*       'coupon': 'SUMMER_SALE', */
/*       'items': [{ */
/*         'item_name': 'Triblend Android T-Shirt', */
/*         'item_id': '12345', */
/*         'price': '15.25', */
/*         'item_brand': 'Google', */
/*         'item_category': 'Apparel', */
/*         'item_variant': 'Gray', */
/*         'quantity': 1 */
/*       }, { */
/*         'item_name': 'Donut Friday Scented T-Shirt', */
/*         'item_id': '67890', */
/*         'price': '33.75', */
/*         'item_brand': 'Google', */
/*         'item_category': 'Apparel', */
/*         'item_variant': 'Black', */
/*         'quantity': 1 */
/*       }] */
/*   } */
/* }); */

     if(!empty($wp_query->query['order-received'])) {
        $transaction_id = $wp_query->query['order-received'];
        $order = new \WC_Order( $transaction_id );

        $status = $order->get_status();

        $value = $order->get_total();
        $tax = $order->get_total_tax();
        $shipping = $order->get_shipping_total();
        $currency = $order->get_currency();
        $coupons = !empty($order->get_coupon_codes()) ? implode(',', $order->get_coupon_codes()) : '';
        $discount = $order->get_discount_total();

        $order_items = $order->get_items();
        $items = [];
        foreach($order_items as $item_id => $item) {
            $product = $item->get_product();
            $sku = $product->get_sku();
            $qty = $item->get_quantity();
            $precoUnit = $item->get_subtotal()/$qty;
            $cat_ids = $product->get_category_ids();

            $items[] = [
                'item_name' => $product->get_title(),
                'item_id' => $item_id,
                'price' => $precoUnit,
                'item_brand' =
                'item_category'
                'sku' => $sku,
                'quantity' => $qty,
            ];
        }

        //$user_id = $order->get_user_id();
        $user_email = $order->get_billing_email();
        $user_fullname = $order->get_billing_first_name(). ' ' .$order->get_billing_last_name();

        echo "Order '$order'";
        echo "
            <script>dataLayer.push({
                event: 'purchase',
                ecommerce: {
                    'transaction_id': 'T12345',
                    'affiliation': 'Online Store',
                    'value': '59.89',
                    'tax': '4.90',
                    'shipping': '5.99',
                    'currency': 'EUR',
                    'coupon': 'SUMMER_SALE',
                    'items': $items
                }
            });
            </script>
        ";
     }
});
