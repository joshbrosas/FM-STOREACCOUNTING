<?php $this->load->view('main/header'); ?>

<?php if($this->session->flashdata('message') != ''){ ?>
		<div class="alert alert-success alert-dismissible" role="alert" id="alertclose">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<strong><i class="fa fa-info"></i> <?php echo $this->session->flashdata("message"); ?></strong>
		</div>
<?php } ?>
<form method="post" class="form-inline">
  <form class="form-inline">
  <div class="form-group">
    <label>Filter Date: </label>
    <div class="form-group input-group">
        <input type="text" id="dpd1" name="datefrom" value="<?php echo $this->session->userdata("date"); ?>" class="form-control input-sm">
       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div>
  </div>
  <input name="btnfilter" value="Filter" class="btn btn-success btn-sm" type="submit">
<hr>
<table class="table table-bordered" style="font-size: 12px">
    <thead style="font-size: 11px">
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
        <th><input type="checkbox" id="selectall">SELECT ALL</th>
      </tr>
    </thead>
    <?php if(isset($payables)) { ?>
<button type="submit" class="btn btn-success btn-circle btn-lg pull-right" style="padding:0px;z-index: 9999999;outline: 0;margin-right: 35px;margin-bottom:25px;position: fixed;right: 0;bottom:0;-webkit-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);transition: 1px ease"><i class="fa fa-check"></i></button>

      <?php foreach($payables as $values){ ?>
        <tr>
        <td><?php echo $values['PONUMB']; ?></td>
        <td><?php echo $values['POMRCV']; ?></td>
        <td><?php echo $values['POLOC']; ?></td>
        <td><?php echo $values['ASNAME']; ?></td>
        <td><?php echo $values['ASTRMS']; ?></td>
        <td><?php echo trim($values['PORDAT']); ?></td>
        <td><?php echo $values['POLADG']; ?></td>
        <td><?php echo number_format($values['PORVCS'], 2); ?></td>
        <?php if(number_format($values['POSHPR'],2) != number_format($values['PORVCS'],2)){ ?>
      <td style="background-color:#FA5858;color:#ffffff;"><?php echo number_format($values['POSHPR'], 2); ?></td>
      <td><input type="text" onkeypress="toggle(<?php echo $values['PONUMB']; ?>)" name="txt_<?php echo $values['PONUMB']; ?>" id="<?php echo $values['PONUMB']; ?>"></td>
      <?php }else{ ?>
      <td style="background-color:#FFFFFF"><?php echo number_format($values['POSHPR'], 2); ?></td>
      <td></td>
      <?php } ?>
      <input type="hidden" name="hdn_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['POMRCV']; ?>">
        
        <td><input type="checkbox" name="selector[]" id="check_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['PONUMB']; ?>"></td>
      <?php } ?>
  </table>
<?php } ?> 
  </form>

<?php $this->load->view('main/footer'); ?>