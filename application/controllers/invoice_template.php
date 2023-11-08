<?php $message = "";
    $message .=  '<body style="background:#f2f2f2">
<table style="border:none; cellpadding:0px; margin:20px auto; padding:0px; border-collapse:collapse; width:720px; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px;  border:1px solid #cccccc; background:#ffffff; box-shadow:0 4px 25px rgba(0, 0, 0, 0.15)">
  <tr>
    <td style="border:none; cellpadding:0px; margin:0px auto;  padding:10px 20px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; background:#000000; color:#ffffff; text-align:center; font-weight:bold; font-size:18px;"> INVOICE </td>
  </tr>
  <tr>
    <td style="border:none; cellpadding:0px; margin:0px auto;  padding:20px 20px 15px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:center; border-bottom:1px solid #cccccc"><img src="http://ashokaoils.com/images/logo-dark.png" alt="Ashoka Oils" title="Ashoka Oils" width="120px"></td>
  </tr>
  <tr>
    <td style="border:none; cellpadding:0px; margin:0px auto;  padding:20px 20px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left;"><table style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; width:680px; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px;">
        <tr>
          <td style="border:none; cellpadding:0px; margin:0px auto; vertical-align:middle; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; width:350px"><span> <strong>Sold By:</strong><br>
            Shree Hari Agro Industries Ltd.<br>
            Data Infosys Limited, Station Road Durgapura <br>
            Jaipur Rajasthan, 302018 </span> <br>
            <br>
            <br></td>
          <td style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:right; width:330px;"><span> <strong>Billing Address:</strong><br>
            <span>'.$firstname.' '.$lastname.'<br>'.$address.'<br>'.$address_2.'<br>'.$city_name.','.$state_name.','.$postcode.'<br>'.$phone.'</span>
            <br>
            <br></td>
        </tr>
        <tr>
          <td valign="top" style="border:none; cellpadding:0px; margin:0px auto; vertical-align:middle; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; width:350px"><span> <strong>PAN No:</strong> AADCS7756H<br>
            <strong>GST Registration No: </strong>08AADCS7756H1ZY <br>
            <strong>Order Number : </strong>'.$order_id_x.'</span> <br>
            <br></td>
          <td valign="top" style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:right; width:330px;"><span> <strong>Invoice Number:</strong> '.$invoice_number_x.'<br>
            <strong>Invoice Date: </strong>'.$date.'<br>
            <strong>Transaction Id : </strong>'.$TxnId.'</span>
            <br>
            <br></td>
        </tr>
      </table>
      <table style="border:none; cellpadding:0px; margin:20px auto; padding:0px; border-collapse:collapse; width:680px; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px;">
                  <tr>
                     <td style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:right; width:680px;">
                        <table style="border:1px solid #cccccc; cellpadding:0px; margin:0px; padding:0px; border-collapse:collapse; width:100%;">
                           <tr style="background:f8f8f8">
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; background:#f8f8f8;"><span>Item</span></th>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; background:#f8f8f8;"><span>Price</span></th>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; background:#f8f8f8;">Qty</th>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; width:65px; background:#f8f8f8;">Packaging</th>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; background:#f8f8f8;">Total</th>
                           </tr>'.$item_msg.'</table>
                     </td>
                  </tr>
               </table>
               <table style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; width:680px; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px;">
                  <tr>
                     <td style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; width:350px">&nbsp;
                     </td>
                     <td style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:right; width:330px;">
                        <table style="border:1px solid #cccccc; cellpadding:0px; margin:0px; padding:0px; border-collapse:collapse; width:100%;">
                           <tr>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; width:150px"><span>Sub Total</span></th>
                              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc;"><span>Rs. '.$subtotal.'</span></td>
                           </tr>'; 
                           $message .=  '<tr>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left;">Shipping </th>
                              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc;"><span>Rs '.$shipping.'</span></td>
                           </tr>'; 
                          if($state==29)
                          {
                             $message .=  '<tr>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left;">SGST (2.5%)</th>
                              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc;"><span>Rs '.round(($gst/2),2).'</span></td>
                            </tr>'; 
                            $message .=  '<tr>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left;">CGST (2.5%)</th>
                              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc;"><span>Rs '.round(($gst/2),2).'</span></td>
                            </tr>'; 
                          }
                          else
                          {
                            $message .=  '<tr>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left;">IGST (5%)</th>
                              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc;"><span>Rs '.$gst.'</span></td>
                            </tr>'; 
                          }
                          $message .=  '<tr>
                              <th style="border:none; cellpadding:0px; margin:0px auto; padding:5px 10px; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left;">Total Amount (Round Off)</th>
                              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc;"><span>Rs '.round($grand_total).'</span></td>
                           </tr></table>
                     </td>
                  </tr>';
                   $message .=  '<br/><tr>
                    <td style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; width:350px"><b>Amount in words : '.$amount_in_words.' only </b></td>
                    <td style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:right; width:330px;">&nbsp;</td>
                  </tr>';

                  if($pick_up_address) { 
                  $message .=  '<br/><br/><tr>
                    <td style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; width:350px"><b>Note - Self Pick up  '.$pick_up_address.'
                     </b></td>
                    <td style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:right; width:330px;">&nbsp;</td>
                  </tr>';
                  }
                  
                $message .=  '</table>
      <br>
      <br>
      <br>
      </td>
  </tr>
  
  <tr>
        <td style="border:none; cellpadding:0px; margin:0px auto;  padding:20px 20px 15px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:right; border-bottom:1px solid #cccccc">
        <table style="border:none; cellpadding:0px; margin:0px auto; padding:0px; border-collapse:collapse; width:100%; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px;">
          <tr>
            <td style="border:none; cellpadding:0px; margin:0px auto;  padding:20px 20px 15px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:12px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc; width:320px; ">
                      <span>
                        Note :  
                        <ol>
                          <li>Recieved the above mentioned articles in good order and condition.</li>
                          <li>Subject to jaipur jurisdictiononly.</li>
                        </ol>
                      </span>
                    </td>
                    <td style="border:none; cellpadding:0px; margin:0px auto;  padding:20px 20px 15px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:right; border-bottom:1px solid #cccccc;">
                      <span><strong>For Shree Hari Agro Industries Ltd.</strong></span><br>
                      <img src="https://www.ashokaoils.com/buy/assets/images/sign.jpg" alt="iPragatii" style="margin-top:10px; margin-bottom:10px" title="iPragatii" width="120px"><br>
                      
                       <span><strong>Authorized Signatory</strong></span><br>
                       <span style="text-align: right; font-size: 10px; font-style: initial;"><a href="https://wwww.pragatii.in/">https://wwww.pragatii.in</a></span>
                  </td>
                 </tr>
              </table>
                </td>
                </tr>
</table>
</body>';
?>
