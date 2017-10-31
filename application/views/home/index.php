<?php 
if(count($slideTitles)>0&&$isHomePage){?>
<div id="myCarousel" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->

    <div class="left one">&nbsp;</div>
    <!-- Wrapper for slides -->
    <div class="carousel-inner left two">
        <?php 
        for($i=0;$i<count($slideTitles);$i++){
            ?>
            <div class="item<?php if($i==0) echo ' active';?>">
                <div class="title">
                    <?php echo $slideTitles[$i];?>
                </div>
                <div style="clear: both; padding:0;"></div>
                <div class="left img">
                    <img src="<?php echo '/uploads/country/image/slide/'.$slideImages[$i] ?>" alt="">
                </div>
                <div class="left text">
                    <p>
                        <?php echo $slideContents[$i];?>                        
                    </p> 
                    <?php 
                    if(trim($slideLinks[$i])!=""){
                        $link=trim($slideLinks[$i]);
                        if(strpos($link, 'http://')===0){
                            $link= ltrim($link,'http://');
                        }
                        if(strpos($link, 'https://')===0){
                            $link= ltrim($link,'https://');
                        }
                        if(strpos($link, 'www.')===0){
                            $link= ltrim($link,'www.');
                        }
                        ?>
                        <label onclick="window.location='//<?php echo $link;?>';">Read More</label>
                    <?php 
                    }
                    ?>
                </div>
                <div style="clear: both;padding:0;"></div>
            </div>
        <?php 
        }
        ?>
    </div>
    <div class="left three">&nbsp;</div>
    <div style="clear: both;"></div>

    <ol class="carousel-indicators">
        <?php 
        for($i=0;$i<count($slideTitles);$i++){?>
        <li data-target="#myCarousel" data-slide-to="<?php echo $i;?>"<?php if($i==0) echo ' class="active"';?>></li>
        <?php 
        }
        ?>
    </ol>

</div>
<?php 
}
?>

<div class="partners clearfix">
    <div class="bgImg-partners">

        <div class="search search-style no-padding-top">
            <div class="search-wrapper">
                <div class="search-header">
                    <span class="search-header-text">Get the books you want at the lowest price shipped to your door step!</span>            
                </div>
                <?php 
                if(
                        $isHomePage
                        &&isset($leftTitle)&&trim($leftTitle)!=""
                        &&isset($leftImageCurrent)&&trim($leftImageCurrent)!=""
                        &&isset($leftAuthor)&&trim($leftAuthor)!=""
                        &&isset($rightTitle)&&trim($rightTitle)!=""
                        &&isset($rightImageCurrent)&&trim($rightImageCurrent)!=""
                        &&isset($rightAuthor)&&trim($rightAuthor)!=""
                        ){
                    if(trim($leftLink)!=''){
                        $cusorLeft='cursor: pointer;';
                        if(strpos($leftLink, 'http://')===0){
                            $leftLink= ltrim($leftLink,'http://');
                        }
                        if(strpos($leftLink, 'https://')===0){
                            $leftLink= ltrim($leftLink,'https://');
                        }
                        if(strpos($leftLink, 'www.')===0){
                            $leftLink= ltrim($leftLink,'www.');
                        }
                        $onClickLeft=' onclick="window.location=\'//'.$leftLink.'\';"';
                    }
                    else{
                        $cusorLeft = $onClickLeft = '';
                    }
                    if(trim($rightLink)!=''){
                        $cusorRight='cursor: pointer;';
                        if(strpos($rightLink, 'http://')===0){
                            $rightLink= ltrim($rightLink,'http://');
                        }
                        if(strpos($rightLink, 'https://')===0){
                            $rightLink= ltrim($rightLink,'https://');
                        }
                        if(strpos($rightLink, 'www.')===0){
                            $rightLink= ltrim($rightLink,'www.');
                        }
                        $onClickRight=' onclick="window.location=\'//'.$rightLink.'\';"';
                    }
                    else{
                        $cusorRight = $onClickRight = '';
                    }
                    ?>
                <div class="position position-left">
                    <img<?php echo $onClickLeft;?> style="width: 130px;height: auto;<?php echo $cusorLeft;?>" src="<?php echo '/uploads/country/image/'.$leftImageCurrent ?>" alt="">
                    <div><?php echo $leftTitle;?></div>
                    <div class="italic">By <?php echo $leftAuthor;?></div>
                </div>
                <div class="position position-right">
                    <img<?php echo $onClickRight;?> style="width: 130px;height: auto;<?php echo $cusorRight;?>" src="<?php echo '/uploads/country/image/'.$rightImageCurrent ?>" alt=""/>
                    <div><?php echo $rightTitle;?></div>
                    <div class="italic">By <?php echo $rightAuthor;?></div>
                </div>
                <?php
                }
                ?>
                <div class="search-form">
                    <?php echo form_open($category == 'default' ? 'books/search' : 'category/'.$category.'/search', 'method="get" id="search-form"') ?>
                        <input type="text" name="q" id="search_box" class="search-form-field" placeholder="Enter Keywords, Titles, Authors, ISBN, EAN">
                        <i class="fa fa-times"></i>
                        <button type="submit" class="search-form-btn">SEARCH</button>								
                    <?php echo form_close(); ?>     
                </div>
            </div>
        </div>

        <div class="partners-wrapper">
            <div class="partners-left">
                <img<?php if($isHomePage) echo ' class="smaller"';?> src="<?php echo Template::theme_url('images/part-1.png') ?>" alt="">
                <img<?php if($isHomePage) echo ' class="smaller"';?> src="<?php echo Template::theme_url('images/part-2.png') ?>" alt="">
                <img<?php if($isHomePage) echo ' class="smaller"';?> src="<?php echo Template::theme_url('images/part-5.png') ?>" alt="" style="margin-top: -30px; width: 80px;">
                <img<?php if($isHomePage) echo ' class="smaller"';?> src="<?php echo Template::theme_url('images/part-4.png') ?>" alt="">
                <img<?php if($isHomePage) echo ' class="smaller"';?> src="<?php echo Template::theme_url('images/part-6.png') ?>" alt="">
                <img<?php if($isHomePage) echo ' class="smaller"';?> src="<?php echo Template::theme_url('images/part-7.png') ?>" alt="">
                <img<?php if($isHomePage) echo ' class="smaller"';?> src="<?php echo Template::theme_url('images/part-8.png') ?>" alt="">
                <img<?php if($isHomePage) echo ' class="smaller"';?> src="<?php echo Template::theme_url('images/part-9.png') ?>" alt="">
                <img src="<?php echo Template::theme_url('images/part-3.png') ?>" alt="" style="width: 170px; margin-bottom: -10px;">
            </div>
            <!-- <div class="partners-right">
                
            </div> -->
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