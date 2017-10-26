<?php

$validation_errors = validation_errors();

if ($validation_errors) :
?>
<div class="alert alert-block alert-error fade in">
	<a class="close" data-dismiss="alert">&times;</a>
	<h4 class="alert-heading">Please fix the following errors:</h4>
	<?php echo $validation_errors; ?>
</div>
<?php
endif;

if (isset($featured))
{
	$featured = (array) $featured;
}
$id = isset($featured['id']) ? $featured['id'] : '';

?>
<div class="admin-box">
	<h3>Book Details</h3>
	<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" id="isbn_form"'); ?>
		<fieldset>

			<div class="control-group <?php echo form_error('ean') ? 'error' : ''; ?>">
				<?php echo form_label('ISBN13'. lang('bf_form_label_required'), 'ean', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='ean' type='text' name='ean' value="<?php echo set_value('ean', isset($featured['ean']) ? $featured['ean'] : ''); ?>" />
					<span class="help-inline" id="ean_loading" style="display:none;"><img src="<?php echo Template::theme_url('images/ajax-loader.gif') ?>" alt="loading" /></span>
					<span class='help-inline'><?php echo form_error('ean'); ?></span>
				</div>
			</div>
		</fieldset>
    <?php echo form_close(); ?>
	<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
		<input type="hidden" name="selected_isbn" value="" />
		<div class="row" style="display:none" id="book_info">
			<div class="col-sm-2" id="bookimg"></div>
			<div class="col-sm-6" id="bookdetails"></div>
		</div>
		<div class="form-actions">
			<input type="submit" name="save" class="btn btn-primary" value="Save" disabled="disabled" id="save_featured"  />
			<?php echo lang('bf_or'); ?>
			<?php echo anchor(SITE_AREA.'/settings/books/featured/'.$current_country, lang('books_cancel'), 'class="btn btn-warning"'); ?>				
		</div>
    <?php echo form_close(); ?>
</div>
<script language="javascript">
<!--
var processing = false;
function processBook(){
	if (processing)
		return;
	
	var value = $("#ean").val();
	if (value.length >= 13) {
		processing = true;
		$("#ean").attr('readonly', 'readonly');
		$('#book_info').hide();
		$('#save_featured').attr('disabled', 'disabled');
		$('input[name="selected_isbn"]').val('');
		$('#ean_loading').show();
		jQuery.post(base_url+'books/fetcbByISBN', $('#isbn_form').serialize(), function(data){
			if (data.error) {
				alert(data.error);
			} else {
				if (data.cdn_image != '')
					$('#bookimg').html('<img src="'+site_url+'assets/covers/'+data.cdn_image+'" class="img-responsive" />');
				else
					$('#bookimg').html('<img src="<?php echo Template::theme_url('images/no-image.jpg') ?>" class="img-responsive" />');
					
				$('#bookdetails').html('<h2>'+data.name+'</h2><p>'+data.author+'</p>');
				$('#book_info').fadeIn();
				$('#save_featured').removeAttr('disabled');
				$('input[name="selected_isbn"]').val(data.ean);
			}			
			processing = false;
			$('#ean_loading').hide();
			$("#ean").removeAttr('readonly');
			$("#ean").val('');
		}, 'json');
	}
}

//-->
</script>

<?php
$inline = '
$("#ean").change(function() {
	processBook();
});

$("#ean").keyup(function() {
	processBook();
});

';

Assets::add_js($inline, 'inline');