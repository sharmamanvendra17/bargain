<?php



$html = '<div style="background-color:#CCFFFF;">
These images <img src="img1.png" style="vertical-align: middle;" />
are <img src="img2.png" style="vertical-align: middle;" />
<b>middle</b> <img src="img3.png" style="vertical-align: middle;" />
aligned <img src="img5.png" style="vertical-align: bottom;" />
</div>

';
//==============================================================
//==============================================================
//==============================================================
include("../mpdf.php");

$mpdf=new mPDF('c'); 
$mpdf->showImageErrors = true;
$mpdf->WriteHTML($html);

$mpdf->Output();
exit;
//==============================================================
//==============================================================
//==============================================================
//==============================================================
//==============================================================


?>