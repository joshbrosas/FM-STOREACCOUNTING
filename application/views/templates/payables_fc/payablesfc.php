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

	<div class="form-group">
			<label for="exampleInputEmail2">Location: </label>
			<input type="text" class="form-control input-sm" id="" name="location" value="<?php echo set_value('location', ''); ?>" placeholder="">
	</div>

	<div class="form-group">
			<label for="exampleInputEmail2">POVNUM: </label>
			<input type="text" class="form-control input-sm" id="" value="<?php echo set_value('vendor', ''); ?>" name="vendor" placeholder="">
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

    <?php if(isset($records)){ ?>
    <button type="submit" class="btn btn-success btn-circle btn-lg pull-right" style="padding:0px;z-index: 9999999;outline: 0;margin-right: 35px;margin-bottom:25px;position: fixed;right: 0;bottom:0;-webkit-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
    -moz-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
    box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);transition: 1px ease"><i class="fa fa-check"></i></button>

    <?php
       $AS400 = odbc_connect ('ansilive', 'pfmadmin', 'M@nager3971' ) or die ( 'Can not connect to server' );
     ?>

        <?php foreach ($records as $key => $value) { ?>

         <?php
          $ponumb = $value['PONUMB'];

          $sql_str= "select a.im_refno as JDAPO,a.im_branch,a.im_refno2 as ANSIPO,a.im_srcdest as VENDOR,e.sp_name,sum(b.is_cost*b.is_qty) as TotalCost
                from [HOVQPBOS].[dbo].tinvmain a
                right join [HOVQPBOS].[dbo].tinvsub b on b.is_iminvno=a.im_invno and a.im_branch=b.is_branch and a.im_invtype=b.is_invtype
                inner join [HOVQPBOS].[dbo].mproduct c on b.is_itemid = c.pd_prodid
                inner join [HOVQPBOS].[dbo].msupplr e on a.im_srcdest=e.sp_code
                where a.im_invtype='P'
                and a.im_srcdest not in('099998') and a.im_candate='1900-01-01 00:00:00' and a.im_refno=$ponumb
                group by   a.im_refno,a.im_branch,a.im_refno2,a.im_srcdest,e.sp_name
                order by a.im_refno,a.im_branch";

          $detailx = odbc_exec($AS400,$sql_str);

          while (odbc_fetch_row($detailx))
          {
          $jdapo      = trim(odbc_result($detailx,1));
          $branch     = odbc_result($detailx,2);
          $ansipo     = odbc_result($detailx,3);
          $vendor     = odbc_result($detailx, 4);
          $spname     = odbc_result($detailx, 5);
          $totalcost  = odbc_result($detailx, 6);
          ?>

           <tr>
              <td><?php echo $jdapo; ?></td>
              <td><?php echo $vendor; ?></td>
              <td><?php echo $branch; ?></td>
              <td><?php echo $spname; ?></td>
              <td>0</td>
              <td><?php echo $value['POSDAT']; ?></td>
              <td><?php echo $ansipo; ?></td>
              <td><?php echo number_format($totalcost, 2); ?></td>

              <?php if($totalcost != $value['POCOST']){?>
              <td style="background-color:#FA5858;color:#ffffff;"><?php echo number_format($value['POCOST'], 2); ?></td>
              <?php }else{ ?>
              <td><?php echo number_format($value['POCOST'], 2); ?></td>
              <?php } ?>

              <input type="hidden" name="hdn_<?php echo $jdapo; ?>"   value="<?php echo $vendor; ?>">
              <input type="hidden" name="branch_<?php echo $jdapo; ?>" value="<?php echo $branch; ?>">
              <input type="hidden" name="spname_<?php echo $jdapo; ?>" value="<?php echo $spname; ?>">
              <input type="hidden" name="payterm_<?php echo $jdapo; ?>" value="0">
              <input type="hidden" name="recdate_<?php echo $jdapo; ?>" value="<?php echo $value['POSDAT']; ?>">
              <input type="hidden" name="ansipo_<?php echo $jdapo; ?>" value="<?php echo $ansipo; ?>">
              <input type="hidden" name="rcr_<?php echo $jdapo; ?>" value="<?php echo $totalcost; ?>">
              <input type="hidden" name="invoice_<?php echo $jdapo; ?>" value="<?php echo $value['POCOST']; ?>">

              <?php if($totalcost != $value['POCOST']){?>
                <td><input type="text" onkeypress="toggle(<?php echo $jdapo; ?>)" name="txt_<?php echo $jdapo ?>" id="txt_<?php echo $jdapo; ?>"></td>
                <td><input type="checkbox" name="selector[]" onclick="return false" id="check_<?php echo $jdapo; ?>" value="<?php echo $jdapo; ?>"></td>
              <?php }else{ ?>
                <td></td>
                <td><input type="checkbox" name="selector[]"  id="check_<?php echo $jdapo; ?>" value="<?php echo $jdapo; ?>"></td>
              <?php } ?>
          </tr>

        <?php } ?>
   <?php } ?>

  <?php }else{ ?>
    <td colspan="11"><div class="alert alert-success" style="margin-bottom: 0px">Please select date.</div></td>
  <?php } ?>
  </table>
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

$(document).ready(function(){
  $('button[type="submit"]').click(function(){
    var checked = $("input[type=checkbox]:checked"); //find all checked checkboxes + radio buttons
    var nbChecked = checked.size();
      if(nbChecked == 0)
        {
          alert('Please select a record.');
          return false;
        }
        });
});

</script>
<?php $this->load->view('main/footer'); ?>
