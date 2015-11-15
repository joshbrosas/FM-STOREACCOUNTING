<?php $this->load->view('main/header'); ?>
	<div class="col-md-offset-2 col-md-6">
		<div class="panel panel-green">
			<div class="panel-heading">Create User Form</div>
			<div class="panel-body">
				<?php if($this->session->flashdata('message') != ''){ ?>
					<div class="alert alert-success" style="padding: 10px;line-height: 0.9;">
						<?php echo $this->session->flashdata('message'); ?>						
					</div>
				<?php } ?>

				<?php if($this->session->flashdata('errors') != ''){ ?>
					<div class="alert alert-danger" style="padding: 10px;line-height: 0.9;">
						<?php echo $this->session->flashdata('errors'); ?>						
					</div>
				<?php } ?>
				<form class="form-horizontal" method="post" action="<?php echo site_url('user/postuser') ?>">
					<div class="form-group">
						<div class="col-md-3 control-label"><b>Username: </b></div>
						<div class="col-md-6">
							<input type="text" class="form-control input-sm" name="username" placeholder="Type username">
						</div>	
					</div>

					<div class="form-group">
						<div class="col-md-3 control-label"><b>Password: </b></div>
						<div class="col-md-6">
							<input type="password" class="form-control input-sm" name="password" placeholder="Type password">
						</div>	
					</div>

					<div class="form-group">
						<div class="col-md-3 control-label"><b>Confirm: </b></div>
						<div class="col-md-6">
							<input type="password" class="form-control input-sm" name="cpassword" placeholder="Type password">
						</div>	
					</div>

					<div class="form-group">
						<div class="col-md-offset-3 col-md-6"><button type="submit" class="btn btn-success btn-sm">Save</button></div>
					</div>	
				</form>	
			</div>	
		</div>	
	</div>	
<?php $this->load->view('main/footer'); ?>