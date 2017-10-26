<?php $local_currency = $this->session->userdata('currency') ? $this->session->userdata('currency') : 'USD'; ?>
<?php if (empty($item['price'])) {
		$target_url = site_url($item['id'].'/'.createSlug($item['name']));
	  } else {
	  	$target_url = $item['price']->target_url;
	  }
?>
<div class="row">
	<div class="col-xs-4"><a href="<?php echo $target_url ?>" target="_blank" class="img-thumbnail" title="<?= $item['name'] ?>"><img src="<?php echo $item['image'] ?>" alt="<?= $item['name'] ?>" class="img-responsive" /></a></div>
	<div class="col-xs-8">
		<p><strong><a href="<?php echo $target_url; ?>" target="_blank" title="<?= $item['name'] ?>"><?= substr($item['name'], 0, 42) ?></a></strong></p>
		<p>Seller: <?= is_object($item['store']) && !empty($item['store']) ? $item['store']->name : 'Unknown'; ?><br />Price: <?= is_object($item['price']) && !empty($item['price']) ? $local_currency.' '.($item['price']->currency != $local_currency ? sprintf("%1\$.2f",exchange($item['price']->price, $item['price']->currency, $local_currency)+$item['shipping']) : sprintf("%1\$.2f", $item['price']->price)+$item['shipping']) : 'N/A' ?></p>
	</div>
	<div class="delete"><a href="<?php echo site_url('cart/delete/'.$item['rowid']) ?>" class="delete_from_cart" rel="<?php echo $item['id'] ?>"><i class="glyphicon glyphicon-remove"></i></a></div>
	<?php if (is_object($item['price']) && !empty($item['price'])) { ?>
	<a class="store_url hidden" href="<?php echo $item['price']->target_url ?>" rel="<?php echo $item['store']->id.'_'.$item['id'] ?>" target="_blank">Go</a>
	<?php } ?>
</div>