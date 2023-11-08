<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking extends CI_Controller {

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
        $this->load->model(array('booking_model','brand_model','vendor_model','category_model','broker_model'));      
                     
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        
    }

    public function index(){   
    	if (!$this->session->userdata('admin'))
           redirect('/');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];
        if($admin_role!='admin' && $admin_role!='moderator')
            redirect('/');
        $data['title'] = "Booking"; 

        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $brand = $this->input->post('brand'); 
            $category = $this->input->post('category'); 
            $product = $this->input->post('product'); 
            $quantity = $this->input->post('quantity'); 
            $rate = $this->input->post('rate');   
            $booking_date = $this->input->post('booking_date'); 
            $insurance = $this->input->post('insurance');   
            $broker = $this->input->post('broker');   
            $is_for = $this->input->post('is_for');   

            $this->form_validation->set_rules('party', 'Party Name','required');
            $this->form_validation->set_rules('brand', 'Brand','required');
            $this->form_validation->set_rules('category', 'Category','required');
            $this->form_validation->set_rules('product', 'Product','required');
            $this->form_validation->set_rules('quantity', 'Quantity','required');


            if ($this->form_validation->run() == false) {
            }
            else { 
                ///$insertdata = array('party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate);
                $product_info = $this->category_model->Productinfobyid($product);
                $loose_rate = $product_info['loose_rate'];
                $weight = $product_info['weight'];
                $vendor_condition = array('id' =>$party);
                $vendor_info = $this->vendor_model->GetUserbyId($vendor_condition);
                //echo "<pre>"; print_r($vendor_info); die;
                $for_rate_per_kg = $vendor_info['for_rate'];
                $total_loose_rate = $loose_rate*$quantity;
                $total_weight = $weight*$quantity;
                $for_price = $for_rate_per_kg;
                $total_for_price = $for_rate;
                
                $insurance_amount = (($total_price*$insurance)/100)+$total_price;
                $rate1 = $rate;
                $total_for_price1 = 0;
                if($is_for==0)
                {
                    $for_rate = $for_rate_per_kg*$weight;                    
                    $total_for_price1 = $rate;
                    $rate1 = $rate-$for_rate;
                }

                $total_price = $rate1*$quantity;

                

                $today_cur_date =  date("dm"); 
                $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 

                if($booking_date!='')
                { 
                    $today_cur_date = date("dm", strtotime($booking_date));
                    $book_chek_date = $booking_date." 00:00:00.000000";

                }  
                $new_booking_id  = $this->booking_model->getlast_booking_id($book_chek_date);
                $booking_count = $this->booking_model->CheckBooking($book_chek_date);
                 
                    if(!$booking_count)
                        $new_booking_id =0;

                
                $insertdata = array('booking_id' =>$new_booking_id+1,'party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate1,'loose_rate' =>$loose_rate,'total_loose_rate' =>$total_loose_rate,'total_weight' =>$total_weight,'total_price' =>$total_price,'insurance' =>$insurance,'insurance_amount' =>$insurance_amount,'broker_id' =>$broker,'admin_id' =>$admin_id,'is_for' =>$is_for,'for_total' => $total_for_price1,'for_price' => $for_price);

                if($booking_date!='')
                     $insertdata['created_at'] = $booking_date.':00'; 
                $result = $this->booking_model->AddBooking($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Booked successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('booking');
            }
        } 
        $data['bookings'] = $this->booking_model->GetBooking(); 
        $data['brokers'] = $this->broker_model->GetBrokers(); 
        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();
    	$data['categories'] = $this->category_model->GetCategories();
        $data['distinct_categories'] = $this->category_model->GetCategories1();
        //echo "<pre>"; print_r($data['categories']); die;
    	$this->load->view('booking',$data);

	}


    public function booking_report()
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
        $objPHPExcel->getActiveSheet()->SetCellValue('K10', 'Rate ( With ins.)'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L10', 'Rate(FOR)');
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

                            if($float_number=='98' || $float_number=='99' )
                            {
                                //echo ($float_number_array[0]+1).'.00';
                                $insurance =  ($float_number_array[0]+1).'.00';
                            }
                            else
                            {
                                $insurance =  $float_number_array[0].'.'.$new_float;
                            }
                            //$insurance =  $float_number_array[0].'.'.$new_float;
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

	public function add(){
        if (!$this->session->userdata('admin'))
           redirect('/');
        $data['title'] = "New Booking";

        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $brand = $this->input->post('brand'); 
            $category = $this->input->post('category'); 
            $product = $this->input->post('product'); 
            $quantity = $this->input->post('quantity'); 
            $rate = $this->input->post('rate');   
            $booking_date = $this->input->post('booking_date');   

            $this->form_validation->set_rules('party', 'Party Name','required');
            $this->form_validation->set_rules('brand', 'Brand','required');
            $this->form_validation->set_rules('category', 'Category','required');
            $this->form_validation->set_rules('product', 'Product','required');
            $this->form_validation->set_rules('quantity', 'Quantity','required');


            if ($this->form_validation->run() == false) {
            }
            else { 
                $product_info = $this->category_model->Productinfobyid($product);
                $loose_rate = $product_info['loose_rate'];
                $weight = $product_info['weight'];
                //$for_rate = $product_info['for_rate'];
                $vendor_condition = array('id' =>$party);
                $vendor_info = $this->vendor_model->GetUserbyId($vendor_condition);
                //echo "<pre>"; print_r($vendor_info); die;
                $for_rate = $vendor_info['for_rate'];

                $total_loose_rate = $loose_rate*$quantity;
                $total_weight = $weight*$quantity;
                $total_price = $rate*$quantity;
                $total_for_price = 0;
                if($is_for==0)
                    $total_for_price = $for_rate*$quantity;

                $insurance_amount = (($total_price*$insurance)/100)+$total_price;

                
                $insertdata = array('party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate,'loose_rate' =>$loose_rate,'total_loose_rate' =>$total_loose_rate,'total_weight' =>$total_weight,'total_price' =>$total_price,'for_total' => $total_for_price);
                if($booking_date!='')
                     $insertdata['created_at'] = $booking_date.': 00'; 
                $result = $this->booking_model->AddBooking($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Booked successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('booking');
            }
        } 


        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();
    	//$data['categories'] = $this->category_model->GetCategories();
    	$this->load->view('booking_add',$data);
    }


    public function edit(){
        if (!$this->session->userdata('admin'))
           redirect('/');
        $data['title'] = "Booking Edit";
        $booking_id = base64_decode($this->uri->segment(3));
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $brand = $this->input->post('brand'); 
            $category = $this->input->post('category'); 
            $product = $this->input->post('product'); 
            $quantity = $this->input->post('quantity'); 
            $rate = $this->input->post('rate');    
            $insurance = $this->input->post('insurance');
            $booking_date = $this->input->post('booking_date');   
            $loose_rate = $this->input->post('loose_rate');  
            $broker = $this->input->post('broker');  
            $is_for = $this->input->post('is_for');  
            $this->form_validation->set_rules('party', 'Party Name','required');
            $this->form_validation->set_rules('brand', 'Brand','required');
            $this->form_validation->set_rules('category', 'Category','required');
            $this->form_validation->set_rules('product', 'Product','required');
            $this->form_validation->set_rules('quantity', 'Quantity','required');


            if ($this->form_validation->run() == false) {
            }
            else { 
                //$insertdata = array('party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate);

                $product_info = $this->category_model->Productinfobyid($product); 
                $weight = $product_info['weight'];
                $loose_rate = $product_info['loose_rate'];
                //$for_rate = $product_info['for_rate'];
                $vendor_condition = array('id' =>$party);
                $vendor_info = $this->vendor_model->GetUserbyId($vendor_condition);
                //echo "<pre>"; print_r($vendor_info); die;
                $for_rate_per_kg = $vendor_info['for_rate'];
                //$total_for_price = 0;
                $for_price = $for_rate_per_kg;
                
                $total_loose_rate = $loose_rate*$quantity;
                $total_weight = $weight*$quantity;
                
                //$total_for_price = $for_rate;
                $rate1 = $rate;
                $total_for_price1 = 0;
                if($is_for==0)
                {
                    $for_rate = $for_rate_per_kg*$weight;                    
                    $total_for_price1 = $rate;
                    $rate1 = $rate-$for_rate;
                }
                $total_price = $rate1*$quantity;
                $insertdata = array('party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate1,'total_loose_rate' =>$total_loose_rate,'total_weight' =>$total_weight,'total_price' =>$total_price,'insurance' =>$insurance,'insurance_amount' =>$insurance_amount,'broker_id' =>$broker,'is_for' =>$is_for,'for_total' => $total_for_price1,'for_price' => $for_price);


                if($booking_date!='')
                     $insertdata['created_at'] = $booking_date.': 00'; 
                $condition = array('id' =>$booking_id);
                $result = $this->booking_model->UpdateBooking($insertdata,$condition);
                if($result)
                    $this->session->set_flashdata('suc_msg','Booked updated successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('booking');
            }
        } 


        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['brokers'] = $this->broker_model->GetBrokers();
        $data['users'] = $this->vendor_model->GetUsers();
        $data['booking_info'] = $this->booking_model->GetBookingInfoById($booking_id);
        //echo "<pre>"; print_r($data['booking_info']); die;
        //$data['categories'] = $this->category_model->GetCategories();
        $this->load->view('booking_edit',$data);
    }
	
    public function delete(){
        $data['title'] = "Order Edit";
        $booking_id = base64_decode($this->uri->segment(3));
        if($booking_id)
        {
            $condition = array('id' =>$booking_id);
            $result = $this->booking_model->DeleteBooking($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Booking deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('booking');  
    }

    public function report(){   
        if (!$this->session->userdata('admin'))
           redirect('/');
        $data['title'] = "Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die;
            $this->session->set_userdata('search__report_data', $_POST);  
            $party_id = $_POST['party'];
            $brand_id = $_POST['brand'];
            $category_id = $_POST['category'];
            $product_id = $_POST['product'];
            $booking_date_from = $_POST['booking_date_from'];
            $booking_date_to = $_POST['booking_date_to'];

            $data['bookings'] = $this->booking_model->GetReport($party_id,$brand_id,$category_id,$product_id,$booking_date_from,$booking_date_to);
			//echo "<pre>"; print_r($data); die;
        } 
        
        //echo "<pre>"; print_r($data['bookings']); die;
        $data['categories'] = $this->category_model->GetCategories();
        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();
        $data['booking_date_from'] = $booking_date_from;
        $data['booking_date_to'] = $booking_date_to;

        $data['party_id'] = $party_id;
        $data['brand_id'] = $brand_id;
        $data['category_id'] = $category_id;
        $data['product_id'] = $product_id;
        $data['distinct_categories'] = $this->category_model->GetCategories1();
        $this->load->view('booking_report',$data);

    }

	public function status_update(){   
		if (!$this->session->userdata('admin'))
           redirect('/');
        $data['title'] = "";
		$status =  $this->uri->segment(3);
		$category_id =  base64_decode($this->uri->segment(4)); 
		$update_data = array('is_enable' => $status);
		$condition  = array('id' => $category_id);
		$result= $this->category_model->UpdateCategory($update_data,$condition);
		if($result)
			$this->session->set_flashdata('suc_msg','Category updated successfully.');
		else
			$this->session->set_flashdata('err_msg','Something went wrong.');
		redirect('category');
	} 
 

	public function edit_category(){    
		if (!$this->session->userdata('admin'))
            redirect('/');
        $data['title'] = "Update Product";
		$category_id =  base64_decode($this->uri->segment(3));
		$data['product'] = $this->category_model->GetCategoryByCategoryId($category_id);
		$old_hsn  = $data['product']['hsn'];

		if($category_id)
		{
			$condition = array('id' => $category_id);
			if(!empty($_POST))
			{ 
				$name = $this->input->post('name'); 
	            $brand = $this->input->post('brand'); 
	            $is_enable = $this->input->post('is_enable'); 
	            $sort_order = $this->input->post('sort_order'); 
	            $hsn = $this->input->post('hsn');   


	            $this->form_validation->set_rules('name', 'Product Name','required');
	            $this->form_validation->set_rules('brand', 'Brand','required');
	            $this->form_validation->set_rules('is_enable', 'Enable','required');
	            $this->form_validation->set_rules('sort_order', 'Sort Order','required');
	            $this->form_validation->set_rules('hsn', 'HSN','required');


	            if ($this->form_validation->run() == false) {
	            }
	            else { 
	                $updatedata = array('category_name' =>$name,'brand_id' =>$brand,'is_enable' =>$is_enable,'sort_order' =>$sort_order,'hsn' =>$hsn);
	                $result = $this->category_model->UpdateCategory($updatedata,$condition);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Category updated successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	                redirect('category');
	            }
			} 
			
			//echo "<pre>"; print_r($data['product']); die;
			$this->load->model('brand_model'); 
	        $data['brands'] = $this->brand_model->GetAllBrand(); 
	    }
	    else
	    {
	    	redirect('category');
	    }
		$this->load->view('category_edit',$data);
	}  

	public function edit_product_old(){    
		if (!$this->session->userdata('admin'))
            redirect('/');
        $data['title'] = "Update Product";
		$product_id =  base64_decode($this->uri->segment(3));
		if(!empty($_POST))
		{ 
			$packages = $_POST['package'];
            $mrp_rates = $_POST['mrp']; 
			if(count($packages))
			{
				foreach ($packages as $key => $price) {
                    $mrp = $mrp_rates[$key];
                    
					$update_data = array('price' => $price,'mrp' => $mrp);
					$condition  = array('id' => $key);
                    $result= $this->category_model->UpdatePackaging($update_data,$condition);

				} 
				$this->session->set_flashdata('suc_msg','Product updated successfully.');
			}
			else
				$this->session->set_flashdata('err_msg','Nothing to update.');
			redirect('product_admin');
		}
		$data['packagings'] = $this->category_model->GetProductPackagingByProductId($product_id);
		$data['product'] = $this->category_model->GetProductByProductId($product_id);
		$this->load->view('packagings',$data);
	} 	
 
    

    public function delete_category(){
        $data['title'] = "Category EDelete";
        $category_id = base64_decode($this->uri->segment(3));
        if($category_id)
        {
            $condition = array('id' =>$category_id);
            $result = $this->category_model->DeleteCategory($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Category deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('category');  
    }
}