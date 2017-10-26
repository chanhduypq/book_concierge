<?php $conditions = array('new');//, 'used' ?>
<?php $local_currency = $this->session->userdata('currency') ? $this->session->userdata('currency') : 'USD'; ?>
<h3>This book is available here! Prices include shipping cost (where possible)</h3>
<?php foreach ($conditions as $condition) { $not_found = array(); if ((!isset($store_prices[$condition]) || ($fetch_price || $this->session->userdata('country') != 'US')) && $condition != 'new') continue; ?>
<div>
	<table class="table table-striped">
	  <tr>
		<th style="width:18%">Where?</th>
		<th style="width:12%" class="hidden-xs">Price</th>
		<th style="width:12%" class="hidden-xs">Delivery</th>
		<th class="hidden-xs">Availability</th>
		<th style="width:12%">Total</th>
		<th style="width:10%;"></th>
	  </tr>
	  <?php if (is_array($stores) && count($stores)) { ?>
		  <?php if ($fetch_price) { ?>
		  <?php foreach ($stores as $store) { ?>
		  <tr class="book-price" data-id = "<?php echo  $store->id ?>">
			<td><?php echo $store->name ?></td>
			<td colspan="10"><img src="<?php echo img_path(); ?>ajax-loader.gif" alt="loading" /></td>
		  </tr>
		  <?php } ?>
		  <?php } else { ?>
		  <?php if (isset($store_prices[$condition]) && is_array($store_prices[$condition]) && count($store_prices[$condition])) { ?>
			  <?php foreach ($store_prices[$condition] as $store_id => $store) { ?>
			  	  <?php if ($store->price) { unset($not_found[$store_id]); ?>
				  <tr class="book-price" data-id = "<?php echo  $store_id ?>">
				  	<td colspan="3" class="visible-xs">
						<div class="row">
							<div class="col-xs-6">
								<a class="show_price_details" href="#"><?php echo $store->name ?> <i class="glyphicon glyphicon-chevron-down" style="font-size:10px;"></i></a>
							</div>
							<div class="col-xs-3">
								<a class="show_price_details" href="#"><?php echo $local_currency ?> <?php echo sprintf("%1\$.2f",$store->total_price); ?></a>
							</div>
							<div class="col-xs-3 text-right">
								<?php if (!empty($current_user)) :?>
									<a href="<?php echo site_url('cart/add/'.$details['ean'].'/'.$store_id.'/'.$condition) ?>" class="add_<?= $details['ean'] ?> add_to_cart<?php if ($this->cart->in_cart($details['ean'])) echo ' disabled'; ?>" title="Add to Cart"><img src="<?php echo img_path();?>cart_add.png" alt="Add to Cart"  /></a>
									<a href="<?php echo $store->target_url  ?>" target="_blank" class="add_<?= $details['ean'] ?><?php if ($this->cart->in_cart($details['ean'])) echo ' disabled'; ?>" style="font-size:16px; color:#666; margin-left:15px; vertical-align:middle;" title="Direct Link"><i class="glyphicon glyphicon-chevron-right"></i></a>
								<?php else: ?>
									<a href="<?php echo $store->target_url  ?>" target="_blank" class="add_<?= $details['ean'] ?> btn btn-small btn-danger">Buy</a>
								<?php endif; ?>
							</div>
						</div>
						<div class="price_details" style="display:none;">
							<strong>Price</strong>: <?php echo $local_currency ?> <?php echo sprintf("%1\$.2f",$store->price); ?><br />
							<strong>Shipping</strong>: <?php echo ($condition == 'used') ? 'Ask Seller' : $local_currency.' '.sprintf("%1\$.2f", $store->shipping); ?><br />
							<strong>Availability</strong>: <?php echo $store->delivery ?>
						</div>
					</td>
					<td class="hidden-xs"><?php echo $store->name ?></td>
					<td class="hidden-xs"><?php echo $local_currency ?> <?php echo sprintf("%1\$.2f",$store->price); ?></td>
					<td class="hidden-xs"><?php echo ($condition == 'used') ? 'Ask Seller' : $local_currency.' '.sprintf("%1\$.2f", $store->shipping); ?></td>				
					<td class="hidden-xs"><?php echo $store->delivery ?></td>
					<td class="hidden-xs"><?php echo $local_currency ?> <?php echo sprintf("%1\$.2f",$store->total_price); ?></td>
					<td class="hidden-xs">
					<?php if (false && !empty($current_user)) :?>			
						<a href="<?php echo site_url('cart/add/'.$details['ean'].'/'.$store_id.'/'.$condition) ?>" class="add_<?= $details['ean'] ?> add_to_cart<?php if ($this->cart->in_cart($details['ean'])) echo ' disabled'; ?>" title="Add to Cart"><img src="<?php echo img_path();?>cart_add.png" alt="Add to Cart"  /></a>
						<a href="<?php echo $store->target_url  ?>" target="_blank" class="add_<?= $details['ean'] ?>" style="font-size:16px; color:#666; margin-left:15px; vertical-align:middle;" title="Direct Link"><i class="glyphicon glyphicon-chevron-right"></i></a>
					<?php else: ?>
						<a href="<?php echo $store->target_url  ?>" target="_blank" class="add_<?= $details['ean'] ?> btn btn-small btn-danger">Buy</a>
					<?php endif; ?>
					</td>
				  </tr>
				  <?php } else {
						  	$not_found[$store_id] = $store->name;
				  		}
				   ?>
			  	<?php } ?>
			  <?php if ($condition == 'new') { 
			  		  foreach ($stores as $store) {
						if (!isset($store_prices['new'][$store->id]) && !in_array($store->id, $not_found)) {
			   ?>
						  <tr class="book-price" data-id = "<?php echo  $store->id ?>">
							<td><?php echo $store->name ?></td>
							<td colspan="10"><img src="<?php echo img_path(); ?>ajax-loader.gif" alt="loading" /></td>
						  </tr>
				<?php 	}
					  }
					}
				?>
		  <?php } else $not_available = true; ?>
	  	  <?php if  (count($not_found) == count($stores)) { ?>
		  <tr>
			<td colspan="10"><strong>Sorry! We could not find this book in any of our selected bookstores.</strong></td>
		  </tr>
		  <?php } ?>
		  <?php } ?>
	  <?php } else { ?>
	  <tr>
		<td colspan="10"><strong>Sorry! There are no bookstores for your region.</strong></td>
	  </tr>
	  <?php } ?>
	</table>
</div>
<?php } ?>
<?php if ($fetch_price && is_array($stores) && count($stores)) {
		$inline = 'fetchBookPrices("'.$details['ean'].'", "books_prices", '.(int)count($stores).');';
		assets::add_js($inline, 'inline');
	}
?>