/**
 * Script to populate State select based on selection in Country select
 */
var countrySelect = '#<?php echo $country_name; ?>';

function setStates(selectedCountry) {
        var addressStates = '';
		var selectedOption = '<?php echo $state_value; ?>',
            selectedStates,
            options = '<option value=""><?php echo lang('bf_select_state')?></option>';
			
		jQuery.get(base_url+'/localization/getStates/', {country: selectedCountry}, function(data) {
			addressStates = data;
			
			if (typeof addressStates[selectedCountry] != 'undefined') {
				selectedStates = addressStates[selectedCountry];
				for (var i in selectedStates) {
					options += '<option value="' + i + '"';
					if (i == selectedOption) {
						options += ' selected="selected"';
					}
					options += '>' + selectedStates[i] + '</option>';
				}
			} else {
				options = '<option value="0"><?php echo lang('bf_select_no_state')?></option>';
			}
	
			$('#<?php echo $state_name; ?>').html(options);
		}, 'json');      
    };

$(document).on('change', '#country', function(e){
	setStates(this.options[e.target.selectedIndex].value);
});

/* Make sure the State select is in sync with the Country select
 * when the page loads, especially for existing users
 */
$(function() {
    //setStates();
});