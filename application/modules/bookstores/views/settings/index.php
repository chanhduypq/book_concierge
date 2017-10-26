<?php

$num_columns	= 2;
$can_delete	= $this->auth->has_permission('Bookstores.Settings.Delete');
$can_edit		= $this->auth->has_permission('Bookstores.Settings.Edit');
$has_records	= isset($records) && is_array($records) && count($records);

?>
<div class="admin-box">
	<h3>Bookstores</h3>
	<ul class="nav nav-tabs" >
		<?php foreach ($countries as $country) { ?>
		<li<?php if ($current_country == $country->iso) echo ' class="active"'; ?>><?php echo anchor(SITE_AREA.'/settings/bookstores/country/'.$country->iso, $country->name); ?></li>
		<?php } ?>
	</ul>
	<?php echo form_open($this->uri->uri_string()); ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<?php if ($has_records) : ?>
					<th class="column-check"><input class="check-all" type="checkbox" /></th>
					<?php endif;?>
					
					<th>Bookstores</th>
				</tr>
			</thead>
			<?php if ($has_records) : ?>
			<tfoot>
				<tr>
					<td colspan="<?php echo $num_columns; ?>">
						<input type="submit" name="save" class="btn btn-danger" value="Save" />
					</td>
				</tr>
			</tfoot>
			<?php endif; ?>
			<tbody>
				<?php
				if ($has_records) :
					foreach ($records as $record) :
				?>
				<tr>
					<td class="column-check"><input type="checkbox" name="checked[]" value="<?php echo $record->id; ?>"<?php if (search_std_object($country_stores, $record->id)) echo ' checked="checked"';?> /></td>
					<td><?php e($record->name); ?></td>
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