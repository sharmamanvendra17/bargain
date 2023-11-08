
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<title>DATA GROUP ORDER LOGIN</title>
<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/new/favicon.ico">
<!--Core CSS  title for russia Почта.РУС -->
<link href="<?php echo base_url(); ?>assets/css/new/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/css/styles-new.css" rel="stylesheet">
</head>


<body>
<section class="bg-yellow">
    <div class="container">
        <div class="row">
            <div class="form-box">
                <div class="col-md-12 mb-3">
                <img class="img-fluid" src="<?php echo base_url(); ?>assets/images/new/logo.png">
                </div>
                <div class="form-content">
                    <h4>Order Login</h4>
                    <form action="" class="sky-form boxed" method="post">
                        <div class="form-bg-white">
                            <?php if($this->session->flashdata('err_msg')) { ?>
                                <div class="alert alert-danger noborder text-center weight-400 nomargin noradius">
                                    <?php echo $this->session->flashdata('err_msg'); ?>
                                </div>
                            <?php } ?>
                            <div class="col-md-12 mb-3">
                                <label style="display: none;" for="exampleFormControlInput1" class="form-label"> Choose Your Production Unit</label>
                                <div class="row" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-control">                            
                                            <div class="form-check form-check-inline" style="padding-left: 1.2em;">
                                              <input class="form-check-input" type="radio" name="store" id="inlineRadio1" value="Jaipur" checked>
                                              <label class="form-check-label" for="inlineRadio1">Jaipur</label>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-control"> 
                                            <div class="form-check form-check-inline" style="padding-left: 1.2em;">
                                              <input class="form-check-input" type="radio" name="store" id="inlineRadio2" value="Alwar">
                                              <label class="form-check-label" for="inlineRadio2">Alwar</label>
                                            </div> 
                                        </div>
                                    </div>                          
                                </div>
                                <div class="col-md-12 mb-3 mt-3" style="display: none;"> 
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-control">     
                                                <div class="form-check form-check-inline" style="padding-left: 1.2em;">
                                                  <input class="form-check-input" type="radio" name="store" id="inlineRadio3" value="Jaipur_Modern_Tardes">
                                                  <label class="form-check-label" for="inlineRadio3">Jaipur Modern Tardes</label>
                                                </div> 
                                            </div>      
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-control">   
                                                <div class="form-check form-check-inline" style="padding-left: 1.2em;">
                                                  <input class="form-check-input" type="radio" name="store" id="inlineRadio4" value="Sales_Core_Team">
                                                  <label class="form-check-label" for="inlineRadio4">Sales Core Team</label>
                                                </div>
                                            </div>      
                                        </div>
                                    </div>                    
                                </div>
                            <div class="col-md-12 mb-3">
                              <label for="code" class="form-label">Get one time login code from your app</label>
                              <input id="code" name="code" type="text" class="form-control" id="code" placeholder="Enter Code"  style="text-transform:uppercase" autofocus autocomplete="off">
                            </div>
                            <div class="col-md-12">
                              <input type="submit" value="Login" class="form-control login-btn">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>
    
  