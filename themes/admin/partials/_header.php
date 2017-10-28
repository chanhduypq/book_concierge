<?php
	Assets::add_css( array(
		'bootstrap.min.css',
		'bootstrap2.min.css',
		'neon-core-min.css',
		'neon-theme-min.css',
		'neon-forms-min.css',
	));
	
	Assets::add_js(array(
		'jquery-1.11.0.min.js',
		'bootstrap.min.js',
		'app.js',
	));

	/*
	if (isset($shortcut_data) && is_array($shortcut_data['shortcut_keys'])) {
		Assets::add_js($this->load->view('ui/shortcut_keys', $shortcut_data, true), 'inline');
	}
	*/
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo isset($toolbar_title) ? $toolbar_title .' : ' : ''; ?> <?php e($this->settings_lib->item('site.title')) ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex" />
	
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<?php echo Assets::css(null, true); ?>
	
	<!--[if lt IE 9]><script src="<?php echo Template::theme_url('js/ie8-responsive-file-warning.js'); ?>"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	<script language="javascript">
	<!--
		site_url = '<?php echo base_url(); ?>';
		base_url = '<?php echo site_url(); ?>';
	//-->
	</script>

</head>
<body class="page-body gray">

    <div class="page-container">
	
		<div class="sidebar-menu">

			<header class="logo-env">
		
				<!-- logo -->
				<div class="logo">
					<a class="navbar-brand" href="<?php echo site_url(SITE_AREA.'/'); ?>" style="padding: 16.5px 15px; line-height: 17px; font-size: 20px; letter-spacing: 2px;">Book Concierge</a>
				</div>
		
				<!-- logo collapse icon -->
		
				<!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
				<div class="sidebar-mobile-menu visible-xs">
					<a href="#" class="with-animation">
						<!-- add class "with-animation" to support animation -->
						<i class="glyphicon glyphicon-th-large"></i>
					</a>
				</div>
		
			</header>
		
			<ul id="main-menu" class="">
				<li<?php if (!strlen($this->uri->segment(2))) echo ' class="opened active"' ?>>
					<a href="<?php echo site_url(SITE_AREA.'/'); ?>"><span>Dashboard</span></a>
				</li>
<!--				<li<?php if ($this->uri->segment(3) == 'books') echo ' class="opened active"' ?>>
					<a href="<?php echo site_url(SITE_AREA.'/settings/books/featured'); ?>"><span>Featured Books</span></a>	
				</li>-->
				<li<?php if ($this->uri->segment(3) == 'bookstores') echo ' class="opened active"' ?>>
					<a href="<?php echo site_url(SITE_AREA.'/settings/bookstores'); ?>"><span>Bookstores</span></a>	
				</li>
<!--				<li>
					<a href="#"><span>Promotions</span></a>	
				</li>-->
				<li>
					<a href="<?php echo site_url(SITE_AREA.'/settings/shippingrates'); ?>"><span>Shipping Rates</span></a>	
				</li>
                                <li>
					<a href="<?php echo site_url(SITE_AREA.'/settings/advertisement'); ?>"><span>Advertisement</span></a>	
				</li>
				<li<?php if ($this->uri->segment(3) == 'localization') echo ' class="opened active"' ?>>
					<a href="#"><span>Localization</span></a>
					<ul>
						<li>
							<a href="<?php echo site_url(SITE_AREA.'/settings/localization'); ?>"><span>Manage Countries</span></a>
						</li>
						<li>
							<a href="<?php echo site_url(SITE_AREA.'/settings/localization/regions'); ?>"><span>Manage Regions</span></a>
						</li>
						<li>
							<a href="<?php echo site_url(SITE_AREA.'/settings/localization/currencies'); ?>"><span>Manage Currencies</span></a>
						</li>
					</ul>
				</li>
				<li<?php if ($this->uri->segment(3) == 'users') echo ' class="opened active"' ?>>
					<a href="<?php echo site_url(SITE_AREA.'/settings/users/'); ?>"><span>Users</span></a>	
					<ul>
						<li><a href="<?php echo site_url(SITE_AREA.'/settings/users/'); ?>"><span>Users</span></a></li>
						<li><a href="<?php echo site_url(SITE_AREA.'/settings/users/create'); ?>"><span>Add User</span></a></li>
					</ul>
				</li>				
				<li<?php if ($this->uri->segment(2) == 'settings' && !strlen($this->uri->segment(3))) echo ' class="opened active"' ?>>
					<a href="<?php echo site_url(SITE_AREA.'/settings/'); ?>"><span>Settings</span></a>	
				</li>
		
			</ul>
		
		</div>

        <div class="main-content">

            <div class="row">

                <!-- Profile Info and Notifications -->
                <div class="col-md-6 col-sm-8 clearfix">

                    <ul class="user-info pull-left pull-none-xsm">

                        <!-- Profile Info -->
                        <li class="profile-info dropdown">
                            <!-- add class "pull-right" if you want to place this from right -->

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?php echo Template::theme_url('images/thumb-1.png') ?>" alt="" class="img-circle" width="44" /><?php echo $current_user->display_name ?>
                            </a>

                            <ul class="dropdown-menu">

                                <!-- Reverse Caret -->
                                <li class="caret"></li>

                                <!-- Profile sub-links -->
                                <li>
                                    <a href="<?php echo site_url(SITE_AREA .'/settings/users/edit') ?>">
                                        <i class="glyphicon glyphicon-edit"></i>
                                        Edit Profile
                                    </a>
                                </li>                           

                                <li>
                                    <a href="<?php echo site_url('logout'); ?>">
                                        <i class="glyphicon glyphicon-log-out"></i>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>

                    

                </div>


                <!-- Raw Links -->
                <div class="col-md-6 col-sm-4 clearfix hidden-xs">

                    <ul class="list-inline links-list pull-right">

                        <li class="dropdown language-selector">

                            Language: &nbsp;
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-close-others="true">
                                <img src="<?php echo Template::theme_url('images/flag-uk.png') ?>" />
                            </a>                         

                        </li>

                        <li class="sep"></li>

                        <li>
                            <a href="<?php echo site_url('logout'); ?>">
								Log Out <i class="glyphicon glyphicon-share-alt"></i>
							</a>
                        </li>
                    </ul>

                </div>

            </div>

            <hr />