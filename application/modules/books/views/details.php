<p>&nbsp;</p>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-sm-4">
				<img class="img-responsive shadow" src="<?php echo book_image_url($details['ean'], $details['cdn_image']) ?>" alt=""><br />				
				<?php /***
                                <div class="text-center GBS">
					<script type="text/javascript" src="http://books.google.com/books/previewlib.js"></script>
					<script type="text/javascript">
					GBS_insertPreviewButtonPopup('ISBN:<?php echo $details['ean']?>');
					</script>
				</div>
                                **/ ?>
				<div class="text-center">
					<span class='st_sharethis_large' displayText='ShareThis'></span>
					<span class='st_facebook_large' displayText='Facebook'></span>
					<span class='st_twitter_large' displayText='Tweet'></span>
					<span class='st_linkedin_large' displayText='LinkedIn'></span>
					<span class='st_pinterest_large' displayText='Pinterest'></span>
					<span class='st_email_large' displayText='Email'></span>
				</div>
			</div>
			<div class="col-sm-8">
				<h1 style="margin-top:0;"><?php echo $details['name'] ?></h1>
				<?php if (!empty($details['author'])) { ?>
				<h3>By <?php echo $details['author'] ?></h3>
				<?php } ?>
				<?php if (!empty($details['description'])) { ?>
				<div id="inform_description" class="book-description more-block">
					<div class="snip" style="height: 130px; overflow: hidden; text-overflow: ellipsis;">
						<div>
							<?php echo strip_tags(html_entity_decode($details['description']), '<br><br /><p>') ?>
						</div>
					</div>
					<div style="display: block;" id="fadeGradient" class="fadeGradient"></div>
					<div><span><a href="#" id="full_description">Read More &raquo;</a></span></div>
				</div>
				<?php } ?>
				<div class="book-attributes">
				<div class="row">
					<div class="col-xs-4">
						ISBN:
					</div>
					<div class="col-xs-8">
						<?php echo $details['ean'] ?>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-4">
						Format:
					</div>
					<div class="col-xs-8">
						<?php echo !empty($details['binding']) ? $details['binding'] : 'Unknown' ?>
					</div>
				</div>
				<?php if (!empty($details['pages'])) { ?>
				<div class="row">
					<div class="col-xs-4">
						Pages:
					</div>
					<div class="col-xs-8">
						<?php echo $details['pages'] ?>
					</div>
				</div>
				<?php } ?>
				<div class="row">
					<div class="col-xs-4">
						Publisher:
					</div>
					<div class="col-xs-8">
						<?php echo !empty($details['publisher']) ? $details['publisher'] : (!empty($details['manufacturer']) ? $details['manufacturer'] :'Unknown') ?>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-4">
						Published:
					</div>
					<div class="col-xs-8">
						<?php echo !empty($details['publication']) ? $details['publication'] : 'Unknown' ?>
					</div>
				</div>
				</div>		
			</div>
		</div>
		<div class="row" style="margin-top:50px;">
			<div class="col-xs-12">
				<p style="font-size:15px; float:right; " id="loading_now_message"><?php if ($fetch_price) echo ' Prices for this book are being updated now.'; else { echo 'Prices for this book were updated '.relative_time($details['timestamp']).'.'; if (strtotime($details['timestamp']) < time()-21600) echo ' <a href="?update-price">Update Now</a>'; } ?></p>
				<div id="books_prices">
					<?php $this->load->view('partials/book_prices'); ?>
				</div>
				<?php /**
				<div id="comment_container">
					<div id="disqus_thread"></div>
				</div>
				**/ ?>
			</div>
		</div>
	</div>
	<!--<?php $this->load->view('partials/right_col') ; ?>-->
</div>
<p>&nbsp;</p>