
        </div>




    
        <!-- JAVASCRIPT FILES -->
        <script type="text/javascript">var plugin_path = '<?php echo base_url(); ?>assets/plugins/';</script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/jquery/jquery-2.2.3.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/app.js"></script>

        <!-- PAGE LEVEL SCRIPTS -->
        <script type="text/javascript">
            loadScript(plugin_path + "datatables/js/jquery.dataTables.min.js", function(){
                loadScript(plugin_path + "datatables/dataTables.bootstrap.js", function(){

                    if (jQuery().dataTable) {

                        var table = jQuery('#datatable_sample');
                        table.dataTable({ 
                            "pageLength": 50
                        }); 

                    }

                });
            });
        </script>

    </body>
</html>