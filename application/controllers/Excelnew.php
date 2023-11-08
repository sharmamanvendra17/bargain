<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excelnew extends CI_Controller {

    function __construct()
    { 
        parent::__construct();
        $this->load->library(array('session','user_agent'));
        $this->load->helper(array(
            'form',
            'url',
            'common'));
        $this->load->library('form_validation');
        $this->load->library('pagination'); 
        $this->load->model(array('category_model','booking_model'));      
                     
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        //if($admin_role!='admin')
            //redirect('/');
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();
        $admin_info = $this->session->userdata('admin');  
 
    }

    public function index()
    {
        // create file name 
        $fileName = 'report_folder/Report-'.date("d-m-Y").'.xlsx';  
        // load excel library
        $this->load->library('excel');
        $categories = $this->category_model->GetCategories();
        $distinct_categories = $this->category_model->GetCategories1();
        $excel_report = $this->session->userdata('excel_report'); 
        $search_report_data = $this->session->userdata('search__report_data'); 
        //echo "<pre>"; print_r($products); die;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
        $i = 1;
        
        $total_weight_ordered = 0;

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );       


        $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(40);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(40);
        $objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(30);
        //$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);


        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );


        $total_weight_ordered = $excel_report['total_weight'];
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Grand Total ( Booking )');
        $objPHPExcel->getActiveSheet()->SetCellValue('B6', round($total_weight_ordered, 2));
        $objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Rates Per Kg. Ex.( Loose)');  


        $objPHPExcel->getActiveSheet()->mergeCells('D2:G2');
        $objPHPExcel->getActiveSheet()->setCellValue('D2','M/s Shree Hari Agro Industries Ltd. - Jaipur');
        $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold( true );
        
        $ms = 'Daily  Booking Sheet Report  From   '.$search_report_data['booking_date_from'].' to '.$search_report_data['booking_date_to'];
        $objPHPExcel->getActiveSheet()->mergeCells('D3:G3');
        $objPHPExcel->getActiveSheet()->setCellValue('D3',$ms);

         

        

        $col = 'C';
        $header_data = $excel_report['header'];
        foreach ($header_data as $key => $value) {
            $i = 5;
            if($key!='vanaspati_average')
            {
                $objPHPExcel->getActiveSheet()->getStyle($col.$i)->applyFromArray($styleArray1);
                $objPHPExcel->getActiveSheet()->SetCellValue($col.$i++,  str_replace("_"," ",$key));
                $objPHPExcel->getActiveSheet()->getStyle($col.$i)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->SetCellValue($col.$i++, round($value['weight'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue($col.$i++, round($value['rate'], 2));
                $col++;
            }
        }
        $vanaspati_average = $header_data['vanaspati_average'];
        $objPHPExcel->getActiveSheet()->setCellValue('C8','VP AVG Rate');
        $objPHPExcel->getActiveSheet()->setCellValue('D8',round($vanaspati_average['rate'], 2));
        $objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($styleArray2);
        $objPHPExcel->getActiveSheet()->getStyle('D8')->applyFromArray($styleArray2);

        $objPHPExcel->getActiveSheet()->mergeCells('C4:D4');
        $objPHPExcel->getActiveSheet()->setCellValue('C4',round($vanaspati_average['weight'], 2));
        
        $party_id = $search_report_data['party'];
        $brand_id = $search_report_data['brand'];
        $category_id = $search_report_data['category'];
        $product_id = $search_report_data['product'];
        $booking_date_from = $search_report_data['booking_date_from'];
        $booking_date_to = $search_report_data['booking_date_to'];

        $bookings = $this->booking_model->GetReport($party_id,$brand_id,$category_id,$product_id,$booking_date_from,$booking_date_to);


        //echo "<pre>"; print_r($bookings); die;

        $objPHPExcel->getActiveSheet()->SetCellValue('B10', 'S.No.');
        $objPHPExcel->getActiveSheet()->SetCellValue('C10', 'Bargain No');
        $objPHPExcel->getActiveSheet()->SetCellValue('D10', 'Party Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('E10', 'Place');
        $objPHPExcel->getActiveSheet()->SetCellValue('F10', 'Brand Name'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G10', 'Category Name'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H10', 'Product Name'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I10', 'Quantity'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J10', 'Rate(Without ins.)'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K10', 'Rate '); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L10', 'Rate(FOR With ins.)');
        $objPHPExcel->getActiveSheet()->SetCellValue('M10', 'Total'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N10', 'Date'); 
         



        $objPHPExcel->getActiveSheet()->getStyle('A10:N10')->applyFromArray($styleArray1);
        $product_reports = array();
        if($bookings)
        {
            $col = 'B';
            $serial=11;
            $sequence =1; 
            $total_qty = 0;
            $total_amt = 0;
            foreach ($bookings as $key => $booking) {

                $rate_without_insurance = $booking['rate'];



                if($booking['insurance']!='0.00')
                {
                    $insurance = (($booking['rate']*$booking['insurance'])/100)+$booking['rate'];
                    $price1 =  round($insurance,2); 
                    //echo $dec = ltrim(($price - floor($price)),"0.");  echo "<br>";
                    //echo $dec =round(($price - floor($price)),2);  echo "<br>";
                    $float_number_array = explode('.', $price1);
                    $insurance =  $insurance.'0';  
                    if(count($float_number_array)>1)
                    {
                        $float_number = $float_number_array[1];
                        if(strlen($float_number)>1)
                        {
                            $first_float =  substr($float_number, 0, 1); 
                            //echo  substr($float_number, 0, 1); echo "<br>";
                            $last_float =  substr($float_number, -1); 
                            if($last_float>=3 && $last_float<=7)
                                $new_float = $first_float.'5';
                            if($last_float>0 && $last_float<3)
                                $new_float = $first_float.'0';
                            if($last_float>7 && $last_float<=9)
                                $new_float = ((int)$first_float+1).'0';

                            $insurance =  $float_number_array[0].'.'.$new_float;
                        }
                        else
                        {
                            $insurance =  $insurance.'0';  
                        }
                    }
                }
                else
                {
                    $insurance = $booking['rate'];
                    $insurance =  round($insurance,2); 
                }
                $for_rate =  $booking['for_total'];
                /*if($booking['for_total']!='0.00') 
                { 
                    $for_rate =  $booking['for_total']+$booking['rate'];
                    $for_rate = $for_rate_per_kg*$weight;


                } */



                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$serial, $sequence);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$serial, 'SHAIL/'.$booking['booking_id']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$serial, $booking['party_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$serial, $booking['city_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$serial, $booking['brand_name']); 
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$serial, $booking['category_name']); 
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$serial, $booking['product_name']); 
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.$serial, $booking['quantity']); 
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$serial, $booking['rate']); 
                $objPHPExcel->getActiveSheet()->SetCellValue('L'.$serial, $for_rate); 
                $total_qty = $total_qty+$booking['quantity'];


                $insurance1 = round($insurance,2);
                $objPHPExcel->getActiveSheet()->SetCellValue('K'.$serial, $insurance1); 
                $originalDate = $booking['created_at'];
                $newDate = date("d-m-Y", strtotime($originalDate));
                $objPHPExcel->getActiveSheet()->SetCellValue('N'.$serial, $newDate); 
                
                if(isset($product_reports[$booking['brand_name']][$booking['category_name']][$booking['product_name']]))
                {
                    
                    //$rate = $booking['quantity']*$insurance;
                    $rate = $booking['quantity']*$rate_without_insurance;
                    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$serial, $rate); 
                    $product_reports[$booking['brand_name']][$booking['category_name']][$booking['product_name']] = array(
                        'qty' => $product_reports[$booking['brand_name']][$booking['category_name']][$booking['product_name']]['qty']+$booking['quantity'],
                        'rate' => $product_reports[$booking['brand_name']][$booking['category_name']][$booking['product_name']]['rate']+$rate,
                        );
                }
                else
                {
                    //$rate = $booking['quantity']*$insurance;
                    $rate = $booking['quantity']*$rate_without_insurance;
                    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$serial, $rate); 
                    $product_reports[$booking['brand_name']][$booking['category_name']][$booking['product_name']] = array(
                        'qty' => $booking['quantity'],
                        'rate' => $rate,
                        );
                }
                $serial++;
                $sequence++;
                $total_amt = $total_amt+$rate;
                //echo "<pre>"; print_r($product_report);

            }
        } 
        $serial = ++$serial;
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$serial, $total_qty);
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$serial, round($total_amt, 2)); 
        $objPHPExcel->getActiveSheet()->getStyle('I'.$serial)->applyFromArray($styleArraythick);

        for ($col='B'; $col<='Z'; $col++) { 
            $objPHPExcel->getActiveSheet()->getStyle($col.'1:'.$col.$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(false);
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(15); 
        }


        $styleArray = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
            )
          )
        );

       

        $objPHPExcel->getActiveSheet()->getStyle('B5:L7')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B5:L5')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C8:D8')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C4:D8')->applyFromArray($styleArray); 
        //$objPHPExcel->getActiveSheet()->getStyle('A10:J10')->applyFromArray($styleArraythick);
        $objPHPExcel->getActiveSheet()->getStyle('B10:M'.$serial)->applyFromArray($styleArray);
        //$objPHPExcel->getActiveSheet()->getStyle('A'.$serial.':J'.$serial)->applyFromArray($styleArraythick);
        unset($styleArray);

        $serial = $serial+2;
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$serial, "QTY");
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$serial, "Amount");
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$serial++, "AVG"); 
        $category_wise_total = 0;
        $category_amount_total = 0;
        //echo "<pre>"; print_r($product_reports); die;
        foreach ($product_reports as $key => $product_report) {
             
            foreach ($product_report as $key1 => $value1) {
                
                foreach ($value1 as $key2 => $value) {
                    $avg = $value['rate']/$value['qty'];
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$serial, $key);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$serial, $key1); 
                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$serial, $key2); 
                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$serial, $value['qty']); 
                    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$serial, round($value['rate'], 3)); 
                    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$serial, round($avg, 3)); 
                    $category_wise_total = $category_wise_total+$value['qty'];
                    $category_amount_total = $category_amount_total+round($value['rate'], 3);
                    $serial++;
                }
            }
            
        } 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$serial, $category_wise_total); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$serial, $category_amount_total);  

        $objPHPExcel->getActiveSheet()->getStyle('H'.$serial.':I'.$serial)->applyFromArray($styleArraythick);


        /*foreach ($product_reports as $key => $product_report) {
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$serial, $key); 
            foreach ($product_report as $key => $value1) {
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$serial, $key); 
                foreach ($value1 as $key => $value) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$serial, $value); 
                }
            }
            $serial++;
        } */
        //echo "<pre>"; print_r($product_report); die;


        /*foreach($cart_session as $id=>$val){
            $product_cart[$id] = $val;
            $old_qty = $product_cart[$id]['qty']; 
            if($id==$packagingid)
            {
                $qty = $old_qty+$qty; 
            }
        } */




        // set Row
        /*$rowCount = 2;
        foreach ($empInfo as $element) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element['name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $element['category_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $element['sku']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $element['brand_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $element['category_name']);
            $rowCount++;
        } */
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
       // echo $_SERVER['DOCUMENT_ROOT'].'sales_management/'.$fileName; die;
        //$objWriter->save(ROOT_UPLOAD_IMPORT_PATH.$fileName);
        // download file
        $objWriter->save($fileName);
        header("Content-Type: application/vnd.ms-excel");
        echo base_url().$fileName; die;
        //redirect(base_url().$fileName);  
        //echo "string"; die;
    }


     public function createXLS() {
        // create file name
        $fileName = 'data-'.time().'.xlsx';  
        // load excel library
        $this->load->library('excel');
        $empInfo = $this->export->employeeList();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'First Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Last Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Email');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'DOB');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Contact_No');       
        // set Row
        $rowCount = 2;
        foreach ($empInfo as $element) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element['first_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $element['last_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $element['email']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $element['dob']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $element['contact_no']);
            $rowCount++;
        }
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save(ROOT_UPLOAD_IMPORT_PATH.$fileName);
        // download file
        header("Content-Type: application/vnd.ms-excel");
        echo "string"; die;
        //redirect(HTTP_UPLOAD_IMPORT_PATH.$fileName);        
    }
}