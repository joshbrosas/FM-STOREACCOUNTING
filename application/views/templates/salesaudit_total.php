<?php $this->load->view('main/header'); ?>
<form method="post" action="<?php echo site_url('salesaudit/filter_salesaudit_total')?>" onsubmit = "validate()" class="form-inline" id="formpayables">
  <div class="form-group">
    <label>Filter Date: </label>
    <div class="form-group input-group input-append date">
        <input type="text" id="dpd1" name="datefilter" style="background-color: #fff;cursor: pointer" value="<?php echo set_value('datefilter', ''); ?>" class="form-control input-sm" readonly>
       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div>
  </div>
  <input name="btnfilter" value="Filter" class="btn btn-success btn-sm" type="submit">
<hr>
<?php if($this->session->flashdata('message') != ""){ ?>
    <div class="alert alert-danger alert-dismissible" role="alert" style="font-size: 13px">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Warning!</strong> <?php echo $this->session->flashdata("message"); ?>
    </div>
    <?php } ?>
<table class="table table-bordered table-hover" style="font-size: 12px">
	<thead style="font-size: 11px">
      <tr>
        <th>LOCATION</th>
        <th>TOTALSALES</th>
        <th>VATEXMT</th>
        <th>CREDITCARD</th>
        <th><input type="checkbox" name="selector[]" id="selectsales" value="" class="control-label">SELECT ALL</th>
      </tr>
    </thead>
    <?php if(isset($salesaudit)){ ?>
    <button type="submit" class="btn btn-success btn-circle btn-lg pull-right" style="padding:0px;z-index: 9999999;outline: 0;margin-right: 35px;margin-bottom:25px;position: fixed;right: 0;bottom:0;-webkit-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);transition: 1px ease" title="Add to report"><i class="fa fa-check"></i></button>
        
        <?php foreach($salesaudit as $values){?>
          <tr>
            <td class="col-md-2"><?php echo $values['STRNAM']; ?></td>
            
            <td class="col-md-1" style="text-align: right"><?php echo number_format($values['00003'], 0); ?></td>
            <td class="col-md-1" style="text-align: right"><?php echo number_format($values['00004'], 0); ?></td>
            <td class="col-md-1" style="text-align: right"><?php echo number_format($values['00005'], 0); ?></td>
            <input type="hidden" name="ts_<?php echo $values['STRNUM'] ?>" value="<?php echo $values['00003']; ?>">
            <input type="hidden" name="loc_<?php echo $values['STRNUM'] ?>" value="<?php echo $values['STRNAM']; ?>">
             <input type="hidden" name="vatexmt_<?php echo $values['STRNUM'] ?>" value="<?php echo $values['00004']; ?>">
              <input type="hidden" name="cc_<?php echo $values['STRNUM'] ?>" value="<?php echo $values['00005']; ?>">
            <td class="col-md-1"><input type="checkbox" name="selector[]" id="" class="control-label" value="<?php echo $values['STRNUM'] ?>"></td>
          </tr>
        <?php } ?>
    <?php }else{ ?>
    <td colspan="7"><div class="alert alert-success" style="margin-bottom: 0px">Please select date.</div></td>
    <?php } ?> 
</table>

 <!--  <button type="submit" class="btn btn-success btn-circle btn-lg pull-right" id="btn_sales" title="Export to CSV" style="padding:0px;z-index: 9999999;outline: 0;margin-right: 35px;margin-bottom:25px;position: fixed;right: 0;bottom:0;-webkit-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
  -moz-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
  box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);transition: 1px ease;"><i class="fa fa-file"></i></button> -->
</form>
<script type="text/javascript">
  $(document).ready(function(){
        $('button[type="submit"]').click(function(){
        var checked = $("input[type=checkbox]:checked"); //find all checked checkboxes + radio buttons
        var nbChecked = checked.size();
        if(nbChecked == 0)
        {
          alert('Please select location.');
          return false;
        }
        });
    });
</script>
<?php $this->load->view('main/footer'); ?>