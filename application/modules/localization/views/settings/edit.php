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
	<h3>localization</h3>
	<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
		<fieldset>

			<div class="control-group <?php echo form_error('iso') ? 'error' : ''; ?>">
				<?php echo form_label('Country Code'. lang('bf_form_label_required'), 'localization_country_code', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='localization_country_code' type='text' name='localization_country_code' maxlength="2" value="<?php echo set_value('localization_country_code', isset($localization['iso']) ? $localization['iso'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('iso'); ?></span>
				</div>
			</div>

			<div class="control-group <?php echo form_error('name') ? 'error' : ''; ?>">
				<?php echo form_label('Country Name'. lang('bf_form_label_required'), 'localization_country_name', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='localization_country_name' type='text' name='localization_country_name' maxlength="100" value="<?php echo set_value('localization_country_name', isset($localization['name']) ? $localization['name'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('name'); ?></span>
				</div>
			</div>
                    
                        <div class="control-group <?php echo form_error('domain') ? 'error' : ''; ?>">
				<?php echo form_label('Domain (url)'. lang('bf_form_label_required'), 'domain', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='domain' type='text' name='domain' value="<?php echo set_value('domain', isset($localization['domain']) ? $localization['domain'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('domain'); ?></span>
				</div>
			</div>

			<div class="form-actions">
				<input type="submit" name="save" class="btn btn-primary" value="<?php echo lang('localization_action_edit'); ?>"  />
				<?php echo lang('bf_or'); ?>
				<?php echo anchor(SITE_AREA .'/settings/localization', lang('localization_cancel'), 'class="btn btn-warning"'); ?>
				
			<?php if ($this->auth->has_permission('Localization.Settings.Delete')) : ?>
				or
				<button type="submit" name="delete" class="btn btn-danger" id="delete-me" onclick="return confirm('<?php e(js_escape(lang('localization_delete_confirm'))); ?>'); ">
					<span class="icon-trash icon-white"></span>&nbsp;<?php echo lang('localization_delete_record'); ?>
				</button>
			<?php endif; ?>
			</div>
		</fieldset>
    <?php echo form_close(); ?>
</div>