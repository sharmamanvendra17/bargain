<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schemes extends CI_Controller {

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
        $this->load->model('scheme_model'); 
        $this->load->model('broker_model');  
        $this->load->model('brand_model');
        $this->load->model('category_model');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();              

    }

    public function index(){ 
    	$data['title'] = "Schemes";
        $condition = array();
    	$data['schemes'] = $this->scheme_model->GetSchemes($condition); 
    	$this->load->view('schemes',$data);

	}

    public function add(){
        $data['title'] = "Scheme Add";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die;   
            $this->form_validation->set_rules('brand', 'Brand','required');
            $this->form_validation->set_rules('category', 'Category','required|is_unique[brokers.pan_card]');
            //$this->form_validation->set_rules('target_dispatched_ton', 'Dispatched Weight','required');
            //$this->form_validation->set_rules('reward', 'Reward','required');
            $this->form_validation->set_rules('state', 'State','required');
            $this->form_validation->set_rules('scheme_for', 'Scheme For','required'); 
            $this->form_validation->set_rules('from_date', 'Scheme Date (From)','required');
            $this->form_validation->set_rules('to_date', 'Scheme Date (To)','required');
            //$this->form_validation->set_rules('scheme_image', 'Image', 'required');

            if ($this->form_validation->run() == false) {
            }
            else {  
                $insertdata = array(
                    'brand_id' =>trim($_POST['brand']),
                    'scheme_name' =>trim($_POST['brand']).'_'.trim($_POST['category']).'_'.trim($_POST['state']),
                    'category_id' =>trim($_POST['category']),
                    //'target_dispatched_ton' =>trim($_POST['target_dispatched_ton']),
                    //'reward_name' =>trim($_POST['reward']),
                    'scheme_state' =>trim($_POST['state']),
                    'scheme_for' =>trim($_POST['scheme_for']),
                    'from_date' => date('Y-m-d', strtotime(trim($_POST['from_date']))),
                    'to_date' =>date('Y-m-d', strtotime(trim($_POST['to_date']))),
                    'created_by' => $admin_id,
                );
                $filename = 'scheme_image';
                $path = './public/uploads/scheme_images/';
                if ($_FILES[$filename]['size'] != 0) {
                    $upload_dir = $path;
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir);
                    }
                    $config = array();
                    $config['upload_path'] = $upload_dir;
                    $config['allowed_types'] = 'jpeg|jpg|png';
                    $config['file_name'] = time() . $_FILES[$filename]['name'];
                    $config['overwrite'] = false;
                    //pr($config); die;
                    $this->load->library('upload', $config);
                    if (!$this->upload->do_upload($filename)) {
                        //pr($this->upload->display_errors()); die;
                    } else {
                        $this->upload_data[$filename] = $this->upload->data();
                        $insertdata['scheme_image'] = $this->upload_data[$filename]['file_name'];
                        //pr($this->upload_data['pancardDoc']); die;
                    }
                } else {
                    //this->form_validation->set_message('filename', "No file selected"); 
                }
                //echo "<pre>"; print_r($insertdata); die;
                $result = $this->scheme_model->AddScheme($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Scheme added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');    
                redirect('schemes');
            }
        } 
        $data['states'] = $this->broker_model->GetStates();
        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['categories'] = $this->category_model->GetCategories();
        $data['distinct_categories'] = $this->category_model->GetCategories1();
        $this->load->view('scheme_add',$data);
    }

    public function detail($scheme_id){
        $data['title'] = "Scheme Add";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die;   
            $target_dispatched_ton = $_POST['target_dispatched_ton'];
            $reward = $_POST['reward']; 
            $scheme_image = $_FILES['scheme_image'];   
            $update_id = $_POST['update_id'];   
            $delete_id = $_POST['delete_id'];   
            $added = 1;
            if(count($target_dispatched_ton))
            {
                $insertdata = array();

                foreach ($target_dispatched_ton as $key => $value) { 
                    $_FILES['userfile']=array();
                    $scheme_image = "";  
                    $path = './public/uploads/scheme_images/';
                    if ($_FILES['scheme_image']['size'][$key] != 0) { 

                        $_FILES['userfile']['name']= $_FILES['scheme_image']['name'][$key];
                        $_FILES['userfile']['type']= $_FILES['scheme_image']['type'][$key];
                        $_FILES['userfile']['tmp_name']= $_FILES['scheme_image']['tmp_name'][$key];
                        $_FILES['userfile']['error']= $_FILES['scheme_image']['error'][$key];
                        $_FILES['userfile']['size']= $_FILES['scheme_image']['size'][$key];    

                        $upload_dir = $path;
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir);
                        }
                        $config = array();
                        $config['upload_path'] = $upload_dir;
                        $config['allowed_types'] = 'jpeg|jpg|png';
                        $config['file_name'] = time() . $_FILES['scheme_image']['name'][$key];
                        $config['overwrite'] = false;
                        //pr($config); die;
                        $this->load->library('upload', $config);
                        //echo $_FILES['scheme_image']['name'][$key]; die;
                        if (!$this->upload->do_upload('userfile')) {
                            //print_r($this->upload->display_errors()); die;
                        } else {
                            $filedata = $this->upload->data();
                            $scheme_image = $filedata['file_name'];
                            //pr($this->upload_data['pancardDoc']); die;
                        }
                    } else {
                        //this->form_validation->set_message('filename', "No file selected"); 
                    }

                    if($update_id[$key] && empty($delete_id[$key]))
                    {
                        $updatedata  = array(
                            'scheme_id' => base64_decode($scheme_id),
                            'target_dispatched_ton' => $value, 
                            'reward_name' => $reward[$key],
                        );

                        if ($_FILES['scheme_image']['size'][$key] != 0) { 
                            $updatedata['reward_image'] = $scheme_image;
                        }
                        //echo $_FILES['scheme_image']['size'][$key];
                        //echo "<pre>"; print_r($_FILES['scheme_image']['size']); die;  
                        $condition = array('id' => $update_id[$key]);
                        $result = $this->scheme_model->UpdateSchemeDetail($updatedata,$condition);
                        if($result)
                            $added = 1;
                    }
                    elseif($delete_id[$key])
                    { 
                        $condition = array('id' => $delete_id[$key]);
                        $result = $this->scheme_model->deletedetail($condition);
                    }
                    else
                    {
                        $insertdata[]  = array(
                            'scheme_id' => base64_decode($scheme_id),
                            'target_dispatched_ton' => $value,
                            'reward_image' => $scheme_image,
                            'reward_name' => $reward[$key],
                        );
                    }
                }
            }

            //echo "<pre>"; print_r($insertdata); die;
            if($insertdata)
                $added = $this->scheme_model->AddSchemeDetail($insertdata);
            if($added)
                $this->session->set_flashdata('suc_msg','Scheme added successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');    
            redirect('schemes'); 
        }  
        $condition = array('scheme.id ' => base64_decode($scheme_id));
        $data['schemes'] = $this->scheme_model->GetSchemes($condition);
        $condition = array('scheme_id ' => base64_decode($scheme_id));
        $data['details'] = $this->scheme_model->GetSchemesDtails($condition);
        //echo "<pre>"; print_r($data['details']); die;   
        $this->load->view('scheme_detail',$data);
    }


    public function edit($scheme_id){
        $id = base64_decode($scheme_id);
        $data['title'] = "Scheme Edit";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die;   
            $this->form_validation->set_rules('brand', 'Brand','required');
            $this->form_validation->set_rules('category', 'Category','required|is_unique[brokers.pan_card]');
            //$this->form_validation->set_rules('target_dispatched_ton', 'Dispatched Weight','required');
            //$this->form_validation->set_rules('reward', 'Reward','required');
            $this->form_validation->set_rules('state', 'State','required');
            $this->form_validation->set_rules('scheme_for', 'Scheme For','required'); 
            $this->form_validation->set_rules('from_date', 'Scheme Date (From)','required');
            $this->form_validation->set_rules('to_date', 'Scheme Date (To)','required');
            //$this->form_validation->set_rules('scheme_image', 'Image', 'required');

            if ($this->form_validation->run() == false) {
            }
            else {  
                $insertdata = array(
                    'brand_id' =>trim($_POST['brand']),
                    'scheme_name' =>trim($_POST['brand']).'_'.trim($_POST['category']).'_'.trim($_POST['state']),
                    'category_id' =>trim($_POST['category']),
                    //'target_dispatched_ton' =>trim($_POST['target_dispatched_ton']),
                    //'reward_name' =>trim($_POST['reward']),
                    'scheme_state' =>trim($_POST['state']),
                    'scheme_for' =>trim($_POST['scheme_for']),
                    'from_date' => date('Y-m-d', strtotime(trim($_POST['from_date']))),
                    'to_date' =>date('Y-m-d', strtotime(trim($_POST['to_date']))),
                    'created_by' => $admin_id,
                );
                $filename = 'scheme_image';
                $path = './public/uploads/scheme_images/'; 
                if (isset($_FILES) && count($_FILES)  && $_FILES[$filename]['size'] != 0) {
                    $upload_dir = $path;
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir);
                    }
                    $config = array();
                    $config['upload_path'] = $upload_dir;
                    $config['allowed_types'] = 'jpeg|jpg|png';
                    $config['file_name'] = time() . $_FILES[$filename]['name'];
                    $config['overwrite'] = false;
                    //pr($config); die;
                    $this->load->library('upload', $config);
                    if (!$this->upload->do_upload($filename)) {
                        //pr($this->upload->display_errors()); die;
                    } else {
                        $this->upload_data[$filename] = $this->upload->data();
                        $insertdata['scheme_image'] = $this->upload_data[$filename]['file_name'];
                        //pr($this->upload_data['pancardDoc']); die;
                    }
                } else {
                    //this->form_validation->set_message('filename', "No file selected"); 
                }
                //echo "<pre>"; print_r($insertdata); die;
                $condition = array('scheme.id ' => base64_decode($scheme_id));
                $result = $this->scheme_model->UpdateScheme($insertdata,$condition);
                if($result)
                    $this->session->set_flashdata('suc_msg','Scheme updated successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');    
                redirect('schemes');
            }
        } 
        $condition = array('scheme.id ' => base64_decode($scheme_id));
        $data['scheme_info'] = $this->scheme_model->GetSchemesInfo($condition);
        $data['states'] = $this->broker_model->GetStates();
        $data['brands'] = $this->brand_model->GetAllBrand();
        $brand_id = $data['scheme_info']['brand_id'];
        $category_caondition = array('brand_id' => $brand_id);
        $data['categories'] = $this->category_model->GetCategory($category_caondition);
        //echo "<pre>"; print_r($data['scheme_info']); die; 
        $this->load->view('scheme_edit',$data);
    }

    public function updateStatus() {
        $id = $this->input->post('id');         
        $status = $this->input->post('status'); 
        if (empty($id)) {
            echo '0';
            die;
        }
        if ($this->scheme_model->updateStatus($id, $status)) {
            echo '1';
        } else {
            echo '0';
            } 
    } 
}