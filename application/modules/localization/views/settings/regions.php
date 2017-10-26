<?php

$num_columns	= 4;
$can_delete	= $this->auth->has_permission('Localization.Settings.Delete');
$can_edit		= $this->auth->has_permission('Localization.Settings.Edit');
$has_records	= isset($records) && is_array($records) && count($records);

?>
<div class="admin-box">
	<h3>Regions | <?php echo anchor(SITE_AREA.'/settings/localization/create_region/'.$current_country, 'add new'); ?></h3>
	<ul class="nav nav-tabs" >
		<?php foreach ($countries as $country) { ?>
		<li<?php if ($current_country == $country->iso) echo ' class="active"'; ?>><?php echo anchor(SITE_AREA.'/settings/localization/regions/'.$country->iso, $country->name); ?></li>
		<?php } ?>
	</ul>
	<?php echo form_open($this->uri->uri_string()); ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<?php if ($can_delete && $has_records) : ?>
					<th class="column-check"><input class="check-all" type="checkbox" /></th>
					<?php endif;?>
					
					<th>Region Abbrev</th>
					<th>Region Name</th>
					<th></th>
				</tr>
			</thead>
			<?php if ($has_records) : ?>
			<tfoot>
				<?php if ($can_delete) : ?>
				<tr>
					<td colspan="<?php echo $num_columns; ?>">
						<?php echo lang('bf_with_selected'); ?>
						<input type="submit" name="delete" id="delete-me" class="btn btn-danger" value="<?php echo lang('bf_action_delete'); ?>" onclick="return confirm('<?php e(js_escape(lang('localization_delete_confirm'))); ?>')" />
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
					<td class="column-check"><input type="checkbox" name="checked[]" value="<?php echo $record->id; ?>" /></td>
					<?php endif;?>
					
					<td><?php echo $record->abbrev ?></td>					
				<?php if ($can_edit) : ?>
					<td><?php echo anchor(SITE_AREA . '/settings/localization/edit_region/' . $record->id, $record->name); ?></td>
				<?php else : ?>
					<td><?php e($record->name); ?></td>
				<?php endif; ?>
				<?php if ($can_edit) : ?>
					<td><?php echo anchor(SITE_AREA . '/settings/localization/edit_region/' . $record->id, '<span class="icon-pencil"></span>'); ?></td>
				<?php endif; ?>
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