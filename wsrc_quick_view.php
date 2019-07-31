<?php
/** ===============================================================================
  Plugin Name: Uptimex Quick View
=================================================================================*/

class wsrc_quick_view{
	public function __construct() {
		add_action('init', array($this, 'init'));

	}
	
	public function init() {
		add_action( 'wp_footer', array($this, 'add_modal' ));
		add_action( 'woocommerce_after_shop_loop_item',  array($this, 'add_button' ));
		
		// AJAX

		add_action( 'wp_enqueue_scripts', array( $this, 'init_plugin' ) );
		add_action( 'wp_ajax_wsrc_quick_view_ajax_retrieve', array( $this, 'wsrc_quick_view_ajax_retrieve' ) ); 
		add_action( 'wp_ajax_nopriv_wsrc_quick_view_ajax_retrieve', array( $this, 'wsrc_quick_view_ajax_retrieve' ) );		

	}
	


    public function init_plugin()
    {
		wp_enqueue_style( 'wsrc-quick-view-style', plugins_url( '/style.css',__FILE__ ) );
        wp_enqueue_script( 
            'wsrc_quick_view_ajax_script', 
            plugins_url( '/scripts.js',__FILE__ ), 
            array('jquery'), 
            TRUE 
        );
        wp_localize_script( 
            'wsrc_quick_view_ajax_script', 
            'wsrc_quick_view_ajax', 
            array(
                'url'   => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( "wsrc_quick_view_ajax_retrieve_nonce" ),
            )
        );
    }

    public function wsrc_quick_view_ajax_retrieve()
    {
        check_ajax_referer( 'wsrc_quick_view_ajax_retrieve_nonce', 'nonce' );
		
		$product = wc_get_product( $_POST['product_id']);
		

		$attachment_ids = $product->get_gallery_image_ids();
		
		foreach( $attachment_ids as $attachment_id ) {
			$gallery_images[] = wp_get_attachment_url( $attachment_id );
		}
		

		// Gather product information
		$product_info = array(
			'id' => $product->get_id(),
			'name' => $product->get_name(),
			'description' => $product->get_description(),
			'price' => $product->get_price(),
			'average_rating' => $product->get_average_rating(),
			'review_count' => $product->get_review_count(),
			'image' => get_the_post_thumbnail_url( $product->get_id(), 'full' ),
			'gallery' => $gallery_images,
		);
		
		echo do_shortcode('[product_page  id="'.$product->get_id().'"]');
		// $this->templating($product_info);
		die;

        if( true )
            wp_send_json_success( $product->get_name() );
        else
            wp_send_json_error( array( 'error' => $custom_error ) );
    }
	
	public function templating($product_info) {
		?>
			<div class="wsrcQuickViewModal">
				<div class="row">
					<div class="col col-7">
						<div class="wsrcQuickViewModalImage"><img src="<?php echo $product_info['image']; ?>"></div>
						<div class="wsrcQuickViewModalGallery">
							<?php foreach($product_info['gallery'] as $gallery_image): ?>
								<div><img style="width: 20px; height: auto;" src="<?php echo $gallery_image; ?>"></div>	
							<?php endforeach;?>
						</div>
					</div>
					<div class="col col-5">
						<div class="row">
							<div class="col col-6"><?php echo $product_info['name']; ?></div>
							<div class="col col-6"><?php echo $product_info['price']; ?></div>
						</div>
						<div class="wsrcQuickViewModalRating"><?php echo $product_info['average_rating']; ?>(<?php echo $product_info['review_count']; ?>)</div>
						<div class="wsrcQuickViewModalQty">
							<form class="cart" method="post" enctype="multipart/form-data">
								<div class="quantity">
									<input type="number" step="1" min="1" max="" name="quantity" value="1" title="Quantity" class="input-text qty text" size="4" pattern="[0-9]*" inputmode="numeric">
								</div>

								<input type="hidden" name="add-to-cart" value="<?php echo $product_info['id']; ?>">

								<button type="submit" class=" button "><i class="fa fa-cart-plus" aria-hidden="true"></i> Add to cart</button>
							</form>
						</div>						
						<div class="wsrcQuickViewModalDetails"><label>Details<textarea></textarea></label></div>

					</div>
				</div>
			</div>
		<?php 
	}
	
	
	public function add_button() {
		global $product;
		$product_id = $product->get_id();

		?>
			<button data-product-id="<?php echo $product_id; ?>" type="button" class="btn btn-primary wsrcQuickViewModalButton" data-toggle="modal" data-target="#wsrc_quick_view_modal">
			  Launch demo modal
			</button>
		<?php
	}
	
	public function add_modal() {
		?>
<!-- Modal -->
<div class="modal fade wsrc_quick_view_modal" id="wsrc_quick_view_modal" tabindex="-1" role="dialog" aria-labelledby="wsrc_quick_view_modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="wsrc_quick_view_modalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body woocommerce">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>	

		<?php
	}
	
}

new wsrc_quick_view;
