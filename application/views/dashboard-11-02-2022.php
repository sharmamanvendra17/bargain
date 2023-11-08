<?php include 'header.php'; ?>
<section id="middle">
    <header id="page-header">
        <h1>Dashboard</h1>
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong>Dashboard</strong>
                </span>
            </div>
            <div class="panel-body">
                <?php if($this->session->flashdata('err_msg')) { ?>
                <div class="alert alert-danger noborder text-center weight-400 nomargin noradius">
                    <?php echo $this->session->flashdata('err_msg'); ?>
                </div>
                <?php } ?>
                <?php if($this->session->flashdata('suc_msg')) { ?>
                <div class="alert alert-success noborder text-center weight-400 nomargin noradius">
                    <?php echo $this->session->flashdata('suc_msg'); ?>
                </div>
                <?php } ?> 
                <div class="row">
                    <div class="col-md-12"> 
                        <div id="exTab1" class="container"> 
                            <ul  class="nav nav-pills">
                                <li class="active">
                              <a  href="#1a" data-toggle="tab">15Days</a>
                                </li>
                                <li><a href="#2a" data-toggle="tab">30Days</a>
                                </li>
                                <li><a href="#3a" data-toggle="tab">More Than Month</a>
                                </li> 
                            </ul>

                                <div class="tab-content clearfix">
                                    <div class="tab-pane active" id="1a"> 
                                        <h3>Past 15 days Report</h3>
                                        <div class="row summary_report">
                                            <div class="col-md-3 summary_report_col">
                                            </div>
                                            <div class="col-md-3 summary_report_col">
                                                <strong>Number Of Bargains</strong>
                                            </div>
                                            <div class="col-md-3 summary_report_col">
                                                <strong>Weight (MT)</strong>
                                            </div>
                                        </div>
                                        <?php //echo "<pre>"; print_r($fifteendays);
                                        $tot_sum_report = $fifteendays['tot_sum_report'];
                                        $locked = $fifteendays['locked'];
                                        $sum_report = $fifteendays['sum_report'];
                                        if($tot_sum_report)
                                        {  foreach ($tot_sum_report as $key => $tot_sum_report_value) { 
                                            ?>
                                            <div class="row summary_report">
                                                <div class="col-md-3 summary_report_col">
                                                    <strong>Total</strong>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo $tot_sum_report_value['bargain_count']; ?>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo ($tot_sum_report_value['weight']) ? $tot_sum_report_value['weight'] : 0; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($locked)
                                        {  foreach ($locked as $key => $locked_value) { 
                                            ?>
                                            <div class="row summary_report" >
                                                <div class="col-md-3 summary_report_col">
                                                    <strong>Locked</strong>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo $locked_value['bargain_count']; ?>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo ($locked_value['weight']) ? $locked_value['weight'] : 0; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($sum_report)
                                        { 
                                            
                                            foreach ($sum_report as $key => $sum_report_value) { ?>

                                                <div class="row summary_report">
                                                    <div class="col-md-3 summary_report_col">
                                                        <strong><?php 
                                                        if($sum_report_value['status']==0)
                                                           echo "Pending";
                                                         elseif($sum_report_value['status']==2)
                                                           echo "Approved";
                                                         else
                                                            echo "Rejected";
                                                         ?></strong>
                                                    </div>
                                                    <div class="col-md-3 summary_report_col">
                                                        <?php echo $sum_report_value['bargain_count']; ?>
                                                    </div>
                                                    <div class="col-md-3 summary_report_col">
                                                        <?php echo $sum_report_value['weight']; ?>
                                                    </div>
                                                </div> 
                                            <?php } 
                                        }  ?>
                                    </div>
                                    <div class="tab-pane" id="2a">
                                        <h3>Past 1 Month Report</h3>
                                        <div class="row summary_report">
                                            <div class="col-md-3 summary_report_col">
                                            </div>
                                            <div class="col-md-3 summary_report_col">
                                                <strong>Number Of Bargains</strong>
                                            </div>
                                            <div class="col-md-3 summary_report_col">
                                                <strong>Weight (MT)</strong>
                                            </div>
                                        </div>
                                        <?php //echo "<pre>"; print_r($fifteendays);
                                        $tot_sum_report = $onemonth['tot_sum_report'];
                                        $locked = $onemonth['locked'];
                                        $sum_report = $onemonth['sum_report'];
                                        if($tot_sum_report)
                                        {  foreach ($tot_sum_report as $key => $tot_sum_report_value) { 
                                            ?>
                                            <div class="row summary_report">
                                                <div class="col-md-3 summary_report_col">
                                                    <strong>Total</strong>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo $tot_sum_report_value['bargain_count']; ?>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo ($tot_sum_report_value['weight']) ? $tot_sum_report_value['weight'] : 0;; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($locked)
                                        {  foreach ($locked as $key => $locked_value) { 
                                            ?>
                                            <div class="row summary_report" >
                                                <div class="col-md-3 summary_report_col">
                                                    <strong>Locked</strong>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo $locked_value['bargain_count']; ?>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo ($locked_value['weight']) ? $locked_value['weight'] : 0; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($sum_report)
                                        { 
                                            
                                            foreach ($sum_report as $key => $sum_report_value) { ?>

                                                <div class="row summary_report">
                                                    <div class="col-md-3 summary_report_col">
                                                        <strong><?php 
                                                        if($sum_report_value['status']==0)
                                                           echo "Pending";
                                                         elseif($sum_report_value['status']==2)
                                                           echo "Approved";
                                                         else
                                                            echo "Rejected";
                                                         ?></strong>
                                                    </div>
                                                    <div class="col-md-3 summary_report_col">
                                                        <?php echo $sum_report_value['bargain_count']; ?>
                                                    </div>
                                                    <div class="col-md-3 summary_report_col">
                                                        <?php echo $sum_report_value['weight']; ?>
                                                    </div>
                                                </div> 
                                            <?php } 
                                        }  ?>
                                    </div>
                                    <div class="tab-pane" id="3a">
                                        <h3>More than 1 Month Report</h3>
                                        <div class="row summary_report">
                                            <div class="col-md-3 summary_report_col">
                                            </div>
                                            <div class="col-md-3 summary_report_col">
                                                <strong>Number Of Bargains</strong>
                                            </div>
                                            <div class="col-md-3 summary_report_col">
                                                <strong>Weight (MT)</strong>
                                            </div>
                                        </div>
                                        <?php //echo "<pre>"; print_r($fifteendays);
                                        $tot_sum_report = $moremonth['tot_sum_report'];
                                        $locked = $moremonth['locked'];
                                        $sum_report = $moremonth['sum_report'];
                                        if($tot_sum_report)
                                        {  foreach ($tot_sum_report as $key => $tot_sum_report_value) { 
                                            ?>
                                            <div class="row summary_report">
                                                <div class="col-md-3 summary_report_col">
                                                    <strong>Total</strong>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo $tot_sum_report_value['bargain_count']; ?>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo ($tot_sum_report_value['weight']) ? $tot_sum_report_value['weight'] : 0;; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($locked)
                                        {  foreach ($locked as $key => $locked_value) { 
                                            ?>
                                            <div class="row summary_report" >
                                                <div class="col-md-3 summary_report_col">
                                                    <strong>Locked</strong>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo $locked_value['bargain_count']; ?>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo ($locked_value['weight']) ? $locked_value['weight'] : 0; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($sum_report)
                                        { 
                                            
                                            foreach ($sum_report as $key => $sum_report_value) { ?>

                                                <div class="row summary_report">
                                                    <div class="col-md-3 summary_report_col">
                                                        <strong><?php 
                                                        if($sum_report_value['status']==0)
                                                           echo "Pending";
                                                         elseif($sum_report_value['status']==2)
                                                           echo "Approved";
                                                         else
                                                            echo "Rejected";
                                                         ?></strong>
                                                    </div>
                                                    <div class="col-md-3 summary_report_col">
                                                        <?php echo $sum_report_value['bargain_count']; ?>
                                                    </div>
                                                    <div class="col-md-3 summary_report_col">
                                                        <?php echo $sum_report_value['weight']; ?>
                                                    </div>
                                                </div> 
                                            <?php } 
                                        }  ?>
                                    </div> 
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>

<style type="text/css">
    body {
  padding : 10px ;
  
}

#exTab2 h3 {
  color : white;
  background-color: #428bca;
  padding : 5px 15px;
}

/* remove border radius for the tab */

#exTab1 .nav-pills > li > a {
  border-radius: 0; 
    border: 1px solid #ccc;
    margin-right: 10px;
}

/* change border radius for the tab , apply corners on top*/

#exTab3 .nav-pills > li > a {
  border-radius: 4px 4px 0 0 ;
}

#exTab3 .tab-content {
  color : white;
  background-color: #428bca;
  padding : 5px 15px;
}
</style>