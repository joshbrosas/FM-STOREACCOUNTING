<?php $this->load->view('main/header'); ?>

<?php if($this->session->flashdata('message') != ''){ ?>
		<div class="alert alert-success alert-dismissible" role="alert" id="alertclose">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<strong><i class="fa fa-info"></i> <?php echo $this->session->flashdata("message"); ?></strong>
		</div>
<?php } ?>
<form method="post" action="<?php echo site_url('payables/postpayables')?>" class="form-inline" id="formpayables">
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
    <?php if(isset($payables)) { ?>

<button type="submit" class="btn btn-success btn-circle btn-lg pull-right" style="padding:0px;z-index: 9999999;outline: 0;margin-right: 35px;margin-bottom:25px;position: fixed;right: 0;bottom:0;-webkit-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);transition: 1px ease"><i class="fa fa-check"></i></button>

      <?php foreach($payables as $values){ ?>
      <input type="hidden" name="hdn_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['POMRCV']; ?>">
      <input type="hidden" name="RCV_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['POMRCV']; ?>">
      <input type="hidden" name="LOC_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['POLOC']; ?>">
      <input type="hidden" name="NME_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['ASNAME']; ?>">
      <input type="hidden" name="TRMS_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['ASTRMS']; ?>">
      <input type="hidden" name="DAT_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['PORDAT']; ?>">
      <input type="hidden" name="ADG_<?php echo $values['PONUMB']; ?>" value="<?php echo trim($values['POLADG']); ?>">
      <input type="hidden" name="VCS_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['PORVCS']; ?>">
      <input type="hidden" name="HPR_<?php echo $values['PONUMB']; ?>" value="<?php echo trim($values['POSHPR']); ?>">
        <tr>
        <td><?php echo $values['PONUMB']; ?></td>
        <td><?php echo $values['POMRCV']; ?></td>
        <td><?php echo $values['POLOC']; ?></td>
        <td class="col-md-3"><?php echo $values['ASNAME']; ?></td>
        <td><?php echo $values['ASTRMS']; ?></td>
        <td><?php echo trim($values['PORDAT']); ?></td>
        <td><input type="textbox" value="<?php echo $values['POLADG']; ?>"></td>
        <td><?php echo number_format($values['PORVCS'], 2); ?></td>
        <?php if(number_format($values['POSHPR'],2) != number_format($values['PORVCS'],2)){ ?>
      <td style="background-color:#FA5858;color:#ffffff;"><?php echo number_format($values['POSHPR'], 2); ?></td>
      <td><input type="text" onkeypress="toggle(<?php echo $values['PONUMB']; ?>)" name="txt_<?php echo $values['PONUMB']; ?>" id="txt_<?php echo $values['PONUMB']; ?>"></td>
      <td><input type="checkbox" name="selector[]" onclick="return false" id="check_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['PONUMB']; ?>"></td>
      <?php }else{ ?>
      <td style="background-color:#FFFFFF"><?php echo number_format($values['POSHPR'], 2); ?></td>
      <td></td>
      <td><input type="checkbox" name="selector[]" id="check_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['PONUMB']; ?>"></td>
      <?php } ?>



      <?php } ?>
  </table>
<?php }else{ ?>
    <td colspan="11"><div class="alert alert-success" style="margin-bottom: 0px">Please Select date.</div></td>
<?php } ?>
  </form>
<script>
// $( "input" ).keypress(function(e) {
//     var a = [];
//     var k = e.which;
//
//     for (i = 48; i < 58; i++)
//     a.push(i);
//
//     // allow a max of 1 decimal point to be entered
//     if (this.value.indexOf(".") === -1) {
//         a.push(46);
//     }
//
//     if (!(a.indexOf(k) >= 0)) e.preventDefault();
// });

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
