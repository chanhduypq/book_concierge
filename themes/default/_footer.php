<?php if ($this->router->fetch_class() != 'home' || $this->router->fetch_method() != 'index') { ?>
    </div>
<?php } ?>
</div>

<?php if (!isset($show) || $show == true) : ?>
    <!-- Footer Content -->
   <footer class="page-footer">
        <div class="page-footer-wrapper">
            <span> &copy; <?php echo date('Y') ?> First Capital Asia Investment Ltd.<br><br>Use of this site constitutes acceptance of <a href="<?php echo site_url(); ?>useragreement">our User Agreement</a> (effective January 1, 2017).  The material on this site may not be reproduced, distributed, transmitted, cached or otherwise used, except with the prior written permission of First Capital Asia Investment Ltd. For any inquiries, please contact <a href="mailto:feedback@bookconcierge.co">feedback@bookconcierge.co</a>. </span>
        </div>
    </footer>
    <!-- Footer Content -->
<?php endif; ?>


<div id="page_loading_container">
    <div id="page_loading"><img src="<?php echo Template::theme_url('images/ajax-loader.gif') ?>" /></div>
</div>
<div id="debug"></div>

</div>
<?php echo Assets::js(); ?>
<script type="text/javascript">
<!--
    NProgress.start();
    $(window).load(function () {
        NProgress.done();
        $('.page_content').fadeIn(function () {
            box_height = $('.snip div').height();
            if (box_height < 130) {
                $('#full_description').hide();
                $('#fadeGradient').hide();
            }

            if ($(window).height() < 600 || $('.body').height() + 175 > $(window).height()) {
                $('#footer').css('position', 'static');
            }
        });
    });
    
    $(window).resize(function() {
        if ($(window).height() < 600 || $('.body').height() + 175 > $(window).height()) {
            $('#footer').css('position', 'static');
        } else {
            $('#footer').css('position', 'absolute');
        }
    })

    $(document).ready(function () {
        $('.agreement_list a').click(function(){
            $('.agreement_list a').removeClass('active');
            $(this).addClass('active');
            var current_tab = $(this).attr('data-tab-content');
            // $('.agreement_content li').removeClass('active');
            $('.agreement_content > li').hide();
            $('.agreement_content > li').each(function(){
                    if($(this).attr('id')==current_tab){
                            // $(this).addClass('active');
                            $(this).fadeIn();
                    }
            });

            return false;
        });
    
        $('#search-form').submit(function () {
            $('.page_content').fadeOut(function () {
                NProgress.start();
            });
        });
        
        $('.burger').click(function(){
            $('.main-menu').slideToggle();
        });
        
        $("#search-results").mpmansory({
            childrenClass: '',
            breakpoints: {
                lg: 2,
                md: 2,
                sm: 4,
                xs: 6
            },
            distributeBy: {
              attr: 'data-order',
              attrOrder: 'asc',
              order: false,
              height: false
            },
            onload: function ( items ) {
              return true;
            }
        });
    });
//-->
</script>

</body>
</html>