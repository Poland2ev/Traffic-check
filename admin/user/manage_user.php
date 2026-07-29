<?php
$meta = [];
$profile = [];
$vehicles = [];
if(isset($_GET['id']) && $_GET['id'] > 0){
    $user = $conn->query("SELECT * FROM users WHERE id ='{$_GET['id']}'");
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
    }
    $driver = $conn->query("SELECT d.* FROM drivers_list d INNER JOIN drivers_meta m ON d.id = m.driver_id AND m.meta_field = 'user_id' WHERE m.meta_value = '{$_GET['id']}' ORDER BY d.id ASC");
    if($driver){
        while($driver_row = $driver->fetch_assoc()){
            $vehicle = [
                'driver_id' => $driver_row['id'],
                'license_id_no' => $driver_row['license_id_no']
            ];
            $driver_meta = $conn->query("SELECT * FROM drivers_meta WHERE driver_id = '{$driver_row['id']}'");
            while($row = $driver_meta->fetch_assoc()){
                $vehicle[$row['meta_field']] = $row['meta_value'];
            }
            if(empty($profile))
                $profile = $vehicle;
            $vehicles[] = $vehicle;
        }
    }
}
if(empty($vehicles)){
    $vehicles[] = [
        'driver_id' => '',
        'plate_no' => '',
        'vehicle_type' => 'Ô tô',
        'vehicle_brand' => '',
        'vehicle_color' => ''
    ];
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($meta['id']) ? 'Sửa tài khoản' : 'Thêm tài khoản' ?></h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div id="msg"></div>
			<form action="" id="manage-user" enctype="multipart/form-data">
				<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
				<div class="row">
					<div class="col-md-6">
						<h5 class="border-bottom pb-2">Thông tin tài khoản</h5>
						<div class="form-group">
							<label for="lastname">Họ</label>
							<input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required pattern="[^0-9]*" title="Họ không được chứa số">
						</div>
						<div class="form-group">
							<label for="firstname">Tên</label>
							<input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required pattern="[^0-9]*" title="Tên không được chứa số">
						</div>
						<div class="form-group">
							<label for="username">Tên đăng nhập</label>
							<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required autocomplete="off">
						</div>
						<div class="form-group">
							<label for="password">Mật khẩu</label>
							<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off" <?php echo isset($meta['id']) ? "": 'required' ?>>
							<?php if(isset($_GET['id'])): ?>
							<small><i>Để trống nếu không muốn đổi mật khẩu.</i></small>
							<?php endif; ?>
						</div>
						<div class="form-group">
							<label for="type">Vai trò</label>
							<select name="type" id="type" class="custom-select">
								<option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected' : '' ?>>Admin</option>
								<option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected' : '' ?>>User</option>
							</select>
						</div>
						<div class="form-group">
							<label class="control-label">Ảnh đại diện</label>
							<div class="custom-file">
								<input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
								<label class="custom-file-label" for="customFile">Chọn tệp</label>
							</div>
						</div>
						<div class="form-group d-flex justify-content-center">
							<img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
						</div>
					</div>
					<div class="col-md-6">
						<h5 class="border-bottom pb-2">Thông tin cá nhân</h5>
						<div class="form-group">
							<label>Số GPLX</label>
							<input type="text" name="license_id_no" class="form-control" value="<?php echo $profile['license_id_no'] ?? '' ?>">
						</div>
						<div class="form-group">
							<label>Số CCCD</label>
							<input type="text" name="cccd_no" class="form-control" value="<?php echo $profile['cccd_no'] ?? '' ?>" pattern="[0-9]*" inputmode="numeric" title="Số CCCD chỉ được nhập chữ số">
						</div>
						<div class="form-group">
							<label>Số điện thoại</label>
							<input type="text" name="contact" class="form-control" value="<?php echo $profile['contact'] ?? '' ?>" placeholder="09123456789" pattern="[0-9]{10}" inputmode="numeric" maxlength="10" required title="Số điện thoại phải gồm đúng 10 chữ số">
						</div>
						<div class="form-group">
							<label>Địa chỉ</label>
							<textarea name="address" class="form-control" rows="2"><?php echo $profile['present_address'] ?? '' ?></textarea>
						</div>
						<div class="d-flex align-items-center justify-content-between border-bottom pb-2 mt-4 mb-3">
							<h5 class="m-0">Thông tin phương tiện sở hữu</h5>
							<button type="button" class="btn btn-sm btn-success" id="add-vehicle"><i class="fas fa-plus"></i> Thêm xe</button>
						</div>
						<div id="vehicle-list">
							<?php foreach($vehicles as $idx => $vehicle): ?>
							<div class="vehicle-row border rounded p-3 mb-3">
								<input type="hidden" name="vehicle_id[]" value="<?php echo $vehicle['driver_id'] ?? '' ?>">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<strong>Phương tiện <?php echo $idx + 1 ?></strong>
									<button type="button" class="btn btn-sm btn-outline-danger remove-vehicle"><i class="fas fa-trash"></i></button>
								</div>
								<div class="form-group">
									<label>Biển số</label>
									<input type="text" name="plate_no[]" class="form-control text-uppercase" value="<?php echo $vehicle['plate_no'] ?? '' ?>">
								</div>
								<div class="form-group">
									<label>Loại phương tiện</label>
									<select name="vehicle_type[]" class="custom-select">
										<?php $vehicle_type = $vehicle['vehicle_type'] ?? 'Ô tô'; ?>
										<option <?php echo $vehicle_type == 'Ô tô' ? 'selected' : '' ?>>Ô tô</option>
										<option <?php echo $vehicle_type == 'Xe máy' ? 'selected' : '' ?>>Xe máy</option>
										<option <?php echo $vehicle_type == 'Xe tải' ? 'selected' : '' ?>>Xe tải</option>
										<option <?php echo $vehicle_type == 'Xe khách' ? 'selected' : '' ?>>Xe khách</option>
									</select>
								</div>
								<div class="form-group">
									<label>Nhãn hiệu</label>
									<input type="text" name="vehicle_brand[]" class="form-control" value="<?php echo $vehicle['vehicle_brand'] ?? '' ?>">
								</div>
								<div class="form-group mb-0">
									<label>Màu xe</label>
									<input type="text" name="vehicle_color[]" class="form-control" value="<?php echo $vehicle['vehicle_color'] ?? '' ?>">
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer">
		<button class="btn btn-sm btn-primary mr-2" form="manage-user">Lưu</button>
		<a class="btn btn-sm btn-secondary" href="./?page=user/list">Hủy</a>
	</div>
</div>
<template id="vehicle-template">
	<div class="vehicle-row border rounded p-3 mb-3">
		<input type="hidden" name="vehicle_id[]" value="">
		<div class="d-flex justify-content-between align-items-center mb-2">
			<strong>Phương tiện</strong>
			<button type="button" class="btn btn-sm btn-outline-danger remove-vehicle"><i class="fas fa-trash"></i></button>
		</div>
		<div class="form-group">
			<label>Biển số</label>
			<input type="text" name="plate_no[]" class="form-control text-uppercase" value="">
		</div>
		<div class="form-group">
			<label>Loại phương tiện</label>
			<select name="vehicle_type[]" class="custom-select">
				<option>Ô tô</option>
				<option>Xe máy</option>
				<option>Xe tải</option>
				<option>Xe khách</option>
			</select>
		</div>
		<div class="form-group">
			<label>Nhãn hiệu</label>
			<input type="text" name="vehicle_brand[]" class="form-control" value="">
		</div>
		<div class="form-group mb-0">
			<label>Màu xe</label>
			<input type="text" name="vehicle_color[]" class="form-control" value="">
		</div>
	</div>
</template>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        }
	        reader.readAsDataURL(input.files[0]);
	    }
	}
	function refreshVehicleLabels(){
		$('#vehicle-list .vehicle-row').each(function(index){
			$(this).find('strong').text('Phương tiện ' + (index + 1));
			$(this).find('.remove-vehicle').prop('disabled', $('#vehicle-list .vehicle-row').length <= 1);
		});
	}
	function showFormError(message){
		$('#msg').html('<div class="alert alert-danger">'+message+'</div>');
		$('html, body').animate({scrollTop: $('#msg').offset().top - 80}, 250);
	}
	function validateUserForm(){
		const lastname = $.trim($('#lastname').val());
		const firstname = $.trim($('#firstname').val());
		const cccd = $.trim($('[name="cccd_no"]').val());
		const contact = $.trim($('[name="contact"]').val());
		const type = $('#type').val();
		let completeVehicleCount = 0;

		if(/\d/.test(lastname))
			return 'Họ không được chứa số.';
		if(/\d/.test(firstname))
			return 'Tên không được chứa số.';
		if(cccd !== '' && !/^\d+$/.test(cccd))
			return 'Số CCCD chỉ được nhập chữ số.';
		if(!/^\d{10}$/.test(contact))
			return 'Số điện thoại chỉ được nhập số và phải đủ 10 chữ số.';

		let vehicleError = '';
		$('#vehicle-list .vehicle-row').each(function(){
			const plate = $.trim($(this).find('[name="plate_no[]"]').val());
			const vehicleType = $.trim($(this).find('[name="vehicle_type[]"]').val());
			const brand = $.trim($(this).find('[name="vehicle_brand[]"]').val());
			const color = $.trim($(this).find('[name="vehicle_color[]"]').val());
			const isComplete = plate !== '' && vehicleType !== '' && brand !== '' && color !== '';
			const hasAny = plate !== '' || brand !== '' || color !== '';
			if(isComplete)
				completeVehicleCount++;
			else if(hasAny && vehicleError === '')
				vehicleError = 'Mỗi phương tiện đã nhập cần có đủ biển số, loại phương tiện, nhãn hiệu và màu xe.';
		});
		if(vehicleError !== '')
			return vehicleError;
		if(type == '2' && completeVehicleCount < 1)
			return 'Tài khoản User bắt buộc phải có ít nhất 1 phương tiện được điền đầy đủ.';
		return '';
	}
	$('#add-vehicle').click(function(){
		$('#vehicle-list').append($('#vehicle-template').html());
		refreshVehicleLabels();
	});
	$(document).on('click', '.remove-vehicle', function(){
		if($('#vehicle-list .vehicle-row').length <= 1)
			return;
		$(this).closest('.vehicle-row').remove();
		refreshVehicleLabels();
	});
	refreshVehicleLabels();
	$('#manage-user').submit(function(e){
		e.preventDefault();
		$('#msg').html('');
		const validationMessage = validateUserForm();
		if(validationMessage !== ''){
			showFormError(validationMessage);
			return;
		}
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Users.php?f=save',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					location.href = './?page=user/list';
				}else if(typeof resp === 'string' && resp.indexOf('error:') === 0){
					showFormError(resp.replace('error:', ''));
                    end_loader()
				}else{
					$('#msg').html('<div class="alert alert-danger">Tên đăng nhập đã tồn tại hoặc dữ liệu chưa hợp lệ</div>')
                    end_loader()
				}
			}
		})
	})
</script>
