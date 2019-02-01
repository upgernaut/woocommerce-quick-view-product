jQuery(document).ready(function($) 
{
    $('.wsrcQuickViewModalButton').click(function(e) 
    {
        e.preventDefault();
		var $productId = $(this).attr('data-product-id');
		
		
        var data = {
            action: 'wsrc_quick_view_ajax_retrieve',
            nonce: wsrc_quick_view_ajax.nonce,
			product_id: $productId,
        };

        $.post( wsrc_quick_view_ajax.url, data, function( response ) 
        {
			$('.wsrc_quick_view_modal .modal-body').html(response);
        });
    });
});