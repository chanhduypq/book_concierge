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

if (isset($localization))
{
	$localization = (array) $localization;
}
$id = isset($localization['id']) ? $localization['id'] : '';

?>
<div class="admin-box">
	<h3>Currency Details</h3>
	<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
		<fieldset>

			<div class="control-group <?php echo form_error('iso') ? 'error' : ''; ?>">
				<?php echo form_label('Currency'. lang('bf_form_label_required'), 'iso', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='iso' type='text' name='iso' maxlength="3" value="<?php echo set_value('iso', isset($localization['iso']) ? $localization['iso'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('iso'); ?></span>
				</div>
			</div>

			<div class="control-group <?php echo form_error('name') ? 'error' : ''; ?>">
				<?php echo form_label('Country Name'. lang('bf_form_label_required'), 'default_country', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<select id="default_country" name="default_country">
						<option value="">None</option>
						<?php foreach ($countries as $country) { ?>
						<option value="<?= $country->iso?>" <?php echo isset($localization['default_country']) && $localization['default_country'] == $country->iso ? ' selected="selected"' : ''; ?>><?= $country->name ?></option>
						<?php } ?>
					</select>
					<span class='help-inline'><?php echo form_error('name'); ?></span>
				</div>
			</div>

			<div class="form-actions">
				<input type="submit" name="save" class="btn btn-primary" value="Save"  />
				<?php echo lang('bf_or'); ?>
				<?php echo anchor(SITE_AREA .'/settings/localization/currencies', lang('localization_cancel'), 'class="btn btn-warning"'); ?>
				
			</div>
		</fieldset>
    <?php echo form_close(); ?>
</div>