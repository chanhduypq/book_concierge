<?php
$validation_errors = validation_errors();
$errorClass = ' error';
$controlClass = 'span6';
$fieldData = array(
    'errorClass' => $errorClass,
    'controlClass' => $controlClass,
);
?>
<p>&nbsp;</p>
<?php if (isset($hybrid_auth_providers)): ?>
<div class="row">
	<div class="col-sm-6">
<?php endif; ?>
		<section id="register">
			<h1 class="page-header"><?php echo lang('us_sign_up'); ?></h1>
			<p>
				<?php echo lang('us_already_registered'); ?>
				<?php echo anchor(LOGIN_URL, lang('bf_action_login')); ?>
			</p>
			<?php if ($validation_errors) : ?>
				<div class="alert alert-error fade in">
					<?php echo $validation_errors; ?>
				</div>
			<?php endif; ?>
		
			<div class="alert alert-info fade in">
				<h4 class="alert-heading"><?php echo lang('bf_required_note'); ?></h4>
				<?php
				if (isset($password_hints))
				{
					echo $password_hints;
				}
				?>
			</div>
			<div class="col-xs-12">
				<?php echo form_open( site_url(REGISTER_URL), array('class' => "", 'autocomplete' => 'off')); ?>
					<?php Template::block('user_fields', 'user_fields', $fieldData); ?>
					<?php
					// Allow modules to render custom fields
					Events::trigger('render_user_form');
					?>
					<!-- Start of User Meta -->
					<?php $this->load->view('users/user_meta', array('frontend_only' => true)); ?>
					<!-- End of User Meta -->
					<div class="control-group">
						<div class="controls">
							<input class="btn btn-primary" type="submit" name="register" id="submit" value="<?php echo lang('us_register'); ?>"  />
						</div>
					</div>
				<?php echo form_close(); ?>				
				<p>&nbsp;</p>
			</div>
		</section>
<?php if (isset($hybrid_auth_providers)): ?>
	</div>
	<div class="col-sm-6 pull-right">
		<h1 class="page-header">Or you can connect with</h1>
		
		<div class="box">
			<?php foreach ($hybrid_auth_providers as $provider => $hy_values): ?>
				<?php
				if ($hy_values['enabled'] == TRUE)
				{
					echo '<p>'.anchor(site_url('users/oAuth') . '/' . $provider, assets::image(img_path().str_replace(' ', '-', strtolower($provider)).'.png', array('alt'=>$provider)), 'class="link_label"').'</p>';
				}
				?>
		<?php endforeach; ?>
		</div>				
	</div>
</div>
<?php endif; ?>