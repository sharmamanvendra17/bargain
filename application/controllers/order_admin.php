<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_admin extends CI_Controller {

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
        $this->load->model('order_model');                   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();
    }

    public function index(){
    	$data['title'] = "Orders";
    	$data['orders'] = $this->order_model->GetAllOrders(); 
    	$this->load->view('orders',$data);
	} 

    public function paid_orders(){ 
        $data['title'] = "Paid Orders";
        $data['orders'] = $this->order_model->GetAllOrders('Paid'); 
        $this->load->view('orders',$data);
    }
    
    public function unpaid_orders(){
        $data['title'] = "Unpaid Orders";
        $data['orders'] = $this->order_model->GetAllOrders('Pending'); 
        $this->load->view('orders',$data);
    }
	 
    public function order(){
        $data['title'] = "Order Detail"; 
        $order_id = base64_decode($this->uri->segment(3));
        $data['order'] = $this->order_model->GetOrderDetail($order_id); 
        $this->load->view('order_detail',$data);
    }

    function create_pdf()
    { 
        $order_id = $_POST['order_id'];
        $order = $this->order_model->GetOrderDetail($order_id);  
        $firstname = $order['firstname'];
        $lastname = $order['lastname'];
        $address = $order['address'];
        $address_2 = $order['address2'];

        $state= $order['state'];
        $city= $order['city'];

        $city_name = getCityname($city);
        $state_name =  getStatename($state);

        $postcode = $order['postcode']; 

        $phone = $order['phone'];


        $order_id = $order['order_id'];
        $invoice_id = $order['invoice_id'];

        $invoice_number_x = "PRAGATII".str_pad($invoice_id,5,0,STR_PAD_LEFT);
        $order_id_x = "HARI-PRAGATII-".str_pad($order_id,5,0,STR_PAD_LEFT);


        $TxnId = $order['TxnId'];

        $created_at = $order['created_at'];
        $date = date("F d Y", strtotime($created_at));
        //$date =  $order['created_at'];

        $subtotal = $order['sub_total'];
        $gst = $order['gst'];
        $grand_total = $order['grand_total'];
        $shipping = $order['shipping_charge'];
        
        $shipping_info_self_pick_up = $order['self_pick_up'];
        $pick_up_address = $order['self_pick_up_address'];

        $amount_in_words = $this->convert_number(round($grand_total));

        $cart_session = json_decode($order['order_history']);
        //echo "<pre>"; print_r($cart_session); die;
        $i=0;
        $item_msg='';
        foreach ($cart_session as $key => $value)
        {
            $row = $this->order_model->product_detail($value->packagingid); 
            $row_total = $value->product_qty*$value->product_price;
            //echo "<pre>"; print_r($row);
            $item_msg .=  '<tr><td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;"><span>Ashoka '.$row['product_name'].' | '.$row['packing'].'</span><br>HSN '.$row['hsn'].'</td>
              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;">'.$value->product_price.'</td>
              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;">'.$value->product_qty.'</td>
              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;"><span>'.$row['packing'].'</span></td>
              <td style="border:none; cellpadding:0px; margin:0px auto;  padding:5px 10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:18px; text-align:left; border-bottom:1px solid #cccccc;"><span>Rs '.$row_total.'</span></td></tr>';
              $i++;
        }  


        $invoice_number_x = "PRAGATII".str_pad($invoice_id,5,0,STR_PAD_LEFT);
        $order_id_x = "HARI-PRAGATII-".str_pad($order_id,5,0,STR_PAD_LEFT);
        include APPPATH."controllers/invoice_template.php";
 
        $html= $message;
        $invoice_name = $firstname.$lastname.$order_id.'.pdf';
        $pdfFilePath = $_SERVER['DOCUMENT_ROOT']."/assets/duplicate_invoice/".$invoice_name;
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "F");

        echo base_url()."/assets/duplicate_invoice/".$invoice_name; die;
    }


    public function convert_number($number)
    { 
      $hyphen      = '-';
      $conjunction = ' and ';
      $separator   = ', ';
      $negative    = 'negative ';
      $decimal     = ' and ';
      $dictionary  = array(
        0                   => 'zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        100000             => 'Lakh',
        10000000          => 'Crore',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
      );
      if (!is_numeric($number)) {
          return false;
      }
      if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
      }
      if ($number < 0) {
          return $negative . $this->convert_number(abs($number));
      }
      $string = $fraction = null;
      if (strpos($number, '.') !== false) {
          list($number, $fraction) = explode('.', $number);
      }
      switch (true) {
          case $number < 21:
              $string = $dictionary[$number];
              break;
          case $number < 100:
              $tens   = ((int) ($number / 10)) * 10;
              $units  = $number % 10;
              $string = $dictionary[$tens];
              if ($units) {
                  $string .= $hyphen . $dictionary[$units];
              }
              break;
          case $number < 1000:
              $hundreds  = $number / 100;
              $remainder = $number % 100;
              $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
              if ($remainder) {
                  $string .= $conjunction . $this->convert_number($remainder);
              }
              break;
            case $number < 100000:
                $thousands  = $number / 1000; 
                if($thousands >= 21)
                {
                  $tens   = ((int) ($thousands / 10)) * 10; 
                  $units  = $thousands % 10; 
                  $string = $dictionary[$tens];
                  if ($units) {
                      $string .= ' '.$dictionary[$units];
                  }
                }
              $remainder = $number % 1000;
              @$string .= $dictionary[$thousands] . ' ' . $dictionary[1000]; 
              if ($remainder) {
                  $string .= $conjunction . $this->convert_number($remainder);
              }
              break;
            case $number < 10000000:
                $thousands  = $number / 100000; 
                if($thousands >= 21)
                {
                  $tens   = ((int) ($thousands / 10)) * 10; 
                  $units  = $thousands % 10; 
                  $string = $dictionary[$tens];
                  if ($units) {
                      $string .= ' '.$dictionary[$units];
                  }
                }
              $remainder = $number % 100000;
              @$string .= $dictionary[$thousands] . ' ' . $dictionary[100000]; 
              if ($remainder) {
                  $string .= $conjunction . $this->convert_number($remainder);
              }
              break;
          default:
               // echo log($number, 100000); die;
               $baseUnit = pow(10000000, floor(log($number, 10000000)));
              $numBaseUnits = (int) ($number / $baseUnit);
              $remainder = $number % $baseUnit; 
              $string = $this->convert_number($numBaseUnits) . ' ' . $dictionary[$baseUnit];
              if ($remainder) {

                  $string .= $remainder < 10000000 ? $conjunction : $separator;

                  $string .= $this->convert_number($remainder)."";
              }
              break;
      }
      if (null !== $fraction && is_numeric($fraction)) {
          $string .= $decimal;
          $words = array();
          foreach (str_split((string) $fraction) as $number) {
              $words[] = $dictionary[$number];
          }
          $string .= implode(' ', $words). "";
      }

      return $string;    
    }
}