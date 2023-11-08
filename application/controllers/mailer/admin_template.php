<?php $msg = '<html>
<head>
<title>Mailer</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body style="margin:0px; padding:0px;">
<div style="margin: 0px; padding: 0px; width: 100%; background:#eef0f6;">
  <center style="padding:0px 20px; margin:0px;">
    <table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:0px; margin:0px;  padding:0px;  font-family:arial;">
      <tr>
        <td colspan="3" style="margin:0px; padding:0 0 0 0;  text-align:left" valign="top" >
         <img src="'.$base_url.'mailer/image/top1.jpg" width="800" height="138" alt="Ajay Data" title="Ajay Data" style="border:0px; padding:0px; margin:0px; display:block;">   
        
       </td>
      </tr>

        <tr>
        <td colspan="3"  style="margin:0px; padding:0 0 0 0;  text-align:left" valign="top" >
         <img src="'.$base_url.'mailer/image/top2.jpg" width="800" height="175" alt="Appointment Booked" title="Appointment Booked" style="border:0px; padding:0px; margin:0px; display:block;">   
        
       </td>
      </tr>

      <tr>
        <td width="70" height="400px" style="margin:0px; padding:0 0 0 0;  text-align:left" valign="top" >
         <img src="'.$base_url.'mailer/image/side-left.jpg"   alt="Sidebar" title="Sidebar" style="width:70px; border:0px; height:400px; padding:0px; margin:0px; ">   
        
       </td>
         <td width="658" style="margin:0px; padding:0 0 0 0; text-align:center; background:#eef0f5;" valign="top">
            <table width="520" border="0" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:0px; margin:0px auto;  padding:0px; font-family:arial;">
              <tr>
                <td style="margin:0px; padding:0 0 0 0;  text-align:left" valign="top" >
                     <h1  style="margin:30px 0px 15px 0px; padding:0 0 0 0;  text-align:left; font-weight:bold; font-size:20px; color:#2873f6;">A new appointment has been scheduled for you.</h1>
                     <h2 style="margin:0px 0px 15px 0px; padding:0 0 0 0;  text-align:left; font-weight:bold; font-size:16px; color:#202020;">Following are the details:</h2>
                </td>
              </tr>
            </table>

             <table width="520" border="0" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:0px; margin:0px auto; background:#ffffff; border:1px dashed #7f7fff;  padding:0px; font-family:arial;">
              <tr>
                <td  width="120" style="margin:0px; padding:15px 20px 5px; font-size:14px;  text-align:left; font-weight:bold;" valign="top">
                    Full Name
                </td>
                <td   width="20" style="margin:0px; padding:15px 5px 5px; text-align:left" valign="top" >
                   :
                </td>
                <td  style="margin:0px; padding:15px 5px 5px;  text-align:left" valign="top" >
                    '.$name.'
                </td>
              </tr>

            <tr>
                <td  width="120" style="margin:0px; padding:15px 20px 5px; font-size:14px;  text-align:left; font-weight:bold;" valign="top">
                    E-mail  
                </td>
                <td   width="20" style="margin:0px; padding:15px 5px 5px; text-align:left" valign="top" >
                   :
                </td>
                <td  style="margin:0px; padding:15px 5px 5px;  text-align:left" valign="top" >
                   '.$email.'
                </td>
              </tr>
               <tr>
                <td  width="120" style="margin:0px; padding:15px 20px 5px; font-size:14px;  text-align:left; font-weight:bold;" valign="top">
                Phone
                </td>
                <td   width="20" style="margin:0px; padding:15px 5px 5px; text-align:left" valign="top" >
                   :
                </td>
                <td  style="margin:0px; padding:15px 5px 5px;  text-align:left" valign="top" >
                  '.$phone.'
                </td>
              </tr>




               <tr>
                <td  width="120" style="margin:0px; padding:15px 20px 5px; font-size:14px;  text-align:left; font-weight:bold;" valign="top">
                  Date
                </td>
                <td   width="20" style="margin:0px; padding:15px 5px 5px; text-align:left" valign="top" >
                   :
                </td>
                <td  style="margin:0px; padding:15px 5px 5px; font-weight:bold; color:#2873f6;  text-align:left" valign="top" >
                   '.$booking_date.'
                </td>
              </tr>




              <tr>
                <td  width="120" style="margin:0px; padding:15px 20px 5px; font-size:14px;  text-align:left; font-weight:bold;" valign="top">
                  Time Slot
                </td>
                <td   width="20" style="margin:0px; padding:15px 5px 5px; text-align:left" valign="top" >
                   :
                </td>
                <td  style="margin:0px; padding:15px 5px 5px; font-weight:bold; color:#2873f6;  text-align:left" valign="top" >
                  '.$slot_time.'
                </td>
              </tr>



               <tr>
                <td  width="120" style="margin:0px; padding:15px 20px 5px; font-size:14px;  text-align:left; font-weight:bold;" valign="top">
                    Category
                </td>
                <td   width="20" style="margin:0px; padding:15px 5px 5px; text-align:left" valign="top" >
                   :
                </td>
                <td  style="margin:0px; padding:15px 5px 5px;  text-align:left" valign="top" >
                    '.$category.'
                </td>
              </tr>
              <tr>
                <td colspan="3" style="margin:0px; padding:15px 20px 25px; font-size:14px;  text-align:left;" valign="top">
                  '.$message.'.
                </td>
              </tr>
            </table>
         

          
          
        
       </td>

         <td width="72" height="400px" style="margin:0px; padding:0 0 0 0;  text-align:left" valign="top" >
         <img src="'.$base_url.'mailer/image/side-right.jpg"  alt="Sidebar" title="Sidebar" style="border:0px; height:400px; width:72px; padding:0px; margin:0px; ">   
        
       </td>
      </tr>


    <tr>
        <td colspan="3" style="margin:0px; padding:0 0 0 0;  text-align:left" valign="top" >
       
          <img src="'.$base_url.'mailer/image/bottom1.jpg" width="800" height="88" alt="Footer" title="Footer" style="border:0px; padding:0px; margin:0px; display:block;">  
        </td>
      </tr>
    
      
    </table>
  </center>
</div>
</body>
</html>
'; ?>