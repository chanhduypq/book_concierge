<?php

$num_columns	= 2;
$can_delete		= true;
$can_edit		= true;
$has_records	= isset($records) && is_array($records) && count($records);

?>
<div class="admin-box">
	<h3>Featured Books | <?php echo anchor(SITE_AREA.'/settings/books/add_featured/'.$current_country, 'add new', 'class="btn btn-primary"') ?></h3>
	<ul class="nav nav-tabs" >
		<?php foreach ($countries as $country) { ?>
		<li<?php if ($current_country == $country->iso) echo ' class="active"'; ?>><?php echo anchor(SITE_AREA.'/settings/books/featured/'.$country->iso, $country->name); ?></li>
		<?php } ?>
	</ul>
	<?php echo form_open($this->uri->uri_string()); ?>
		<table class="table table-striped">
			<?php if ($has_records) : ?>
			<thead>
				<tr>
					<?php if ($has_records) : ?>
					<th class="column-check"><input class="check-all" type="checkbox" /></th>
					<?php endif;?>
					<th>&nbsp;</th>
					<th>Book Details</th>
				</tr>
			</thead>
			<tfoot>
				<?php if ($can_delete) : ?>
				<tr>
					<td colspan="<?php echo $num_columns; ?>">
						<?php echo lang('bf_with_selected'); ?>
						<input type="submit" name="delete" id="delete-me" class="btn btn-danger" value="<?php echo lang('bf_action_delete'); ?>" onclick="return confirm('<?php e(js_escape(lang('books_delete_confirm'))); ?>')" />
					</td>
				</tr>
				<?php endif; ?>
			</tfoot>
			<?php endif; ?>
			<tbody>
				<?php
				if ($has_records) :
					foreach ($records as $record) :
				?>
				<tr>
					<?php if ($can_delete) : ?>
					<td class="column-check"><input type="checkbox" name="checked[]" value="<?php echo $record->ean; ?>" /></td>
					<?php endif;?>
					<td><img src="<?php echo book_image_url($record->ean, $record->cdn_image) ?>" width="100" class="img-thumbnail" /></td>
					<td><h2><?php echo $record->name ?></h2><p><?php echo $record->author ?></p></td>
				</tr>
				<?php
					endforeach;
				else:
				?>
				<tr>
					<td colspan="<?php echo $num_columns; ?>">No records found that match your selection.</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	<?php echo form_close(); ?>
</div>