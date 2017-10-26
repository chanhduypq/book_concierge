$(document).ready(function() {
	$('select.default_currency').change(function() {
		var selected = $(this).find('option:selected').val();
		var id = $(this).attr('rel');

		$('select.currency').each(function(i) {
			var current = $(this).attr('rel');
			if (current == 'store_'+id) {
				$(this).val(selected);
			}
		});
	});
});