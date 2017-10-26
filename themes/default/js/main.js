$(document).ready(function($) {
	var cache = {};
	$( "#search_box" ).autocomplete({		
		minLength: 2,
		source: function( request, response ) {
			var term = request.term;
			if ( term in cache ) {
				response( cache[ term ] );
				return;
			}
			$.getJSON( base_url+"/books/autocomplete", request, function( data, status, xhr ) {
				cache[ term ] = data;
				response( data );
			});
		},
		select: function(e, ui) {
			document.location.href = base_url+'/books/search/'+encodeURIComponent(ui.item.value);	
			return false;
		}
	});
        
        var owl = $('.owl-carousel');
        owl.owlCarousel({
            margin: 10,
            nav: false,
            loop: true,
            responsive: {
                0: {
                    items: 2
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        });
	
	jQuery('#scroll_to_search').on('click', function() {
		$.smoothScroll({
			scrollTarget: '.search-box',
			afterScroll: function() {
				$('#search_box').focus();
			}
		});
		return false;
	});
	
	jQuery('#show_scroll_search_box, #show_scroll_search_box_mobile').on('click', function() {
		jQuery('.search-box').fadeIn('fast', function() {
			$.smoothScroll({
				scrollTarget: '.search-box',
				afterScroll: function() {
					$('#search_box').focus();
				}
			});
		});
		
		return false;
	});
	
	jQuery.ui.autocomplete.prototype._resizeMenu = function () {
		var ul = this.menu.element;
		ul.outerWidth(this.element.outerWidth());
	}
	
	$('#search_form').submit(function() {
		addTab($(this).find('#keywords').val());
		
		return false;
	});
	
	var tabs = $('.tabs').tabs({ 
		beforeLoad: function( event, ui ) {			
			if ( ui.tab.data( "loaded" ) ) {
			  event.preventDefault();
			  return;
			}
			
			if ($(ui.panel).is(":empty")) {
				$(ui.panel).append("<div class='tab-loading'>Loading...</div>")
			}
			
			ui.jqXHR.success(function() {
			  ui.tab.data( "loaded", true );
			});
		},
		load: function(event, ui) {
			loadDisqus();
		},
		beforeActivate: function (event, ui) {
			$(ui.newPanel).find('#comment_container').append($('#disqus_thread'));
		}
	});
	
	var tabCounter = totalTabCounter = $('.ui-tabs-nav > li').length + 1;
	var tabTemplate = "<li><a href='#{href}'>#{label}</a> <a href='#' class='close-tab'><i class='fa fa-times-circle'></i></a></li>" // <span class='ui-icon ui-icon-close' role='presentation'>Remove Tab</span>
	function addTab(label, href) {	
		href = typeof href !== 'undefined' ? href : "results.php?q=" + escape(label); 
	
		id = "ui-tabs-" + tabCounter,
		li = $( tabTemplate.replace( /#\{href\}/g, href ).replace( /#\{label\}/g, label ) ),
		tabContentHtml = "";
		tabs.find( ".ui-tabs-nav" ).append( li );
		tabs.append( "<div id='" + id + "'>" + tabContentHtml + "</div>" );
		tabs.tabs( "refresh" );
		$( ".tabs" ).tabs( "option", "active", totalTabCounter-1);
		tabCounter++;
		totalTabCounter++;
	}	
	
	$(document).on('click', '.ui-tabs-panel .pagination a', function() {
		var active = $( ".tabs" ).tabs( "option", "active" );
		var href = $(this).attr('href');
		
		$('#ui-tabs-'+active).html("<div class='tab-loading'>Loading...</div>");
		jQuery.get(href, function(data) {
			$('#ui-tabs-'+active).html(data);
			loadDisqus();
		});
		
		return false;
	});
	
	$(document).on('click', '.ui-tabs-panel .result a', function() {
		var text = $(this).html();
		addTab(text.substr(0,25)+'...', $(this).attr('href'));
		
		return false;
	});
	
	$(document).on('click', '.close-tab', function() {
		panel = $(this).parent().attr('aria-controls');		
		$(this).parent().remove();
		
		tabs.find('div#'+panel).remove();
		tabs.tabs( "refresh" );
		
		totalTabCounter--;
		
		if (!$('.tabs > ul > li').length) {
			document.location.href = base_url;
		}
		
		return false;
	});
	
	jQuery('.slider-arrow').click(function(e) {
        e.preventDefault();
		if($(this).hasClass('show')){
			jQuery('.shopping_panel').animate({right: 0}, 1000, 'easeOutQuart');			
			$(this).removeClass('show');
		} else {
			jQuery('.shopping_panel').animate({right: -300}, 1000, 'easeOutQuart');
			$(this).addClass('show');
		}
    });
    
    $('#search_box').keypress(function() {
        if ($(this).val() != '')
            $(this).siblings('.fa').show();
        else
            $(this).siblings('.fa').hide();
    })
    
    $('.search-form .fa-times').click(function() {
        $('#search_box').val('');
        $(this).hide();
    });
	
	loadDisqus();
});

function loadDisqus() {	
	if (!$('#disqus_thread').length)
		return;
	
	/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
	var disqus_shortname = 'readoutlet'; // required: replace example with your forum shortname

	/* * * DON'T EDIT BELOW THIS LINE * * */
	(function() {
		var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	})();
}

function fetchBookPrices(ean, container, book_stores_count) {
	var total_run = Math.ceil(book_stores_count/5);
        var stores = [];
        $('.book-price').each(function(i) {
            stores.push($(this).data('id'));
        });
        run = 1;
	for (i=0; i<total_run; i++) {
                step = [];
                for (j=(run-1)*5; j < run * 5; j++) {
                    if (j > stores.length)
                        break;
                    
                    step.push(stores[j]);
                }
		$('#'+container).load(base_url+'/books/fetch_price/'+ean+'_'+step.join('_'), function() {
			if(i == total_run) {
				$('#loading_now_message').html('Prices for this book were updated moments ago.');	
			}
		});
                
                run++;
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