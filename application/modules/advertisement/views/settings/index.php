<style>
    textArea{
        height: 100px;
    }
    input[type=file]{
        margin: 0 auto;
    }
    .slide_left{
        float: left;
        width: 30%;
    }
    .border_right{
        border-right: solid black 1px;
    }
    .left_lr{
        float: left;
        width: 45%;
    }
    .error{
        width: 100%;
        text-align: center;
        font-size: 30px;
        text-transform: uppercase;
        margin-bottom: 50px;
        color: red;
    }
    p.submit{
        margin: 0 auto;
        text-align: center;
        margin-bottom: 50px;
    }
    .err{
        border: 1px solid red !important;
    }
</style>
<div class="admin-box">
    
	<h3>Advertisement</h3>
	<ul class="nav nav-tabs" >
		<?php foreach ($countries as $country) { ?>
		<li<?php if ($current_country == $country->iso) echo ' class="active"'; ?>><?php echo anchor(SITE_AREA.'/settings/advertisement/country/'.$country->iso, $country->name); ?></li>
		<?php } ?>
	</ul>
	<?php 
        $url= str_replace('advertisement', 'advertisement/save', $this->uri->uri_string());
        echo form_open_multipart($url); ?>
        <div class="wrapper" style="max-height:450px; overflow:auto; position:relative;background-color: #cccccc;margin: 0 auto;text-align: center;padding-bottom: 50px;">
            <h3 style="width: 100%;text-align: center;margin-bottom: 50px;">Slides</h3>
            
            <div class="slide_left border_right">
                <input class="input-small<?php if($slideIds[0]!=''&&trim($slideTitles[0]=='')) echo ' err';?>" placeholder="title" type="text" name="slide_title[]" value="<?php echo html_entity_decode($slideTitles[0]);?>" style="width:200px;" />
                <br>
                <input class="input-small" placeholder="link" type="text" name="read_more_link[]" value="<?php echo html_entity_decode($slideLinks[0]);?>" style="width:200px" />
                <br>
                <textarea<?php if($slideIds[0]!=''&&trim($slideContents[0])=='') echo ' class="err"';?> name="slide_content[]" placeholder="content" cols="20" rows="10"><?php echo $slideContents[0];?></textarea>
                <br>
                <?php 
                if($slideImages[0]!=''){
                ?>
                <img style="width: 100px;height: 100px;" src="<?php echo '/uploads/country/image/slide/'.$slideImages[0];?>" alt=""/>
                <?php 
                }
                ?>
                <br>
                <input<?php if($slideIds[0]!=''&&trim($slideImages[0])=='') echo ' class="err"';?> type="file" accept="image/*" name="slide_image_0"/>
            </div>
            <div class="slide_left border_right">
                <input class="input-small<?php if($slideIds[1]!=''&&trim($slideTitles[1]=='')) echo ' err';?>" placeholder="title" type="text" name="slide_title[]" value="<?php echo html_entity_decode($slideTitles[1]);?>" style="width:200px" />
                <br>
                <input class="input-small" placeholder="link" type="text" name="read_more_link[]" value="<?php echo html_entity_decode($slideLinks[1]);?>" style="width:200px" />
                <br>
                <textarea<?php if($slideIds[1]!=''&&trim($slideContents[1])=='') echo ' class="err"';?> name="slide_content[]" placeholder="content" cols="20" rows="10"><?php echo $slideContents[1];?></textarea>
                <br>
                <?php 
                if($slideImages[1]!=''){
                ?>
                <img style="width: 100px;height: 100px;" src="<?php echo '/uploads/country/image/slide/'.$slideImages[1];?>" alt=""/>
                <?php 
                }
                ?>
                <br>
                <input<?php if($slideIds[1]!=''&&trim($slideImages[1])=='') echo ' class="err"';?> type="file" accept="image/*" name="slide_image_1"/>
            </div>
            
            <div class="slide_left">
                <input class="input-small<?php if($slideIds[2]!=''&&trim($slideTitles[2]=='')) echo ' err';?>" placeholder="title" type="text" name="slide_title[]" value="<?php echo html_entity_decode($slideTitles[2]);?>" style="width:200px" />
                <br>
                <input class="input-small" placeholder="link" type="text" name="read_more_link[]" value="<?php echo html_entity_decode($slideLinks[2]);?>" style="width:200px" />
                <br>
                <textarea<?php if($slideIds[2]!=''&&trim($slideContents[2])=='') echo ' class="err"';?> name="slide_content[]" placeholder="content" cols="20" rows="10"><?php echo $slideContents[2];?></textarea>
                <br>
                <?php 
                if($slideImages[2]!=''){
                ?>
                <img style="width: 100px;height: 100px;" src="<?php echo '/uploads/country/image/slide/'.$slideImages[2];?>" alt=""/>
                <?php 
                }
                ?>
                <br>
                <input<?php if($slideIds[2]!=''&&trim($slideImages[2])=='') echo ' class="err"';?> type="file" accept="image/*" name="slide_image_2"/>
            </div>
            
            <input type="hidden" name="iso" value="<?php echo $current_country;?>">
            <input type="hidden" name="slide_image_current_0" value="<?php echo $slideImages[0];?>">
            <input type="hidden" name="slide_image_current_1" value="<?php echo $slideImages[1];?>">
            <input type="hidden" name="slide_image_current_2" value="<?php echo $slideImages[2];?>">
            
	</div>
        <div style="clear: both;"></div>
	<p>&nbsp;</p>
        <p class="submit"><input type="submit" name="save" class="btn btn-danger" value="Save" /></p>
	<?php echo form_close(); ?>
        <hr style="margin-bottom: 50px;border-top: 5px solid black;">
        <?php echo form_open_multipart($url); ?>
        <div class="wrapper" style="max-height:450px; overflow:auto; position:relative;background-color: #999999;margin: 0 auto;text-align: center;padding-bottom: 50px;">
            <h3 style="width: 100%;text-align: center;margin-bottom: 50px;">Left and Right</h3>
            
            <div class="border_right left_lr">
                <input class="input-small<?php if($leftId!=''&&trim($leftTitle)=='') echo ' err';?>" placeholder="title" type="text" name="left_title" value="<?php echo html_entity_decode($leftTitle);?>" style="width:200px" />
                <br>
                <input class="input-small<?php if($leftId!=''&&trim($leftAuthor)=='') echo ' err';?>" placeholder="author" type="text" name="left_author" value="<?php echo html_entity_decode($leftAuthor);?>" style="width:200px" />
                <br>
                <input class="input-small" placeholder="link" type="text" name="read_more_link_left" value="<?php echo html_entity_decode($leftLink);?>" style="width:200px" />
                <br>
                <?php 
                if($leftImageCurrent!=''){
                ?>
                <img style="width: 100px;height: 100px;" src="<?php echo '/uploads/country/image/'.$leftImageCurrent;?>" alt=""/>
                <?php 
                }
                ?>
                <input<?php if($leftId!=''&&trim($leftImageCurrent)=='') echo ' class="err"';?> type="file" accept="image/*" name="left_image"/>
            </div>
            <div class="left_lr">
                <input class="input-small<?php if($rightId!=''&&trim($rightTitle=='')) echo ' err';?>" placeholder="title" type="text" name="right_title" value="<?php echo html_entity_decode($rightTitle);?>" style="width:200px" />
                <br>
                <input class="input-small<?php if($rightId!=''&&trim($rightAuthor=='')) echo ' err';?>" placeholder="author" type="text" name="right_author" value="<?php echo html_entity_decode($rightAuthor);?>" style="width:200px" />
                <br>
                <input class="input-small" placeholder="link" type="text" name="read_more_link_right" value="<?php echo html_entity_decode($rightLink);?>" style="width:200px" />
                <br>
                <?php 
                if($rightImageCurrent!=''){
                ?>
                <img style="width: 100px;height: 100px;" src="<?php echo '/uploads/country/image/'.$rightImageCurrent;?>" alt=""/>
                <?php 
                }
                ?>
                <input<?php if($rightId!=''&&trim($rightImageCurrent)=='') echo ' class="err"';?> type="file" accept="image/*" name="right_image"/>
            </div>
            
            <input type="hidden" name="iso" value="<?php echo $current_country;?>">
            <input type="hidden" name="left_image_current" value="<?php echo $leftImageCurrent;?>">
            <input type="hidden" name="right_image_current" value="<?php echo $rightImageCurrent;?>">

	</div>
	<p>&nbsp;</p>
	<p class="submit"><input type="submit" name="save" class="btn btn-danger" value="Save" /></p>
	<?php echo form_close(); ?>
</div>