<div class="col-lg-3 visible-lg">
	<div class="shopping-cart" style="margin-bottom:15px;">
		<h1>My Cart</h1>	
		<div class="cart_contents">
		<?php if (!empty($current_user)) :?>
		<?php if (function_exists('displayCart')) displayCart('summary'); ?>
		<?php else: ?>
		<p>You are not logged in.</p><p><?php echo anchor('login', 'Log in'); ?> to add your favourite books to your cart and buy them together.</p>
		<?php endif; ?>
		</div>
	</div>
	<?php if ($_SERVER['HTTP_HOST'] != 'localhost') { ?>
	<div class="text-center">
	<a href="http://www.jdoqocy.com/click-7389342-10892643" target="_blank" onmouseover="window.status='http://www.betterworldbooks.com';return true;" onmouseout="window.status=' ';return true;">
<img src="http://www.tqlkg.com/image-7389342-10892643" width="200" height="200" alt="Buy Books. Do Good. Support Literacy Worldwide" border="0"/></a>
	</div>
	<?php } ?>
</div>