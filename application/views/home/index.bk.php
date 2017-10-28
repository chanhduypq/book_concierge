<div class="search">
    <div class="search-wrapper">
        <div class="search-header">
            <span class="search-header-text">Get the books you want at the lowest price shipped to your door step!</span>            
        </div>
        <div class="search-form">
            <?php echo form_open($category == 'default' ? 'books/search' : 'category/'.$category.'/search', 'method="get" id="search-form"') ?>
                <input type="text" name="q" id="search_box" class="search-form-field" placeholder="Enter Keywords, Titles, Authors, ISBN, EAN">
                <i class="fa fa-times"></i>
                <button type="submit" class="search-form-btn">SEARCH</button>								
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="partners clearfix">
    <div class="partners-wrapper">
        <div class="partners-left">
            <img src="<?php echo Template::theme_url('images/part-1.png') ?>" alt="">
            <img src="<?php echo Template::theme_url('images/part-2.png') ?>" alt="">
            <img src="<?php echo Template::theme_url('images/part-3.png') ?>" alt="">
            <img src="<?php echo Template::theme_url('images/part-4.png') ?>" alt="">
            <img src="<?php echo Template::theme_url('images/part-6.png') ?>" alt="">
            <img src="<?php echo Template::theme_url('images/part-7.png') ?>" alt="">
            <img src="<?php echo Template::theme_url('images/part-8.png') ?>" alt="">
            <img src="<?php echo Template::theme_url('images/part-9.png') ?>" alt="">
        </div>
        <div class="partners-right">
            <img src="<?php echo Template::theme_url('images/part-5.png') ?>" alt="">
        </div>
    </div>
</div>

<div class="bestsellers">
    <div class="bestsellers-wrapper"> 
        <?php $categories = array('children'=>'Children', 'management'=>'Management', 'religion'=>'Religion &amp; Inspiration', 'crime'=>'Crime &amp; Mystery'); ?>
        <h2>Top 10 <?php echo empty($category) ? "" : ucwords($categories[$category]); ?> Bestsellers on Amazon</h2>
        <?php if (function_exists('show_featured')) show_featured(10, $category); ?>
    </div>
</div>