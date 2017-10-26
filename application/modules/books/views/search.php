		<div class="row text-center search-result-header" style="margin-bottom:25px;">
			<div class="col-lg-12">
				<!--<h2><i class="fa fa-book"></i> Results for "<?php echo $q ?>"</h2>
				<div class="line"></div>-->
				<?php if ($total_records) { ?>
				<h2><i class="fa fa-search"></i> We have found <?= $total_records ?> results for "<?php echo trim($q) ?>"</h2>
                                <p style="margin-top:15px; font-size:16px"><a href="<?php echo site_url() ?>">&laquo; Search Again</a></p>
				<?php } ?>
			</div>
		</div>
		<?php if ($total_records) { ?>
                <div class="row result" id="search-results">
			<?php $i = 0; foreach ($results['matches'] as $result) { $result = $result['attrs']; ?>
			<?php
				if (isset($result['name_2']) && !empty($result['name_2'])) $result['name'] = $result['name_2'];
				if (isset($result['author_2']) && !empty($result['author_2'])) $result['author'] = $result['author_2'];
			?>
			<?php
			if (empty($result['cdn_image'])) {
				$result['cdn_image'] = ''; //get_book_image($result['isbn']);
			}
			?>
				<div class="item" data-order="<?php echo $i ?>">
                                    <a href="<?php echo site_url($result['isbn'].'/'.createSlug($result['name']))?>" target="_blank">
                                        <img class="img-responsive img-thumbnail" src="<?php echo book_image_url($result['isbn'], $result['cdn_image']) ?>" alt="<?php echo $result['name'] ?>">
                                        <br><strong><?php echo substr($result['name'], 0, 50).(strlen($result['name'])>50 ? '...': '') ?></strong><br>
                                        ISBN: <?php echo $result['isbn'] ?>
                                    </a>
				</div>
			<?php $i++; } ?>
                    
                </div>
                <div class="row">
                        <div class="col-xs-12">
                                <div class="text-center">
                                        <?php echo $this->pagination->create_links(); ?>
                                </div>
                        </div>
                </div>
		<?php } else { ?>
		<div class="row text-center">
			<div class="col-lg-12">
				<h3>Sorry! We could not find any book matching your keyword</h3>
				<?php if (isset($suggestion)) { ?>
				<p>Did you mean "<a href="<?php echo site_url('books/search/'.urlencode($suggestion)) ?>"><?= $suggestion ?></a>"?</p>
				<?php } ?>
                                <p style="margin-bottom: 180px;">Help us to improve our range of books by letting us know the book you want and cannot find.<br>Email us with the book details at <a href="mailto:feedback@bookconcierge.co">feedback@bookconcierge.co</a>.</p>
			</div>
		</div>
		<?php } ?>