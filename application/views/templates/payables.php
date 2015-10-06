<?php $this->load->view('main/header'); ?>
<div class="table-responsive">
<?php if($this->session->flashdata('message') != ''){ ?>
		<div class="alert alert-success alert-dismissible" role="alert" id="alertclose">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<strong><i class="fa fa-info"></i> <?php echo $this->session->flashdata("message"); ?></strong>
		</div>
<?php } ?>
<form method="post">
<table class="table table-bordered" style="font-size: 12px">
<input type="submit" class="btn btn-success pull-right" style="margin-bottom: 5px;" value ="Post">
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
			<?php }else{ ?>
			<td style="background-color:#FFFFFF"><?php echo number_format($values['POSHPR'], 2); ?></td>
			<?php } ?>
			<input type="hidden" name="hdn_<?php echo $values['PONUMB']; ?>" value="<?php echo $values['POMRCV']; ?>">
		    <td><input type="text" name="txt_<?php echo $values['PONUMB']; ?>"></td>
		    <td><input type="checkbox" name="selector[]" value="<?php echo $values['PONUMB']; ?>"></td>
      </tr>
      <?php } ?>
  </table>
  </form>
</div>
<?php $this->load->view('main/footer'); ?>