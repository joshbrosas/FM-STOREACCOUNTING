<?php $this->load->view('main/header'); ?>

<?php if($this->session->flashdata('message') != ''){ ?>
          <div class="alert alert-success alert-dismissible" role="alert" id="alertclose">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong><i class="fa fa-file"></i> <?php echo $this->session->flashdata("message"); ?></strong>
        </div>
        <?php } ?>

<table class="table table-bordered" style="font-size: 11px">
    <thead style="font-size: 12px">
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
      </tr>
    </thead>

<?php if(isset($process)){ ?>
  <?php foreach($process as $values){ ?>
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
      
      <?php } ?>

  <?php } ?>
<?php if(count($process) == 0){ ?>
<td colspan="9"><div class="alert alert-success" style="margin-bottom:0px;font-size:12px">No Records found.</div></td>
<?php } ?>
  </table>
  <?php if(count($count_status) != 0){ ?>
  <form method="post" action="<?php echo site_url('payables/postMatched')?>">
    <button type="submit" data-toggle="modal" data-target="#myModal" class="btn btn-success btn-circle btn-xl pull-right" title="Process SAP File" style="padding:0px;outline: 0;margin-right: 35px;margin-bottom:25px;position: fixed;right: 0;bottom:0;-webkit-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);
box-shadow: 0px 0px 8px -1px rgba(0,0,0,0.75);transition: 1px ease"><i class="fa fa-file-excel-o"></i></button>
</form>
<?php } ?>
 <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel">CSV Report</h4>
          </div>
          <div class="modal-body">
            <div class="progress progress-striped active">
                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    <span class="sr-only">Generating CSV Report...</span>
                    <strong>Generating CSV Report...</strong>
                </div>
            </div>
          </div>
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<?php $this->load->view('main/footer'); ?>