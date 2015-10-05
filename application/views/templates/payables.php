<?php $this->load->view('main/header'); ?>
<table class="table table-bordered" style="font-size: 12px">
    <thead>
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
        <th>OK TO PROCESS</th>
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
		    <td><input type="text"></td>
		    <td><input type="checkbox"> Ok</td>
      </tr>
      <?php } ?>
  </table>
<?php $this->load->view('main/footer'); ?>