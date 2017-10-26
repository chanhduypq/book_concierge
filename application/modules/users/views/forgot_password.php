<div class="row">
	<div class="col-xs-12">
		<div id="login">
			<div class="page-header">
				<h1><?php echo lang('us_reset_password'); ?></h1>
			</div>
			
			<?php if (validation_errors()) : ?>
				<div class="alert alert-error fade in">
					<?php echo validation_errors(); ?>
				</div>
			<?php endif; ?>
			
			<div class="alert alert-info fade in">
				<?php echo lang('us_reset_note'); ?>
			</div>
			
			<div class="row-fluid">
				<div class="span12">
			
			<?php echo form_open($this->uri->uri_string(), array('class' => "form-horizontal", 'autocomplete' => 'off')); ?>
			
				<div class="form-group <?php echo iif( form_error('email') , 'error'); ?>">
					<label for="email" class="col-sm-2 control-label required"><?php echo lang('bf_email'); ?></label>
					<div class="col-sm-10">
						<input class="span6 form-control" type="text" name="email" id="email" value="<?php echo set_value('email') ?>" />
					</div>
				</div>
			
				<div class="form-group">
					<label for="send" class="col-sm-2 control-label"></label>
					<div class="col-sm-10">
						<input class="btn btn-primary" type="submit" name="send" value="<?php e(lang('us_send_password')); ?>" />
					</div>
				</div>
			
			<?php echo form_close(); ?>
			
				</div>
			</div>
		</div>
	</div>
</div>