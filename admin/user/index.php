<?php
$meta = [];
$profile = [];
$vehicles = [];
$uid = (int)$_settings->userdata('id');
$user = $conn->query("SELECT * FROM users WHERE id ='{$uid}'");
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
$driver = $conn->query("SELECT d.* FROM drivers_list d INNER JOIN drivers_meta m ON d.id = m.driver_id AND m.meta_field = 'user_id' WHERE m.meta_value = '{$uid}' ORDER BY d.id ASC");
if($driver){
	while($driver_row = $driver->fetch_assoc()){
		$vehicle = [
			'driver_id' => $driver_row['id'],
			'license_id_no' => $driver_row['license_id_no'],
			'name' => $driver_row['name']
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
?>
<div class="row">
	<div class="col-lg-6">
		<div class="card card-outline card-primary">
			<div class="card-header">
				<h3 class="card-title">Thông tin cá nhân</h3>
			</div>
			<div class="card-body">
				<p><b>Họ tên:</b> <?php echo ucwords(($meta['firstname'] ?? '').' '.($meta['lastname'] ?? '')) ?></p>
				<p><b>Tên đăng nhập:</b> <?php echo $meta['username'] ?? '' ?></p>
				<p><b>Số GPLX:</b> <?php echo $profile['license_id_no'] ?? 'Chưa cập nhật' ?></p>
				<p><b>Số CCCD:</b> <?php echo $profile['cccd_no'] ?? 'Chưa cập nhật' ?></p>
				<p><b>Số điện thoại:</b> <?php echo $profile['contact'] ?? 'Chưa cập nhật' ?></p>
				<p><b>Địa chỉ:</b> <?php echo $profile['present_address'] ?? 'Chưa cập nhật' ?></p>
			</div>
		</div>
		<div class="card card-outline card-warning">
			<div class="card-header">
				<h3 class="card-title">Đổi mật khẩu</h3>
			</div>
			<div class="card-body">
				<div id="password-msg"></div>
				<form id="change-password">
					<div class="form-group">
						<label>Mật khẩu hiện tại</label>
						<input type="password" name="current_password" class="form-control" required autocomplete="current-password">
					</div>
					<div class="form-group">
						<label>Mật khẩu mới</label>
						<input type="password" name="new_password" class="form-control" required minlength="6" autocomplete="new-password">
					</div>
					<div class="form-group">
						<label>Nhập lại mật khẩu mới</label>
						<input type="password" name="confirm_password" class="form-control" required minlength="6" autocomplete="new-password">
					</div>
					<button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="card card-outline card-info">
			<div class="card-header">
				<h3 class="card-title">Thông tin phương tiện sở hữu</h3>
			</div>
			<div class="card-body p-0">
				<?php if(count($vehicles) > 0): ?>
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
						<thead>
							<tr>
								<th>#</th>
								<th>Biển số</th>
								<th>Loại phương tiện</th>
								<th>Nhãn hiệu</th>
								<th>Màu xe</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($vehicles as $idx => $vehicle): ?>
							<tr>
								<td class="text-center"><?php echo $idx + 1 ?></td>
								<td><?php echo $vehicle['plate_no'] ?? 'Chưa cập nhật' ?></td>
								<td><?php echo $vehicle['vehicle_type'] ?? 'Chưa cập nhật' ?></td>
								<td><?php echo $vehicle['vehicle_brand'] ?? 'Chưa cập nhật' ?></td>
								<td><?php echo $vehicle['vehicle_color'] ?? 'Chưa cập nhật' ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php else: ?>
				<div class="p-3 text-muted">Chưa có phương tiện nào được liên kết với tài khoản.</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="alert alert-secondary">
			Thông tin cá nhân và phương tiện do quản trị viên cập nhật. Nếu có sai sót, vui lòng dùng mục <b>Liên hệ hỗ trợ</b>.
		</div>
	</div>
</div>
<script>
$('#change-password').submit(function(e){
	e.preventDefault();
	start_loader();
	$('#password-msg').html('');
	$.ajax({
		url:_base_url_+'classes/Users.php?f=change_password',
		method:'POST',
		data:$(this).serialize(),
		dataType:'json',
		error:function(){
			$('#password-msg').html('<div class="alert alert-danger">Đã xảy ra lỗi khi đổi mật khẩu.</div>');
			end_loader();
		},
		success:function(resp){
			if(resp.status == 'success'){
				$('#password-msg').html('<div class="alert alert-success">'+resp.msg+'</div>');
				$('#change-password')[0].reset();
			}else{
				$('#password-msg').html('<div class="alert alert-danger">'+resp.msg+'</div>');
			}
			end_loader();
		}
	});
});
</script>
