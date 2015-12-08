<?php $this->load->view('main/header'); ?>
<form method="post" action="<?php echo site_url('payables/filter_consignment')?>" class="form-inline" id="formconsignment">
  <div class="form-group">
    <label>Filter Date: </label>
    <div class="form-group input-group input-append date">
        <input type="text" id="dpd1" name="datefilter1" style="background-color: #fff;cursor: pointer" placeholder="Date From" value="<?php echo set_value('datefilter1', ''); ?>" class="form-control input-sm" readonly>
       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div>
    <div class="form-group input-group input-append date">
        <input type="text" id="dpd2" name="datefilter2" style="background-color: #fff;cursor: pointer" placeholder="Date To" value="<?php echo set_value('datefilter2', ''); ?>" class="form-control input-sm" readonly>
       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div>
  </div>
  <input name="btnfilter" value="Filter" class="btn btn-success btn-sm" type="submit">
<hr>


<div class="row">
<div class="col-md-offset-1 col-md-9">
  <?php if($this->session->flashdata('message') != ''){ ?>
  <div class="alert alert-success alert-dismissible" role="alert" id="alertclose">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong><i class="fa fa-file"></i> <?php echo $this->session->flashdata("message"); ?></strong>
</div>
<?php } ?>
<table class="table table-bordered table-hover" style="font-size: 12px">
	<thead style="font-size: 11px">
      <tr>
        <th>VENDOR</th>
        <th>TOTAL SALES</th>
      </tr>
    </thead>
    <?php if(isset($result)){ ?>
        <?php foreach($result as $values){?>
          <tr>
            <td class="col-md-1"><?php echo $values['VENDOR']; ?></td>
            <td class="col-md-1" style="text-align: right"><?php echo number_format($values['TOTALSALES'], 2); ?></td>
          </tr>
        <?php } ?>
    <?php }else{ ?>
    <td colspan="2"><div class="alert alert-success" style="margin-bottom: 0px">Please select date.</div></td>
    <?php } ?> 
</table>
</div>	
  <?php if(isset($result)){ ?>
    <button type="submit" name="btnexport" value="export" data-toggle="modal" data-target="#myModal" class="btn btn-success btn-circle btn-xl pull-right" title="Export to CSV" style="padding:0px;outline: 0;margin-right: 35px;margin-bottom:25px;position: fixed;right: 0;bottom:0;-webkit-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);transition: 1px ease"><i class="fa fa-file-excel-o"></i></button>
    <?php } ?>
</form>
<?php $this->load->view('main/footer'); ?>