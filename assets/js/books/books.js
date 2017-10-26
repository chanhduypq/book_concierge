function fetchBookPrices(ean, container, book_stores_count) {
	var total_run = Math.ceil(book_stores_count/5);
	for (i=0; i<total_run; i++) {
		$('#'+container).load(base_url+'/books/fetch_price/'+ean, function() {
			if(i == total_run) {
				$('#loading_now_message').html('Prices for this book were updated moments ago.');	
			}
		});
	}
}

$(document).ready(function() {	
	var full_desc = false;
	$('#full_description').click(function() {
		box_height = $('.snip div').height();
		if (!full_desc) {
			$('.snip').animate({height: box_height+'px'}, 500, function() {});
			$('#fadeGradient').hide();
			$(this).html('Less');
			full_desc = true;
		} else {
			$('.snip').animate({height: '130px'}, 500, function() {});
			$('#fadeGradient').show();
			$(this).html('Read More');
			full_desc = false;
		}
		
		return false;
	});
	
	$(document).on('click', '.show_price_details', function() {		
		var clicked = $(this);
		
		if (clicked.parents('td').find('.price_details').is(':visible'))
			return false;		
		
		$('.price_details').slideUp('fast');
		clicked.parents('td').find('.price_details').slideDown('fast');
		
		return false;
	});
	
	$( document ).ajaxStart(function() {
		
	});
	
	$( document ).ajaxStop(function() {
		
	});
	
	$(document).on('click', '.add_to_cart', function() {			
		if ($(this).hasClass('disabled')) return false;
		
		$('#page_loading_container').fadeIn();
		jQuery.get($(this).attr('href'), function(data) {
			if (data == 'SUCCESS') {				
				$('.add_to_cart').addClass('disabled');
				updateCart();
				$('#page_loading_container').fadeOut();
			} else {
				
			}
		});
		
		return false;
	});
	
	$(document).on('click', '.delete_from_cart', function() {			
		$('#page_loading_container').fadeIn();
		var removing = $(this).attr('rel');
		jQuery.get($(this).attr('href'), function(data) {
			if (data == 'SUCCESS') {				
				updateCart();
				$('#page_loading_container').fadeOut();
				$('.add_'+removing).removeClass('disabled');
			} else {
				
			}
		});
		
		return false;
	});
	
	$(document).on('click', '.checkout', function() {
		$('.shopping-cart .store_url').each(function() {
			//$(this).trigger('click');
			open_in_new_tab('win_'+$(this).attr('rel'), $(this).attr('href'));
		});
		
		return false
		// empty the cart
	});
	
	$(document).on('click', '.store_url', function() {
		window.open($(this).attr('href'));
	});
});

function open_in_new_tab(name, url)
{
	if (typeof(window.open('',name))=='undefined') {
		alert("Your browser have popup blocker installed, which blocked some of the tabs being opened. Try disabling it or click on each of the Books in your cart to buy them.");
	}
	
	window.open('',name).location.replace(url)
}

function updateCart() {
	$('.cart_contents').load(base_url+'/cart/update_cart_view/', function() {
																		   
	});
}