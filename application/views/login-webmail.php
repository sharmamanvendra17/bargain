<!doctype html>
<html lang="en-US">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title><?php echo $title; ?></title>
        <meta name="description" content="" />
        <meta name="Author" content="Dorin Grigoras [www.stepofweb.com]" />

        <!-- mobile settings -->
        <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />

        <!-- WEB FONTS -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800&amp;subset=latin,latin-ext,cyrillic,cyrillic-ext" rel="stylesheet" type="text/css" />

        <!-- CORE CSS -->
        <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        
        <!-- THEME CSS -->
        <link href="<?php echo base_url(); ?>assets/css/essentials.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>assets/css/layout.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>assets/css/color_scheme/green.css" rel="stylesheet" type="text/css" id="color_scheme" />

    </head>
    <!--
        .boxed = boxed version
    -->
    <body>


        <div class="padding-15">

            <div class="login-box">

                <!-- login form -->
                <form action="" class="sky-form boxed" method="post">
                    <header style="text-align: center;">Sales Management</header>

                    <!--
                    <div class="alert alert-danger noborder text-center weight-400 nomargin noradius">
                        Invalid Email or Password!
                    </div>

                    <div class="alert alert-warning noborder text-center weight-400 nomargin noradius">
                        Account Inactive!
                    </div>

                    <div class="alert alert-default noborder text-center weight-400 nomargin noradius">
                        <strong>Too many failures!</strong> <br />
                        Please wait: <span class="inlineCountdown" data-seconds="180"></span>
                    </div>
                    -->

                    <fieldset>  
                        <?php if($this->session->flashdata('err_msg')) { ?>
                                <div class="alert alert-danger noborder text-center weight-400 nomargin noradius">
                                    <?php echo $this->session->flashdata('err_msg'); ?>
                                </div>
                            <?php } ?>

                        <section>
                            <label class="label">Enter Code</label>
                            <label class="input"> 
                                <input type="text" id="code" name="code" placeholder="Enter Code" required="">
                                <span class="tooltip tooltip-top-right">Enter Code</span>
                                <?php echo form_error('code'); ?>
                            </label>
                        </section>  

                    </fieldset>

                    <footer>
                        <input type="submit" name="submit" value="Login"> 
                    </footer>
                </form>
                <!-- /login form -->  
                </div>

            </div>

        </div>

        <!-- JAVASCRIPT FILES -->
   
    </body>
</html>