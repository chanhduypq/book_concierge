<?php if ($cart->total_items() == 0) { ?>
<p>You have not added any book to your cart yet.</p>
<?php } else { ?>
<p>You have <?= $this->cart->total_items() ?> book<? if ($this->cart->total_items() > 1) echo 's'; ?> in your cart.</p>
<?php if (strlen($this->session->userdata('shipping_state')) || strlen($this->session->userdata('shipping_country'))) { ?>
<p><strong>Shipping To:</strong><br /><?php if (strlen($this->session->userdata('shipping_state'))) echo $this->session->userdata('shipping_state').', '; ?><?php echo $this->session->userdata('shipping_country') ?> <?php echo anchor('users/profile', 'change'); ?></p>
<?php if (isset($ask_shipping) && $ask_shipping) { ?><div class="alert alert-danger">Price doesn't include shipping rate. Please check with Seller.</div><?php } ?>
<?php } ?>
<?php } ?>