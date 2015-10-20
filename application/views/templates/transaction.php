<?php $this->load->view('main/header'); ?>

<?php if($this->session->flashdata('message') != ''){ ?>
          <div class="alert alert-success alert-dismissible" role="alert" id="alertclose">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong><i class="fa fa-file"></i> <?php echo $this->session->flashdata("message"); ?></strong>
        </div>
        <?php } ?>

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
        <th>NEW AMT</th>      
      </tr>
    </thead>

<?php if(isset($transaction)){ ?>
  <?php foreach($transaction as $index =>  $values){ ?>
        <tr>
        <td><?php echo $values['PONUMB']; ?></td>
        <td><?php echo $values['POMRCV']; ?></td>
        <td><?php echo $values['POLOC']; ?></td>
        <td><?php echo $values['ASNAME']; ?></td>
        <td><?php echo $values['ASTRMS']; ?></td>
        <td><?php echo trim($values['PORDAT']); ?></td>
        <td><?php echo $values['POLADG']; ?></td>
        <td><?php echo number_format($values['PORVCS'], 2); ?></td>
        <td><?php echo number_format($values['POSHPR'], 2); ?></td>
      	<td><?php echo number_format($amount[$index], 2); ?></td>
      <?php } ?>

  <?php } ?>
<?php if(count($transaction) == 0){ ?>
<td colspan="10"><div class="alert alert-success" style="margin-bottom:0px;font-size:12px">No Records found.</div></td>
<?php } ?>
</table>
 <?php if(count($count_status) != 0){ ?>
<form method="post" action="<?php echo site_url('payables/postException')?>">
  <button type="submit"  class="btn btn-success btn-circle btn-lg pull-right" title="Process SAP File" style="padding:10px;z-index: 9999999;outline: 0;margin-right: 35px;margin-bottom:25px;position: fixed;right: 0;bottom:0;-webkit-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);"><i class="fa fa-file"></i></button>
</form>
<?php } ?>
<?php $this->load->view('main/footer'); ?>