<ul class="nav nav-pills">
	<li <?php echo $this->uri->segment(4) == '' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/settings/bookstores') ?>" id="list"><?php echo lang('bookstores_list'); ?></a>
	</li>
	<?php if ($this->auth->has_permission('Bookstores.Settings.Create')) : ?>
	<li <?php echo $this->uri->segment(4) == 'create' ? 'class="active"' : '' ?> >
		<a href="<?php echo site_url(SITE_AREA .'/settings/bookstores/create') ?>" id="create_new"><?php echo lang('bookstores_new'); ?></a>
	</li>
	<?php endif; ?>
</ul>