<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100%;
	}
	img#cimg2{
		height: 42vh;
		width: 100%;
		object-fit: cover;
		border-radius: 8px;
	}
</style>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title">Cài đặt hình ảnh hệ thống</h5>
		</div>
		<div class="card-body">
			<form action="" id="system-frm">
				<div id="msg" class="form-group"></div>
				<div class="form-group">
					<label for="logo_url" class="control-label">Link logo hệ thống</label>
					<input type="url" class="form-control form-control-sm" name="logo" id="logo_url" value="<?php echo $_settings->info('logo') ?>" placeholder="https://example.com/logo.png">
				</div>
				<div class="form-group d-flex justify-content-center">
					<img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="Logo hệ thống" id="cimg" class="img-fluid img-thumbnail">
				</div>
				<div class="form-group">
					<label for="cover_url" class="control-label">Link ảnh bìa cổng tra cứu</label>
					<input type="url" class="form-control form-control-sm" name="cover" id="cover_url" value="<?php echo $_settings->info('cover') ?>" placeholder="https://example.com/cover.jpg">
				</div>
				<div class="form-group d-flex justify-content-center">
					<img src="<?php echo validate_image($_settings->info('cover')) ?>" alt="Ảnh bìa cổng tra cứu" id="cimg2" class="img-fluid img-thumbnail">
				</div>
			</form>
		</div>
		<div class="card-footer">
			<button class="btn btn-sm btn-primary" form="system-frm">Cập nhật</button>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#logo_url').on('input', function(){
			const value = $(this).val().trim();
			if(value)
				$('#cimg').attr('src', value);
		});
		$('#cover_url').on('input', function(){
			const value = $(this).val().trim();
			if(value)
				$('#cimg2').attr('src', value);
		});
	});
</script>
