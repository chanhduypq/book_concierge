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
	<h3>Region Details</h3>
	<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
		<fieldset>

			<div class="control-group <?php echo form_error('name') ? 'error' : ''; ?>">
				<?php echo form_label('Region'. lang('bf_form_label_required'), 'name', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='name' type='text' name='name' maxlength="40" value="<?php echo set_value('name', isset($localization['name']) ? $localization['name'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('name'); ?></span>
				</div>
			</div>
			
			<div class="control-group <?php echo form_error('abbrev') ? 'error' : ''; ?>">
				<?php echo form_label('Abbrevation'. lang('bf_form_label_required'), 'abbrev', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='abbrev' type='text' name='abbrev' maxlength="2" value="<?php echo set_value('abbrev', isset($localization['abbrev']) ? $localization['abbrev'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('abbrev'); ?></span>
				</div>
			</div>

			<div class="control-group <?php echo form_error('country_iso') ? 'error' : ''; ?>">
				<?php echo form_label('Country Name'. lang('bf_form_label_required'), 'country_iso', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<select id="country_iso" name="country_iso">
						<option value="">Select</option>
						<?php foreach ($countries as $country) { ?>
						<option value="<?= $country->iso?>" <?php echo isset($localization['country_iso']) && $localization['country_iso'] == $country->iso ? ' selected="selected"' : ''; ?>><?= $country->name ?></option>
						<?php } ?>
					</select>
					<span class='help-inline'><?php echo form_error('name'); ?></span>
				</div>
			</div>

			<div class="form-actions">
				<input type="submit" name="save" class="btn btn-primary" value="Save"  />
				<?php echo lang('bf_or'); ?>
				<?php echo anchor(SITE_AREA .'/settings/localization/regions'.(isset($localization['country_iso']) ? '/'.$localization['country_iso'] : ''), lang('localization_cancel'), 'class="btn btn-warning"'); ?>
				
			</div>
		</fieldset>
    <?php echo form_close(); ?>
</div>