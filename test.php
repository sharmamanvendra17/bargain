<span class="add-more">Add more</span>
<div class="view">
<select class="form-control viewer"  name="viewer[]" multiple> 
	<option  value="">Select Bragain Approver</option>
	<option value="130">Ajay Gupta - ajay.g@datagroup.in </option>
    <option value="26">Arvind Paharia - arvind.p@datagroup.in </option>
    <option value="99">Babulal - babu.lal@datagroup.in </option>
    <option value="74">Devesh_view - devesh@datagroup.in </option>
    <option value="47">Joginder Singh Bedi - joginder@datagroup.in </option>
    <option value="75">Narendra Vashishtha - nv@datagroup.in </option>
    <option value="28">Pankaj Gupta - pankajkumar.gupta@datafoods.com </option>
    <option value="135">Sanjay Gupta - sanjaykumar.gupta@datafoods.com </option>
    <option value="24">Shailendra Singh - ss@datagroup.in </option>
</select>
<div>
<div class="more_div"></div>
<script type="text/javascript" src="https://sales.datagroup.in/assets/plugins/jquery/jquery-2.2.3.min.js"></script>
<script src='https://sales.datagroup.in/assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='https://sales.datagroup.in/assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
    $(document).ready(function(){ 
        $(".viewer").select2();

        $(".add-more").click(function () {
        	$(".view").append('<select class="form-control viewer"  name="viewer[]" multiple><option  value="">Select Bragain Approver</option><option value="130">Ajay Gupta - ajay.g@datagroup.in </option><option value="26">Arvind Paharia - arvind.p@datagroup.in </option><option value="99">Babulal - babu.lal@datagroup.in </option><option value="74">Devesh_view - devesh@datagroup.in </option><option value="47">Joginder Singh Bedi - joginder@datagroup.in </option><option value="75">Narendra Vashishtha - nv@datagroup.in </option><option value="135">Sanjay Gupta - sanjaykumar.gupta@datafoods.com </option><option value="24">Shailendra Singh - ss@datagroup.in </option></select>');
		    alert("Hello!"); 
		    $(".viewer").select2();
		});     
    });
</script>