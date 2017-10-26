        <header class="page-header clearfix">
            <div class="container page-header-wrapper">
                <div class="page-header-logo clearfix">
                    <a href="<?php echo site_url(); ?>"><img src="<?php echo Template::theme_url('images/logo.png') ?>" class="img-responsive" alt="Book Concierge"></a>
                </div>
                <?php if (function_exists('localize_options')) localize_options(); ?>
                <nav class="nav">
                    <a class="burger" href="#">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <ul class="main-menu">
                        <li><a href="<?php echo site_url(); ?>">All <span>Books</span></a></li>
                        <li<?php if($this->uri->segment(2)=="children"){echo ' class="active"';}?>><a href="<?php echo site_url(); ?>category/children">Children's <span>Books</span></a></li>
                        <li<?php if($this->uri->segment(2)=="management"){echo ' class="active"';}?>><a href="<?php echo site_url(); ?>category/management">Management<span> Books</span></a></li>
                        <li<?php if($this->uri->segment(2)=="religion"){echo ' class="active"';}?>><a href="<?php echo site_url(); ?>category/religion">Religious &amp; <span>Inspirational Books</span></a></li>
                        <li<?php if($this->uri->segment(2)=="crime"){echo ' class="active"';}?>><a href="<?php echo site_url(); ?>category/crime">Crime &amp; <span>Mystery Books</span></a></li>
                        <!-- li><a href="#">RECENTLY VIEWED</a></li //-->
                    </ul>
                </nav>
            </div>
        </header>
	
	<?php if ($this->router->fetch_class() != 'home' || $this->router->fetch_method() != 'index') { ?>
        <div class="container info-content">
        <?php } ?>
