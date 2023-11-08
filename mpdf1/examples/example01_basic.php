<?php


$html = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<title>Tax Invoice</title>
	<link rel="stylesheet" type="text/css" href="https://www.vidhyarthidarpan.com/invoice/css/style.css" />
	<link rel="stylesheet" type="text/css" href="https://www.vidhyarthidarpan.com/invoice/css/print.css" media="print" />
	<script type="text/javascript" src="https://www.vidhyarthidarpan.com/invoice/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="https://www.vidhyarthidarpan.com/invoice/js/example.js"></script>
</head>

<body>

	<div id="page-wrap">

		<textarea id="header">TAX INVOICE</textarea>
		
		<div id="identity">
		
        <div id="address">
        <b><span style="font-size:25px;">Vidhyarthi Darpan Private Limited</span></b><br><br>
		<b><span style="font-size:15px;">Corporate Address:</span></b> 07,MIIC, <br> 
		Malviya National Institute of Technology (MNIT)<br>
		Jawahar Lal Nehru Marg, Jaipur, Rajasthan 302017<br><br>
		<b><span style="font-size:15px;">Registred Address:</span></b> A-52(A), Anand Vihar<br>
		Gopal Pura By Pass, Jaipur, Rajasthan 302018</div>
            <div id="logo">
              <img id="image" src="images/logo.png" alt="logo" style="height:80px;"/>
            </div>
		</div>
		
		<div style="clear:both"></div>
		<table id="" border="0" style="width:100%; margin:10px 0;">
                <tr>
                    <td><b>Institute ID :-</b></td>
                    <td><span>VDS121120201200RPN720</span></td>
                    <td><b>Institute Name :-</b></td>
                    <td><span>Vidhyarthi Darpan School </span></td>
                </tr>
            </table>
		<div style="clear:both"></div>
		
		<div id="customer">

            <div id="customer-title"><b>Billing Details</b><br>
            	<span style="font-size:17px;">Devendra Kumar Sharma<br>
            	Jhotwara, Jaipur<br>
        		Rajasthan  302012</span>
        	</div>
            <table id="meta">
                <tr>
                    <td class="meta-head">Invoice #</td>
                    <td><span>000123</span></td>
                </tr>
                <tr>
                    <td class="meta-head">Date</td>
                    <td><span id="date">December 15, 2009</span></td>
                </tr>
            </table>
		
		</div>
		<div style="clear:both"></div>
		<table id="items" cellpadding="0">
		
		  <tr>
		      <th colspan="3">Description</th>
		      <th colspan="2">Price (Rs.)</th>
		  </tr>
		  <tr class="item-row">
		      <td class="description" colspan="3"><span>Registration + Subscription Amount</span></td>
		      <td colspan="2"><span class="price">11500.00</span></td>
		  </tr>  
		  <tr>
		      <td colspan="3" class="blank" style="border-top:1px solid #ccc;"> </td>
		      <td class="total-line">Service Charges :</td>
		      <td class=""><div id="subtotal">11500.00</div></td>
		  </tr>
		  <tr>
		      <td colspan="3" class="blank"> </td>
		      <td colspan="" class="total-line">GST<span>(18%)</span> : :</td>
		      <td class=" "><div class="">2070.00</div></td>
		  </tr>
		  <tr>
		      <td colspan="3" class="blank"> </td>
		      <td colspan="" class="total-line balance"><b>Final Payable Amount :</b></td>
		      <td class="balance"><div class="due"><b>13570.00</b></div></td>
		  </tr>
		  <tr>
		      <td colspan="3" class="blank"> </td>
		      <td colspan="" class="total-line"><span style="Color:green;font-size:17px;"><b>Received/Paid Amount :</b></span></td>
		      <td class=""><div class=""><span style="Color:green;"><b>13570.00</b></span></div></td>
		  </tr>
		  <tr>
		      <td colspan="5" class="total-line"><span style="Color:green;font-size:17px;"><b>Thirteen Thousands Five Hundrads Seventy Rupees and Zero paisa Only</b></span></td>
		  </tr>
		</table>
		<table id="items" border="0" style="width:100%; margin:10px 0;">
				<tr>
			      <th colspan="6">Payment Detail</th>
			  </tr>
                <tr class="item-row">
                    <td class="meta-head">Payment Mode</td>
                    <td colspan="5">
                    	<span style="margin-right: 40px;"><input type="checkbox" id="cash" name="cash" value="Cash"><label for="cash"> Cash</label></span>
                    	<span style="margin-right: 40px;"><input type="checkbox" id="Card" name="Card" value="Card"><label for="Card"> Credit/Debit Card</label></span>
                    	<span style="margin-right: 40px;"><input type="checkbox" id="upi" name="upi" value="upi"><label for="upi"> UPI</label></span>
                    	<span style="margin-right: 40px;"><input type="checkbox" id="Wallet" name="Wallet" value="Wallet"><label for="Wallet"> Wallet </label></span>
                    	<span style="margin-right: 40px;"><input type="checkbox" id="paytm" name="paytm" value="paytm"><label for="paytm"> Paytm</label></span>
                    	<span style="margin-right: 40px;"><input type="checkbox" id="Cheque" name="Cheque" value="Cheque"><label for="Cheque"> Cheque</label></span><br><br>
                    	<div class="form-group">
                        <input id="" name="" type="text" class="form-control" placeholder="Bank Name"  style="margin-right:30px;"/>
                        <input id="" name="" type="text" class="form-control" placeholder="Cheque No." style="margin-right:30px;" />
                        <input id="" name="" type="date" class="form-control" placeholder="Date" />
                    </div>
                    </td>
                </tr>

            </table>
		<div style="clear:both"></div>
		<div id="terms">
		  <h5>Terms and Conditions</h5>
		  <div style="border-bottom: 1px solid #ccc; padding: 0 0 8px 0; margin: 0 0 8px 0;">
		  	   <span>* </span><span>Terms and Conditions 1 </span><br>
		  	   <span>* </span><span>Terms and Conditions 2 </span><br>
		  </div>
		  
		</div>
		<div style="border-bottom: 1px solid #ccc; padding: 0 0 8px 0; margin: 0 0 8px 0;">
		  	   <span style="float:left;"><b>Helpline No. :-</b>9829211106</span> 
		  	   <span>&nbsp;&nbsp;<b>Helpline Id :-</b>info@vidhyarthidarpan.com</span> 
		  	   <span style="float:right;"><b>GST No. :-</b>08AAFCV6380G1Z1</span>
		  </div>
	
	</div>
	
</body>

</html>

';


//==============================================================
//==============================================================
//==============================================================

include("../mpdf.php");
$mpdf=new mPDF('c'); 

$mpdf->WriteHTML($html);
$mpdf->Output();
exit;

//==============================================================
//==============================================================
//==============================================================


?>