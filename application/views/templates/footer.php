<?php 
/**
 * This view is included into all desktop full views. It contains the footer of the application.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
 
    


                
    </div><!-- ./page content container -->
      </div>
              
                <!-- ./page content -->  
         </div>  
            <!-- ./page container -->                                                    
            
            <!-- FOOTER -->
    <div class="dev-page-footer dev-page-footer-fixed">
      <div class="span4"><br />&copy; 2016</div>
     <ul class="dev-page-footer-buttons">
         <li> <center>
              <img src="<?php echo base_url();?>assets/images/logo.png" style="margin-top:-6px;">
              <b>
<?php switch ($language_code){
    case 'fr' : echo '<a class="anchor" href="http://albaraka-bank.com/fr" target="_blank">Albaraka</a>'; break;
    default : echo '<a class="anchor" href="http://albaraka-bank.com/fr" target="_blank">Albaraka</a>'; break;
} ?>
                  </b>
          </center></li>
      </ul>
      <div class="span4">&nbsp;</div>
       <ul class="dev-page-footer-controls dev-page-footer-controls-auto pull-right">
          <li><a class="dev-page-sidebar-minimize tip" title="Toggle navigation"><i class="fa fa-outdent"></i></a></li>
          <li><a class="dev-page-footer-fix tip" title="Fixed footer"><i class="fa fa-thumb-tack"></i></a></li>
          <li><a class="dev-page-footer-collapse dev-page-footer-control-stuck"><i class="fa fa-dot-circle-o"></i></a></li>
      </ul>
      </div>   <!-- ./FOOTER --> 
     
             </div>
        <!-- ./page wrapper -->
        <!-- .javascript -->
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>        
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/moment/moment.js"></script>
        
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/knob/jquery.knob.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/sparkline/jquery.sparkline.min.js"></script>
        
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
        
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/nvd3/d3.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/nvd3/nv.d3.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/nvd3/lib/stream_layers.js"></script>
        
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/waypoint/waypoints.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/plugins/counter/jquery.counterup.min.js"></script>        
                
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/dev-loaders.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/dev-layout-default.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/demo.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/dev-app.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/demo-dashboard.js"></script>
        <!-- ./javascript -->
</body>
</html>
