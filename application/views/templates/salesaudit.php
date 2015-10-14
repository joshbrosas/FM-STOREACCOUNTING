<?php $this->load->view('main/header'); ?>
<form method="post" action="<?=site_url('payables/filter_salesaudit')?>" class="form-inline" id="formpayables">
  <div class="form-group">
    <label>Filter Date: </label>
    <div class="form-group input-group">
        <input type="text" id="dpd1" name="datefilter"  value="<?php echo set_value('datefilter', ''); ?>" class="form-control input-sm">
       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div>
  </div>
  <input name="btnfilter" value="Filter" class="btn btn-success btn-sm" type="submit">
<hr>
<table class="table table-bordered table-hover" style="font-size: 12px">
	<thead style="font-size: 11px">
      <tr>
        <th>LOCATION</th>
        <th>POS</th>
        <th>TOTALSALES</th>
        <th>VATEXMT</th>
        <th>CREDITCARD</th>
      </tr>
    </thead>
    <?php if(isset($salesaudit)){ ?>
        <?php foreach($salesaudit as $values){?>
          <tr>
            <td class="col-md-1"><?php echo $values['STRNAM']; ?></td>
            <td class="col-md-1"><?php echo $values['CSREG']; ?></td>
            <td class="col-md-1"><?php echo number_format($values['00003'], 0); ?></td>
            <td class="col-md-1"><?php echo number_format($values['00004'], 0); ?></td>
            <td class="col-md-1"><?php echo number_format($values['00005'], 0); ?></td>
          </tr>
        <?php } ?>
    <?php }else{ ?>
    <td colspan="5"><div class="alert alert-success" style="margin-bottom: 0px">Please select date.</div></td>
    <?php } ?> 
</table>

</form>


<?php $this->load->view('main/footer'); ?>