<?php $local_currency = $this->session->userdata('currency') ? $this->session->userdata('currency') : 'USD'; ?>
<hr />
<h3 class="text-center">Cart Total: <?php echo $local_currency.' '.sprintf("%1\$.2f",$total); ?></h3>
<hr />
<div class="text-right">
	<a class="btn btn-danger checkout" href="#">Checkout</a>
</div>