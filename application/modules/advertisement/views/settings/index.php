<?php
$num_columns	= 50;
$has_records	= isset($records) && is_array($records) && count($records) && isset($country_stores) && is_array($country_stores) && count($country_stores);

Assets::add_js('jquery.floatThead.min');
$inline = '
	var $table = $("table.table");
	$table.floatThead({
		scrollContainer: function($table){
			return $table.closest(\'.wrapper\');
		}
	});
';
Assets::add_js( $inline, 'inline' );

if (!isset($rates)) $rates = array();
?>
<div class="admin-box">
	<h3>Shipping Rates</h3>
	<ul class="nav nav-tabs" >
		<?php foreach ($countries as $country) { ?>
		<li<?php if ($current_country == $country->iso) echo ' class="active"'; ?>><?php echo anchor(SITE_AREA.'/settings/shippingrates/country/'.$country->iso, $country->name); ?></li>
		<?php } ?>
	</ul>
	<?php echo form_open($this->uri->uri_string()); ?>
	<div class="wrapper" style="max-height:450px; overflow:auto; position:relative;">
		<table class="table table-striped">
			<thead>
				<tr>					
					<th style="width:200px">Region</th>
					<?php foreach ($country_stores as $store) { ?>
					<th style="width:150px"><?php echo $store->name ?></th>
					<?php } ?>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr>					
					<td>Default</td>					
					<?php foreach ($country_stores as $store) { ?>
					<td>
						<input class="input-small" type="text" name="rate[<?= $current_country ?>][<?= $store->id?>]" value="<?php if (isset($rates[$current_country][$store->id])) echo $rates[$current_country][$store->id]['rate']; ?>" style="width:40px" />
						<select rel="<?= $store->id?>" class="input-small default_currency" name="currency[<?= $current_country?>][<?= $store->id?>]" style="width:65px">
							<?php foreach ($currencies as $currency) { ?>
							<option value="<?php echo $currency->iso ?>"<?php if ((isset($rates[$current_country][$store->id]) && $rates[$current_country][$store->id]['currency'] == $currency->iso) || (!isset($rates[$current_country][$store->id]) && $currency->default_country == $current_country)) echo ' selected';?>><?php echo $currency->iso ?></option>
							<?php } ?>
						</select>
						<input class="input-small" type="text" name="availability[<?= $current_country?>][<?= $store->id?>]" value="<?php if (isset($rates[$current_country][$store->id])) echo $rates[$current_country][$store->id]['availability']; ?>" placeholder="availability" style="width:110px;" />
					</td>
					<?php } ?>
					<td></td>
				</tr>
				<?php
				if ($has_records) :
					foreach ($records as $record) :
				?>
				<tr>					
					<td><?php echo $record->name ?></td>					
					<?php foreach ($country_stores as $store) { ?>
					<td>
						<input class="input-small" type="text" name="rate[<?= $record->id?>][<?= $store->id?>]" value="<?php if (isset($rates[$record->id][$store->id])) echo $rates[$record->id][$store->id]['rate']; ?>" style="width:40px" />
						<select rel="store_<?= $store->id?>" class="input-small currency" name="currency[<?= $record->id?>][<?= $store->id?>]" style="width:65px">
							<?php foreach ($currencies as $currency) { ?>
							<option value="<?php echo $currency->iso ?>"<?php if ((isset($rates[$record->id][$store->id]) && $rates[$record->id][$store->id]['currency'] == $currency->iso) || (!isset($rates[$record->id][$store->id]) && $currency->default_country == $current_country)) echo ' selected';?>><?php echo $currency->iso ?></option>
							<?php } ?>
						</select>
						<input class="input-small" type="text" name="availability[<?= $record->id?>][<?= $store->id?>]" value="<?php if (isset($rates[$record->id][$store->id])) echo $rates[$record->id][$store->id]['availability']; ?>" placeholder="availability" style="width:110px;" />
					</td>
					<?php } ?>
					<td></td>
				</tr>
				<?php
					endforeach;
				endif; ?>
			</tbody>
		</table>
	</div>
	<p>&nbsp;</p>
	<p><input type="submit" name="save" class="btn btn-danger" value="Save" /></p>
	<?php echo form_close(); ?>
</div>