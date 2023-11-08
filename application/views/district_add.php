<?php include 'header.php'; ?>
    <section id="middle">
        <header id="page-header">
            <h1><?php echo $title; ?></h1> 
        </header>
        <div id="content" class="padding-20">
            <div id="panel-1" class="panel panel-default">
                <div class="panel-heading">
                    <span class="title elipsis">
                        <strong><?php echo $title; ?></strong>
                    </span>  
                </div>
                <div class="panel-body">
                    <?php if($this->session->flashdata('err_msg')) { ?>
                        <div class="alert alert-danger noborder text-center weight-400 nomargin noradius">
                            <?php echo $this->session->flashdata('err_msg'); ?>
                        </div>
                    <?php } ?>
                    <?php if($this->session->flashdata('suc_msg')) { ?>
                        <div class="alert alert-success noborder text-center weight-400 nomargin noradius">
                            <?php echo $this->session->flashdata('suc_msg'); ?>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" class="" method="post">
                                <div class="form-group">
                                    <label for="state_name">State Name</label> 
                                    <select class="form-control" id="state_name" name="state_name" required="" >
                                        <?php if($states) { 
                                            foreach ($states as $key => $state) { ?>
                                            <option value="<?php echo $state['id']; ?>"><?php echo $state['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('state_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="district_name">District Name</label> 
                                    <input type="text" class="form-control" id="district_name" name="district_name" required="" value="<?php if(isset($_POST['district_name'])) echo $_POST['district_name']; ?>">
                                    <span class="txt-danger"><?php echo form_error('district_name'); ?></span>
                                </div>
                                <button type="submit" class="btn btn-default">Save</button> 
                            </form>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include 'footer.php'; ?>