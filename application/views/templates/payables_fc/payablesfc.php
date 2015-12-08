<?php $this->load->view('main/header'); ?>

<?php if($this->session->flashdata('message') != ''){ ?>
		<div class="alert alert-success alert-dismissible" role="alert" id="alertclose">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<strong><i class="fa fa-info"></i> <?php echo $this->session->flashdata("message"); ?></strong>
		</div>
<?php } ?>
<form method="post" action="<?php echo site_url('payablesfc/postpayables')?>" class="form-inline" id="formpayables">
  <div class="form-group">
    <label>Filter Date: </label>
    <div class="form-group input-group">
        <input type="text" id="dpd1" readonly style="background-color: #fff;cursor: pointer" name="date" value="<?php echo set_value('date', ''); ?>"  class="form-control input-sm">
       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div>
  </div>
  <input name="btnfilter" value="Filter" class="btn btn-success btn-sm" type="submit">
<hr>
<table class="table table-bordered table-hover" id="dataTables-example" style="font-size: 12px">
    <thead style="font-size: 12px" class="table-condensed">
      <tr>
        <th>PO NO.</th>
        <th>RCR NO.</th>
        <th>LOCATION</th>
        <th>VENDOR</th>
        <th>PAYMENT TERM</th>
        <th>REC DATE</th>
        <th>INVOICE #</th>
        <th>RCR AMT</th>
        <th>INVOICE AMT</th>
        <th>NEW AMOUNT</th>
        <th><input type="checkbox" class="control-label" id="selectall">SELECT ALL</th>
      </tr>
    </thead>
  </form>
<script>
$( "input" ).keypress(function(e) {
    var a = [];
    var k = e.which;

    for (i = 48; i < 58; i++)
    a.push(i);

    // allow a max of 1 decimal point to be entered
    if (this.value.indexOf(".") === -1) {
        a.push(46);
    }

    if (!(a.indexOf(k) >= 0)) e.preventDefault();
});
</script>
<?php $this->load->view('main/footer'); ?>