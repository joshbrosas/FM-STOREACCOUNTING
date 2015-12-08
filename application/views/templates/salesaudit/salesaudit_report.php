<?php $this->load->view('main/header'); ?>
<form method="post" action="<?php echo site_url('salesaudit/exportreport')?>" class="form-inline" id="formpayables">
<?php if($this->session->flashdata('message') != ''){ ?>
    <div class="alert alert-success alert-dismissible" role="alert" id="alertclose">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong><i class="fa fa-info"></i> <?php echo $this->session->flashdata("message"); ?></strong>
    </div>
<?php } ?>
  <table class="table table-bordered table-hover" style="font-size: 12px">
	<thead style="font-size: 11px">
      <tr>
        <th>LOCATION</th>
        <th>TOTALSALES</th>
        <th>VATEXMT</th>
        <th>CREDITCARD</th>
       <th>DATE</th>
       <th>ACTION</th>
      </tr>
    </thead>
 <?php if(count($salesreport) == 0){ ?>     
              <td colspan="6"><div class="alert alert-success" style="margin-bottom: 0px">No records yet.</div></td>  
        <?php } ?>
        <?php foreach($salesreport as $values){?>
          <tr>
            <td class="col-md-2"><?php echo $values->sa_location; ?></td>
            
            <td class="col-md-1" style="text-align: right"><?php echo number_format($values->sa_totalsales, 0); ?></td>
            <td class="col-md-1" style="text-align: right"><?php echo number_format($values->sa_vatexmt, 0); ?></td>
            <td class="col-md-1" style="text-align: right"><?php echo number_format($values->sa_creditcard, 0); ?></td>
            <td class="col-md-1" style="text-align: right"><?php echo $values->sa_curr_date?></td>
            <td class="col-md-1"><a onclick="return confirm('Are you sure want to delete selected data?')" href="<?php echo base_url(); ?>index.php/salesaudit/salesaudit_remove/<?php echo $values->id ?>" class="btn btn-xs btn-danger" title="Remove"><i class="fa fa-trash"></i> Remove</a></td>
          </tr>
       
        <?php } ?>
</table>
 <button type="submit" class="btn btn-success btn-circle btn-lg pull-right" id="btn_sales" title="Export to CSV" style="padding:0px;z-index: 9999999;outline: 0;margin-right: 35px;margin-bottom:25px;position: fixed;right: 0;bottom:0;-webkit-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
  -moz-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
  box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);transition: 1px ease;"><i class="fa fa-file-excel-o"></i></button> 
</form>

<?php $this->load->view('main/footer'); ?>