<ul class="nav nav-pills">
	<li <?php echo $this->uri->segment(4) == '' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/settings/localization') ?>" id="list"><?php echo lang('localization_list'); ?></a>
	</li>
	<?php if ($this->auth->has_permission('Localization.Settings.Create')) : ?>
	<li <?php echo $this->uri->segment(4) == 'create' ? 'class="active"' : '' ?> >
		<a href="<?php echo site_url(SITE_AREA .'/settings/localization/create') ?>" id="create_new"><?php echo lang('localization_new'); ?></a>
	</li>
	<?php endif; ?>
</ul>