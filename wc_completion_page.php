<?php
global $dp_options, $usces;

if ( ! $dp_options ) $dp_options = get_design_plus_option();
$active_sidebar = get_active_sidebar();

get_header();
?>
<main class="l-main">
<?php
get_template_part( 'template-parts/page-header' );
get_template_part( 'template-parts/breadcrumb' );

if ( $active_sidebar ) :
?>
	<div class="l-inner l-2columns">
		<div class="l-primary">
<?php
else :
?>
	<div class="l-inner l-primary">
<?php
endif;

if ( have_posts() ) :
	the_post();
	usces_remove_filter();
?>
			<div class="p-entry p-wc p-wc-cart-page p-wc-<?php usces_page_name(); ?>">
				<div class="p-entry__body p-wc__body">
					<h2 class="p-wc-headline"><?php _e( 'It has been sent succesfully.', 'tcd-w' ); ?></h2>
					<div class="p-wc-header_explanation">
						<p><?php _e( 'Thank you for shopping.', 'tcd-w' ); ?><br><?php _e( "If you have any questions, please contact us by 'Contact'.", 'tcd-w' ); ?></p>
						<?php do_action( 'usces_action_cartcompletion_page_header', $usces_entries, $usces_carts ); ?>
					</div>
<?php
	if ( defined( 'WCEX_DLSELLER' ) ) :
		dlseller_completion_info( $usces_carts );
	endif;

	usces_completion_settlement();
	do_action( 'usces_action_cartcompletion_page_body', $usces_entries, $usces_carts );
?>
					<div class="p-wc-footer_explanation"><?php do_action( 'usces_action_cartcompletion_page_footer', $usces_entries, $usces_carts ); ?></div>

					<div class="send"><a href="<?php echo home_url(); ?>" class="back_to_top_button p-button p-button--lg"><?php _e( 'Back to the top page.', 'tcd-w' ); ?></a></div>
<?php
	echo apply_filters( 'usces_filter_conversion_tracking', NULL, $usces_entries, $usces_carts );
?>
				</div>
			</div>
<?php
else:
?>
			<p><?php _e( 'There is no registered post.', 'tcd-w' ); ?></p>
<?php
endif;

if ( $active_sidebar ) :
?>
		</div>
<?php
	get_sidebar();
?>
	</div>
<?php
else :
?>
	</div>
<?php
endif;
?>

<?php
    $sesdata = $usces->cart->get_entry();
    $order_id = $sesdata['order']['ID'];
    $cart = usces_get_ordercartdata( $order_id );

    // 商品データ
    $tmpItems = [];
    for( $i=0; $i<count($cart); $i++ ){ 
        $cart_row = $cart[$i];
        $sku = $cart_row['item_code']; 
        $itemName = $cart_row['item_name'];
        $skuPrice = $cart_row['price'];
        $quantity = $cart_row['quantity']; 
        $tmpItems[] = [$sku, $itemName, $skuPrice, $quantity];
    }
    $items = json_encode($tmpItems);
?>

<script type="text/javascript">
    var tran_id = '<?php echo $order_id; ?>';
    var items_str = '<?php echo $items; ?>';
    var items = JSON.parse(items_str)

    var revenue = 0;
    var ecomProducts = [];
    for (var i = 0; i < items.length; i++) {
        ecomProducts.push({
            'id' : items[i][0],
            'name' : items[i][1],
            'price' : String(items[i][2]),
            'quantity' : String(items[i][3])
        });
        revenue += parseInt(items[i][2]);
    }

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        'event': 'eec_purchase',
        'ecommerce': {
            'purchase': {
                'actionField': {
                    'id' : tran_id,
                    'revenue' : String(revenue),
                    'transactionTax' : String(0),
                    'transactionShipping' : String(0)
                },
                'products' : ecomProducts
            }
        }
    });
</script>

</main>
<?php get_footer(); ?>
