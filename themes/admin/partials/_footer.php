			<!-- Footer -->
            <footer class="main">

                <div class="pull-right">
                    Executed in {elapsed_time} seconds, using {memory_usage}.
                </div>

                &copy; <?php echo date('Y'); ?> Book Concierge

            </footer>
        </div>

    </div>
	
	<div id="debug"><!-- Stores the Profiler Results --></div>

	<?php echo Assets::js(); ?>
        
        <script type="text/javascript">
            jQuery(function ($){
                $("body").delegate('input[type="file"]', "change", function(){
                   j = 0;
                    for (i =$(this).val().length - 1; i > - 1; i--)
                        if ($(this).val().charAt(i) == '.')
                        {
                            j = i;
                            break;
                        }
                    ext = "";
                    for (i = j + 1; i < $(this).val().length; i++)
                        ext += $(this).val().charAt(i);
                    ext = ext.toLowerCase();
                    a = new Array();
                    a.push("gif");
                    a.push("jpg");
                    a.push("jpeg");
                    a.push("png");
                    n = a.length;
                    for (i = 0; i < n; i++)
                        if (ext == a.pop()) {
                            $(this).css('color', '#ff0000');
                            return;
                        }

                    alert('Please select the image file.');
                    name=jQuery(this).attr('name');
                    jQuery(this).replaceWith("<input type='file' name='"+jQuery(this).attr('name')+"' accept='image/*'/>");
                    
               }) ;
            });
            
        </script>
</body>
</html>
