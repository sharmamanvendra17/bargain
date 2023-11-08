<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rate extends CI_Controller {

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
        $this->load->model('rate_model');      
        $this->load->library('dynamic');    
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
    }

    public function index1(){  
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Rate Master";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;

        if(isset($_POST) && !empty($_POST))
        {
            $this->form_validation->set_rules('brand', 'Brand Name','required');
            $this->form_validation->set_rules('category', 'Category Name','required');
            $this->form_validation->set_rules('rate', 'Rate','required');
            if ($this->form_validation->run() == false) {
            }
            else { 
                $condition= array('brand_id' =>$_POST['brand'],'id' =>$_POST['category']);
                $update_data = array('product_price' => $_POST['rate']);
                $insertdata = array('brand_id' =>$_POST['brand'],'category_id' =>$_POST['category'],'rate' =>$_POST['rate'],'created_by' =>$userid);
                $insertdata['is_ex_rate'] =1;
                $update_data['is_ex_rate'] = 1;
                if(!isset($_POST['is_ex_rate']))
                {
                    $update_data['is_ex_rate'] = 0;
                    $insertdata['is_ex_rate'] = 0;
                }

                $insertdata['insurance_included'] =0;
                $update_data['insurance_included'] = 0;

                if(isset($_POST['insurance']))
                {
                    $update_data['insurance_included'] = 1;
                    $insertdata['insurance_included'] = 1;
                }
                
                $result = $this->rate_model->AddRates($insertdata);
                if($result)
                {
                    $result = $this->rate_model->UpdateCategoryRates($update_data,$condition);
                    $this->session->set_flashdata('suc_msg','Rate added successfully.');  
                }
                else
                {
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                }
                redirect('rate');
            }
        }
    	$data['rates'] = $this->rate_model->GetMasterRates();
    	//echo "<pre>"; print_r($data['rates']); die;
        $this->load->model('brand_model'); 
        $data['brands'] = $this->brand_model->GetAllBrand(); 
    	$this->load->view('rate_master_old',$data);
	}

    public function index(){  
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Rate Master";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;

        if(isset($_POST) && !empty($_POST))
        {
            $this->form_validation->set_rules('brand', 'Brand Name','required');
            $this->form_validation->set_rules('category', 'Category Name','required');
            $this->form_validation->set_rules('rate', 'Rate','required');
            if ($this->form_validation->run() == false) {
            }
            else { 
                $condition= array('brand_id' =>$_POST['brand'],'id' =>$_POST['category']);
                $update_data = array('product_price' => $_POST['rate']);
                $insertdata = array('brand_id' =>$_POST['brand'],'category_id' =>$_POST['category'],'rate' =>$_POST['rate'],'created_by' =>$userid);
                $insertdata['is_ex_rate'] =1;
                $update_data['is_ex_rate'] = 1;
                if(!isset($_POST['is_ex_rate']))
                {
                    $update_data['is_ex_rate'] = 0;
                    $insertdata['is_ex_rate'] = 0;
                }

                $insertdata['insurance_included'] =0;
                $update_data['insurance_included'] = 0;

                if(isset($_POST['insurance']))
                {
                    $update_data['insurance_included'] = 1;
                    $insertdata['insurance_included'] = 1;
                }
                
                $result = $this->rate_model->AddRates($insertdata);
                if($result)
                {
                    $result = $this->rate_model->UpdateCategoryRates($update_data,$condition);
                    $this->session->set_flashdata('suc_msg','Rate added successfully.');  
                }
                else
                {
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                }
                redirect('rate');
            }
        }
        $data['rates'] = $this->rate_model->GetMasterRates();
        //echo "<pre>"; print_r($data['rates']); die;
        $this->load->model('brand_model'); 
        $data['brands'] = $this->brand_model->GetAllBrand(); 
        $data['packaging'] = $this->rate_model->rate_master_packaging (); 
        $this->load->view('rate_master',$data);
    }

    public function pdfhtml($type=''){
        $html = '
                    <table cellspacing="0" cellpadding="0" style="background:#ffff00;border-collapse:collapse;border:0px;">
                        <tr>
                            <td width="160">&nbsp;</td>
                            <td width="86">&nbsp;</td>
                            <td width="84">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="86">&nbsp;</td>
                            <td width="86">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="113">&nbsp;</td>
                            <td width="88">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border-bottom:1px solid #000;"> </td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td width="75" style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td width="75" style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                        </tr>';
                        if($type=='preview')
                        {
                            $html .= '<tr>
                                <td style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Loose Rate EX JP</td> 
                                <td width="88" style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getlooseratevansapati(2,17).'</td>
                                <td width="88" style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getlooseratevansapati(10,55).'</td>
                                <td width="86" style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getlooserate(2,18).'</td>
                                <td width="84" style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getlooserate(1,54).'</td>
                                <td width="75" style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getlooserate(2,27).'</td>
                                <td width="75" style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getlooserate(9,48).'</td>
                                <td  style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getlooserate(2,23).'</td>
                                <td width="86" style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getlooserate(1,31).'</td>
                                <td width="86" style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getlooserate(8,47).'</td>
                                <td width="75" style="color:#00b0f0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getlooserate(1,21).'</td>
                            </tr>';
                        }

                        $html .= '<tr>
                            <td colspan="11" style="color:#ed2f12;font-size:20px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">Data Group of Industries - Jaipur</td>
                        </tr>
                        <tr>
                            <td colspan="11" style="color:#7030a0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">Daily Rate Sheet (Ex. Fact. Plus GST+ Ins.(if Applicable) Against Adv. Payment) Dated '.date('d.m.Y').'</td>
                        </tr>
                        <tr>
                            <td rowspan="3" width="160" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Packed Items</td>
                            <td colspan="5" style="color:#002060;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> Shree Hari Agro Industries - Jaipur</td>
                            <td colspan="5" style="color:#002060;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;"> Babu Lal Edible Oil Pvt Ltd. - Alwar</td>
                        </tr>
                        <tr>
                            <td colspan="2" width="170" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Vans Ghee</td>
                            <td colspan="2" width="150" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">SBRO</td>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> GNRO  </td>
                            <td width="86" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">PGMO</td>
                            <td colspan="2" width="161" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">KGMO</td>
                            <td width="113" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">RBO </td>
                            <td width="88" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">SBRO</td>
                        </tr>
                        <tr>
                            <td width="86" style="color:#002060;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Ashoka / Scooter</td>
                            <td width="84" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Sutr Veg. Ghee</td>
                            <td width="75" style="color:#002060;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Ashoka</td>
                            <td width="75" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Scooter</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">Ashoka</td>
                            <td width="86" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Must Health</td>
                            <td width="86" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Ashoka   </td>
                            <td width="75" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Scooter</td>
                            <td width="113" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Shiv  Classic</td>
                            <td width="88" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">Scooter</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 15Kg Tin</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,71,15,1,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(10,55,306,307,15,1,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',90,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',320,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">NA</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',220,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',103,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',120,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',303,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',95,15,1,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">13 Kg Tin</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,351,13,1,'vanaspati').'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',325,13,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',324,13,1,'').'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',322,13,1,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 15 Ltr Tin(13.650)</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(2,18).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(1,54).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(2,27).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(9,48).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(2,23).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(1,31).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(8,47).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getrate(1,21).'</td>
                        </tr>
                        <tr>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 15 Ltr Tin(13.455)</td>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(2,17).'</td>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(10,55).'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">15 Ltr Jar</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,251,15,2,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',213,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',310,15,2,'').' </td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',179,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',296,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',205,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',237,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',213,15,2,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">5 Ltr Jar</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,186,5,2,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',165,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',311,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',110,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',190,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',122,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',136,5,2,'').'</td>

                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',292,5,2,'').'</td>

                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',147,5,2,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">2 Ltr Jar</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,187,2,2,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',334,2,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',111,2,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',191,2,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',222,2,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',239,2,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',293,2,2,'').'</td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',264,2,2,'').'</td>
                        </tr>

                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 1Ltr.Pouch</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,184,1,2,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',94,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',304,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',113,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',196,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',107,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',180,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',99,1,2,'').'</td>
                        </tr>

                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> Pouch 850 Gram</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',345,0.850,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',343,0.850,1,'').'</td>
                        </tr>

                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">500 Ml Pouch</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,168,500,3,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',198,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',305,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',274,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',233,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',234,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',252,500,3,'').'</td>
                        </tr>

                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Pouch 425 Gram</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 200 Ml Pouch</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,185,200,3,'vanaspati').'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td width="88" style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 100 Ml Pouch </td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,197,100,3,'vanaspati').'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td width="88" style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">1 Ltr Bottle</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">NA</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',93,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',335,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',112,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',192,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',211,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',188,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',177,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',98,1,2,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">500 Ml Bottle</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',144,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',336,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',193,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',209,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',189,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',290,500,3,'').'</td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',236,500,3,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">200 Ml Bottle</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',143,200,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',260,200,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',210,200,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',199,200,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',291,200,3,'').'</td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',142,200,3,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">100 Ml Bottle</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',241,100,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',301,100,3,'').'</td>
                            <td width="88" style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"><span style="color:#ed2f12;">Pouch</span> <span style="color:#7030a0;">Rs 10/-</span> <span style="color:#ed2f12;">MRP</span></td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;text-align:center;">'.getskurate(1,31,'',308,100,3,'',1).'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td width="88" style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Note: -1</td>
                            <td colspan="9" style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Add Rs. 1/- per Ltr as Freight  if Lifted From Jaipur of Alwar Product  and Vice versa of Jaipur product for Alwar Lifting </td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr> 
                    </table>
                ';
        return $html;
    }
	
	public function pdfhtml_watsapp(){
        $html = '
                    <table cellspacing="0" cellpadding="0" style="background:#ffff00;border-collapse:collapse;border:0px;">
                        <tr>
                            <td width="160">&nbsp;</td>
                            <td width="86">&nbsp;</td>
                            <td width="84">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="86">&nbsp;</td>
                            <td width="86">&nbsp;</td>
                            <td width="75">&nbsp;</td>
                            <td width="113">&nbsp;</td>
                            <td width="88">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border-bottom:1px solid #000;"> </td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td width="75" style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td width="75" style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="11" style="color:#ed2f12;font-size:20px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">Data Group of Industries - Jaipur</td>
                        </tr>
                        <tr>
                            <td colspan="11" style="color:#7030a0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">Daily Rate Sheet (Ex. Fact. Plus GST+ Ins.(if Applicable) Against Adv. Payment) Dated '.date('d.m.Y').'</td>
                        </tr>
                        <tr>
                            <td rowspan="3" width="160" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Packed Items</td>
                            <td colspan="5" style="color:#002060;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> Shree Hari Agro Industries - Jaipur</td>
                            <td colspan="5" style="color:#002060;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;"> Babu Lal Edible Oil Pvt Ltd. - Alwar</td>
                        </tr>
                        <tr>
                            <td colspan="2" width="170" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Vans Ghee</td>
                            <td colspan="2" width="150" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">SBRO</td>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> GNRO  </td>
                            <td width="86" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">PGMO</td>
                            <td colspan="2" width="161" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">KGMO</td>
                            <td width="113" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">RBO </td>
                            <td width="88" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">SBRO</td>
                        </tr>
                        <tr>
                            <td width="86" style="color:#002060;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Ashoka / Scooter</td>
                            <td width="84" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Sutr Veg. Ghee</td>
                            <td width="75" style="color:#002060;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Ashoka</td>
                            <td width="75" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Scooter</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">Ashoka</td>
                            <td width="86" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Must Health</td>
                            <td width="86" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Ashoka   </td>
                            <td width="75" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Scooter</td>
                            <td width="113" style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Shiv  Classic</td>
                            <td width="88" style="color:#0070c0;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">Scooter</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 15Kg Tin</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,71,15,1,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(10,55,306,307,15,1,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',90,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',320,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">NA</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',220,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',103,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',120,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',303,15,1,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',95,15,1,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">13 Kg Tin</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',324,13,1,'').'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',322,13,1,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 15 Ltr Tin(13.650)</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(2,18).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(1,54).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(2,27).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(9,48).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(2,23).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(1,31).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(8,47).'</td>
                            <td  style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getrate(1,21).'</td>
                        </tr>
                        <tr>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 15 Ltr Tin(13.455)</td>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(2,17).'</td>
                            <td style="color:#ed2f12;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getrate(10,55).'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">15 Ltr Jar</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,251,15,2,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',213,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',311,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',179,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',296,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',205,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',237,15,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',213,15,2,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">5 Ltr Jar</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,186,5,2,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',165,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',311,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',110,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">539.81</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',190,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',122,5,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',136,5,2,'').'</td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',147,5,2,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">2 Ltr Jar</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,187,2,2,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',111,2,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',191,2,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',222,2,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',239,2,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',293,2,2,'').'</td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',264,2,2,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 1Ltr.Pouch</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,184,1,2,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',94,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',304,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',113,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',196,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',107,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',180,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',99,1,2,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">500 Ml Pouch</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,168,500,3,'vanaspati').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',198,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,54,'',305,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',274,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',233,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',234,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',252,500,3,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 200 Ml Pouch</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,185,200,3,'vanaspati').'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td width="88" style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"> 100 Ml Pouch </td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,20,79,197,100,3,'vanaspati').'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td width="88" style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">1 Ltr Bottle</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">NA</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',93,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,27,'',112,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',192,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',211,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',188,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',177,1,2,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',98,1,2,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">500 Ml Bottle</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',144,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',193,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',209,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',189,500,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',290,500,3,'').'</td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',236,500,3,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">200 Ml Bottle</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,18,'',143,200,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"></td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(9,48,'',260,200,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(2,23,'',210,200,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',199,200,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',291,200,3,'').'</td>
                            <td width="88" style="color:#000;font-size:14px;font-style: italic;font-weight: 700;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">'.getskurate(1,21,'',142,200,3,'').'</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">100 Ml Bottle</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(1,31,'',241,100,3,'').'</td>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">'.getskurate(8,47,'',301,100,3,'').'</td>
                            <td width="88" style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;"><span style="color:#ed2f12;">Pouch</span> <span style="color:#7030a0;">Rs 10/-</span> <span style="color:#ed2f12;">MRP</span></td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;text-align:center;">'.getskurate(1,31,'',308,100,3,'',1).'</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td width="88" style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;">&nbsp;</td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Note: -1</td>
                            <td colspan="9" style="color:#000;font-size:14px;font-style: italic;font-weight: 600;text-align:center;border-bottom:1px solid #000;border-left:1px solid #000;">Add Rs. 1/- per Ltr as Freight  if Lifted From Jaipur of Alwar Product  and Vice versa of Jaipur product for Alwar Lifting </td>
                            <td style="border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                        </tr> 
                    </table>
                ';
        return $html;
    }
	
    public function pdf(){
        include(FCPATH."mpdf1/mpdf.php");
        $mpdf=new mPDF('utf-8','A4-L','0','0','10','10','25','35','10','10');  
        $html = $this->pdfhtml('preview');
        $mpdf->WriteHTML($html);
        $partyname = str_replace(' ', '-', $partyname);
        $file_name  =  $partyname.'Rate-Sheet-'.date('d-m-y').'.pdf';
        $pdf_file_name  = base_url().'rates-cnf/'.$file_name;
        //$file_name  =  FCPATH.'rates-cnf/'.$file_name;
        $mpdf->Output($file_name,'I'); 
    }

    public function whatsapppdf(){

        //echo "<pre>"; print_r($_POST['numbers']); die;

        include(FCPATH."mpdf1/mpdf.php");
        $mpdf=new mPDF('utf-8','A4-L','0','0','10','10','25','35','10','10');  
        $html = $this->pdfhtml('whatsapp');
        $mpdf->WriteHTML($html);
          
        $file_name  =  'StateWise-Rate-Sheet-'.date('d-m-y-h-i-s').'.pdf';
        $pdf_file_name  = base_url().'rate-pdf/'.$file_name;
        $file_name  =  FCPATH.'rate-pdf/'.$file_name;
        $mpdf->Output($file_name,'F'); 
        $this->load->model('admin_model');   
        $users = $_POST['numbers'];
        if($users)
        {
             
            $mobile_number = implode(',', $users); 
            $message_params = urlencode(date('d-M-Y',time()));
            $curl_watsappapi = curl_init(); 
            curl_setopt_array($curl_watsappapi, array( 
            CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_number.'&TID=9781753&P='.$message_params.'&PATH='.$pdf_file_name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS =>'',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
              ),
            ));
            echo $response = curl_exec($curl_watsappapi);
            curl_close($curl_watsappapi);
        }
    }

    public function excel(){
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $group_name_style = array(
            'font'  => array(
                'color' => array('rgb' => 'ff0000'),
                'size'  => 20,
                'bold'  => true,
            ),
            'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array('rgb' => '90EE90')
            ),
            'borders' => array (
              'allborders' => array (
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000'),        // BLACK
              )
            ),
            'alignment' => array ( 
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
            ),
        );

        $group_style = array(
            'alignment' => array ( 
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
            ),
            'font'  => array(
                'color' => array('rgb' => 'a020f0'),
                'size'  => 14, 
            ),
            'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array('rgb' => '90EE90')
            ),
            'borders' => array (
              'allborders' => array (
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000'),        // BLACK
              )
            ),
        );

        $blue_style = array(
            'alignment' => array ( 
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
            ),
            'font'  => array(
                'color' => array('rgb' => 'ff0000'),
                'size'  => 14, 
            ),
            'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array('rgb' => '90EE90')
            ),
            'borders' => array (
              'allborders' => array (
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000'),        // BLACK
              )
            ),
        );

        $col =  'B';
        $row =  5; 
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Data Group of Industries - Jaipur');
        $objPHPExcel->getActiveSheet()->mergeCells($col.$row.':L'.$row);
        $objPHPExcel->getActiveSheet()->getStyle($col.$row.':L'.$row)->applyFromArray($group_name_style);
        //$objPHPExcel->getStyle($col.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $row++;

        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Daily  Rate Sheet ( Ex. Fact. Plus GST+ Ins.( if Applicable) Against Adv. Payment  )  Dated '.date('d.m.Y'));
        $objPHPExcel->getActiveSheet()->mergeCells($col.$row.':L'.$row);
        $objPHPExcel->getActiveSheet()->getStyle($col.$row.':L'.$row)->applyFromArray($group_style);
        $row++;


        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packed Items');
        $next_row = $row+2;
        $objPHPExcel->getActiveSheet()->mergeCells($col.$row.':'.$col.$next_row);
        $objPHPExcel->getActiveSheet()->getStyle($col.$row.':'.$col.$next_row)->applyFromArray($blue_style);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Shree Hari Agro Industries- Jaipur');
        $objPHPExcel->getActiveSheet()->mergeCells($col.$row.':G'.$row);

        $objPHPExcel->getActiveSheet()->setCellValue('H'.$row,'Babu Lal Edible Oil Pvt Ltd. - Alwar');
        $objPHPExcel->getActiveSheet()->mergeCells('H'.$row.':L'.$row);
        //$objPHPExcel->getActiveSheet()->getStyle($col.$row.':L'.$row)->applyFromArray($group_name_style);
        $row++; 

        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Vans Ghee');
        $objPHPExcel->getActiveSheet()->mergeCells($col.$row.':D'.$row);
        //$objPHPExcel->getActiveSheet()->getStyle($col.$row.':L'.$row)->applyFromArray($group_name_style);
        $col = 'E';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'SBRO');
        $objPHPExcel->getActiveSheet()->mergeCells($col.$row.':F'.$row); 
        //$objPHPExcel->getActiveSheet()->getStyle($col.$row.':L'.$row)->applyFromArray($group_name_style);
        $col = 'G';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'GNRO');
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'PGMO');
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'KGMO');
        $objPHPExcel->getActiveSheet()->mergeCells($col.$row.':J'.$row); 
        $col = 'K';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'RBO');
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'SBRO');
        $row++; 
        $col = 'C';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Ashoka / Scooter');
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Sutr Veg. Ghee');
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Ashoka');
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Scooter');
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Ashoka');
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Must Health');
        $col++;

        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Ashoka');
        $col++;

        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Scooter');
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Shiv  Classic');
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Scooter'); 
        $row++; 


        $fileName = 'Daily Rates.xls'; 
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Pragma: no-cache");
        header("Expires: 0");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save('php://output'); 
    }

    public function rateusers(){
        $this->load->model('admin_model');  
        $condition = array('rate_pdf' => 1,'status' => 1);
        $users = $this->admin_model->allemployess($condition);   
        $response= '<table class="table table-striped table-bordered table-hover" id="datatable_sample">
            <thead>
            <tr>
                <th>S.No.</th>
                <th></th>
                <th>Name</th>
                <th>Mobile</th>
            </tr>
            </thead>
        ';
        if($users)
        {
            $i = 1;
            foreach ($users as $key => $value) {
                $response .=
                        "<tr>
                            <td>".$i."</td>
                            <td><input name='users' type='checkbox' class='user_select' value='".$value['mobile']."' checked ></td>
                            <td>".$value['name']."</td>
                            <td>".$value['mobile']."</td>
                        </tr>";
                $i++;
            }
        }
        else
        {
            $response .= "<tr><td colspan='4'>No User Found</td></tr>";
        }
        $response .= "</table>";
        echo $response;
    }
}
