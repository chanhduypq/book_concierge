<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$has_records	= isset($records) && is_array($records) && count($records);

if ($has_records):
?>
<div class="owl-carousel owl-theme">
    <?php $i = 0; $open = false; foreach ($records as $record) : ?>
    <div class="item">
        <a href="<?php echo site_url($record->ean.'/'.createSlug($record->name))?>" class="thumbnail" title="<?= $record->name ?>"><img src="<?php echo book_image_url($record->ean, $record->cdn_image) ?>" class="img-responsive" /></a>
    </div>
    <?php $i++; endforeach; ?>
</div>
<?php endif; ?>