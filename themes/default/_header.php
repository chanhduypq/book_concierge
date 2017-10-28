<?php
    Assets::add_js( array('jquery-1.11.0.min.js', 'jquery-ui-1.10.4.custom.min.js', 'bootstrap.min.js', 'jquery.smooth-scroll.min.js', 'nprogress.js', 'mp.mansory.js', 'owl.carousel.min.js', 'main.js' ) );
    Assets::add_css( array('bootstrap.min.css', 'bootstrap-col-height.css', 'jquery-ui-1.10.3.custom.min.css', 'font-awesome/css/font-awesome.css', 'nprogress.css', 'normalize.css', 'owl/owl.carousel.min.css', 'owl/owl.theme.default.min.css','styles.css','slide.css','left_right.css'));
    
    //'ProximaNova-Bold/styles.css', 'ProximaNova-Extrabld/styles.css','ProximaNova-Black/styles.css'
?>
<!DOCTYPE html>
<!--[if IE 8]>			<html class="ie ie8"> <![endif]-->
<!--[if IE 9]>			<html class="ie ie9"> <![endif]-->
<!--[if gt IE 9]><!-->
<html><!--<![endif]-->
<head>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        <?php
        switch($_SERVER[HTTP_HOST]) {
            case 'bookconcierge.my':
            case 'bookconcierge.com.my':
                echo "ga('create', 'UA-68652258-5', 'auto');";
                break;
            case 'bookconcierge.hk':
                echo " ga('create', 'UA-68652258-4', 'auto');";
                break;
            case 'bookconcierge.sg':
            case 'bookconcierge.com.sg':
                echo "ga('create', 'UA-68652258-6', 'auto');";
                break;
            default:
                echo "ga('create', 'UA-68652258-3', 'auto');";
                break;
        }
        ?>        
        ga('send', 'pageview');
    </script>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="google-site-verification" content="GZFvOOF4RLRjDNUbVkOXIMmDGBvm1njulDwOLPFHL-M" />
    <title><?php echo isset($page_title) ? $page_title .' : ' : ''; ?> <?php echo 'Book Concierge'; ?></title>

    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700|Libre+Baskerville:400,700" rel="stylesheet">
    
    <!-- CSS -->
    <?php echo Assets::css(); ?>

    <script language="javascript">
    <!--
    var base_url = '<?php echo trim(site_url(), '/'); ?>';
    //-->
    </script>
    

    <!--[if lt IE 9]>
        <script src="http://css3-mediaqueries-js.googlecode.com/files/css3-mediaqueries.js"></script>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>
<body<?php if ($this->router->fetch_class() != 'home' || $this->router->fetch_method() != 'index')  echo ' class="inner-page"'; ?>>
    <div class="page_content" style="display:none;">
        <div class="body">
            <!-- Top Content -->