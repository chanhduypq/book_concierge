<?php
    Assets::add_js( 'bootstrap.min.js' );
    Assets::add_css( array('bootstrap2.min.css', 'bootstrap2-responsive.min.css'));

    $inline  = '$(".dropdown-toggle").dropdown();';
    $inline .= '$(".tooltips").tooltip();';

    Assets::add_js( $inline, 'inline' );
?>
<!doctype html>
<head>
    <meta charset="utf-8">

    <title><?php echo isset($page_title) ? $page_title .' : ' : ''; ?> <?php if (class_exists('Settings_lib')) e(settings_item('site.title')); else echo 'Bonfire'; ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <?php echo Assets::css(); ?>
	
	<style>
		body {
			background: none repeat scroll 0% 0% rgb(245, 245, 245);
			padding:0;
			margin:0;
		}

		#login {
			max-width: 300px;
			padding: 19px 29px 14px;
			margin: 100px auto 0;
			background-color: rgb(255, 255, 255);
			border: 1px solid rgb(229, 229, 229);
			border-radius: 5px 5px 5px 5px;
			box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
		}

	</style>

    <link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico">
</head>
<body>
<div class="container">
	<div class="container-fluid">
        <?php echo isset($content) ? $content : Template::content(); ?>
	</div>
</div>

<div id="debug"></div>
<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo js_path(); ?>jquery-1.7.2.min.js"><\/script>')</script>

<!-- This would be a good place to use a CDN version of jQueryUI if needed -->
<?php echo Assets::js(); ?>

</body>
</html>