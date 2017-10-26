jQuery(document).ready(function() {
	jQuery('.sidebar-mobile-menu a').click(function() {
		if (jQuery('#main-menu').is(':visible')) {
			jQuery('#main-menu').slideUp();	
		} else {
			jQuery('#main-menu').slideDown();	
		}
		
		return false;
	});
	
	jQuery('#main-menu > li').each(function() {
		if (jQuery(this).children('ul').length) {
			jQuery(this).addClass('root-level has-sub');
			
			if (jQuery(this).hasClass('opened')) {
				jQuery(this).children('ul').slideDown();
			}
		}		
	});
	
	jQuery('#main-menu').on('click', '.has-sub > a', function() {
		if (jQuery(this).parent('.has-sub').children('ul').is(':visible')) {
			jQuery(this).parent('.has-sub').children('ul').slideUp();	
			jQuery(this).parent('.has-sub').removeClass('opened');
		} else {
			jQuery(this).parent('.has-sub').children('ul').slideDown();
			jQuery(this).parent('.has-sub').addClass('opened');
		}
		
		return false;
	});
	
	jQuery(".check-all").change(function(){
		jQuery("table input[type=checkbox]").attr('checked', jQuery(this).is(':checked'));
	});

});