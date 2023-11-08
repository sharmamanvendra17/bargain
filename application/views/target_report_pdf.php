<?php if($results) { ?>
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th style="text-align: center;">S.No.</th>
            <th style="text-align: center;">Name</th>
            <th style="text-align: center;">State Name</th> 
            <th style="text-align: center;">Target (MT)</th> 
            <th style="text-align: center;">Bargain (MT) </th>
            <?php if((isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')) { ?>
            <th style="text-align: center;">Dispatched (MT) </th>
            <?php } ?>
            <th style="text-align: center;"><?php echo (isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers') ? 'Dispatched' : 'Bargain' ?>    %</th>  
            <th style="text-align: center;">Target Visits </th>
            <th style="text-align: center;">Visited</th>
            <th style="text-align: center;">Visits %</th>                                     
        </tr>
    </thead>
    <tbody>
        <?php   
        $count = 1;
        $cur_page =1;
        if(isset($limit))
            $con_li = $limit;
        if($this->uri->segment(3)!='')
            $cur_page = $this->uri->segment(3);
        $count = ($cur_page-1)*$con_li+1;
        foreach ($results as $key => $value) { ?>
        <tr>
            <td style="text-align: center;"><?php echo $count; ?></td>
            <td style="text-align: center;" title="<?php echo $value['mobile']; ?>"><?php echo $value['employee_name']; echo ($value['joining_month']) ? ' <strong > ('.$value['joining_month'].') </strong>' : ''; ?></td>
            <td style="text-align: center;"><?php echo $value['state_name']; ?></td>
            <td style="text-align: center;"><?php echo round($value['total_target_weight'],2); ?></td>  
            <td style="text-align: center;"><?php echo round($value['bargain_total_weight'],2); ?></td>
            <?php if((isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')) { ?>
            <td style="text-align: center;">  
            <?php
                $month_post = $_POST['month'].'-'.$_POST['year'];
               $dispatched  =  dispatchtarget($month_post,$value['user_id'],$value['state_ids']);
               $dispatched_target_Acheived =  ($dispatched['total_dispateched_weight']) ? $dispatched['total_dispateched_weight'] : 0;
               echo round($dispatched_target_Acheived,2);
                ?>

            </td>
            <td style="text-align: center;"><?php $dispatched_persentage = ($value['total_target_weight']>0) ? (round(($dispatched['total_dispateched_weight']*100)/($value['total_target_weight']),2)) : 0; 
                                                    echo $dispatched_persentage; //echo round($value['per_target'],2); ?></td>

            <?php } else { ?>
            <td><?php echo round($value['per_target'],2); ?></td>
            <?php } ?>
            <td style="text-align: center;"><?php echo $value['total_target_visits']; ?></td>
            <td style="text-align: center;"><?php echo ($value['total_visited']) ? "".$value['total_visited']."" : $value['total_visited']; ?></td>
            <td style="text-align: center;"><?php echo round($value['per_target_visit'],2); ?></td>   
        </tr>
        <?php $count++;  } ?>
    </tbody> 
</table>
<?php } ?>     