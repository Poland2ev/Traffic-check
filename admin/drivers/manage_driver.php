<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `drivers_list` where id = '{$_GET['id']}' ");
    $qry2 = $conn->query("SELECT * from `drivers_meta` where driver_id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
	if($qry2->num_rows > 0){
        while($row = $qry2->fetch_assoc()){
            ${$row['meta_field']}=$row['meta_value'];
        }
    }
}
?>
<style>
	img#cimg{
		height: 25vh;
		width: 15vw;
		object-fit: scale-down;
		object-position: center center;
	}
</style>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Cập nhật ": "Thêm mới " ?> hồ sơ người vi phạm / phương tiện</h3>
	</div>
	<div class="card-body">
		<form action="" id="driver-form">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="row">
				<div class="col-6">
					<h5 class="border-bottom pb-2">Thông tin cá nhân</h5>
					<div class="form-group">
						<label for="license_id_no" class="control-label">Số GPLX</label>
						<input type="text" maxlength="50" class="form-control form" required name="license_id_no" value="<?php echo isset($license_id_no) ? $license_id_no : '' ?>">
					</div>
					<div class="form-group">
						<label for="cccd_no" class="control-label">Số CCCD</label>
						<input type="text" maxlength="20" class="form-control form" name="cccd_no" value="<?php echo isset($cccd_no) ? $cccd_no : '' ?>">
					</div>
					<div class="form-group">
						<label for="lastname" class="control-label">Họ</label>
						<input type="text" class="form-control form" required name="lastname" value="<?php echo isset($lastname) ? $lastname : '' ?>">
					</div>
					<div class="form-group">
						<label for="firstname" class="control-label">Tên</label>
						<input type="text" class="form-control form" required name="firstname" value="<?php echo isset($firstname) ? $firstname : '' ?>">
					</div>
					<div class="form-group">
						<label for="middlename" class="control-label">Tên đệm</label>
						<input type="text" class="form-control form" name="middlename" value="<?php echo isset($middlename) ? $middlename : '' ?>">
					</div>
					<div class="form-group">
						<label for="dob" class="control-label">Ngày sinh</label>
						<input type="date" class="form-control form" required name="dob" value="<?php echo isset($dob) ? date("Y-m-d",strtotime($dob)) : '' ?>">
					</div>
					<div class="form-group">
						<label for="contact" class="control-label">Số điện thoại</label>
						<input type="text" maxlength="13" class="form-control form" required name="contact" value="<?php echo isset($contact) ? $contact : '' ?>">
					</div>
					<div class="form-group">
						<label for="present_address" class="control-label">Địa chỉ liên hệ</label>
						<textarea rows="3" class="form-control" style="resize:none" required name="present_address"><?php echo isset($present_address) ? $present_address : '' ?></textarea>
					</div>
					<div class="form-group">
						<label for="permanent_address" class="control-label">Địa chỉ thường trú</label>
						<textarea rows="3" class="form-control" style="resize:none" required name="permanent_address"><?php echo isset($permanent_address) ? $permanent_address : '' ?></textarea>
					</div>
				</div>
				<div class="col-6">
					<h5 class="border-bottom pb-2">Thông tin phương tiện</h5>
					<div class="form-group">
						<label for="plate_no" class="control-label">Biển số xe</label>
						<input type="text" maxlength="20" class="form-control form text-uppercase" name="plate_no" value="<?php echo isset($plate_no) ? $plate_no : '' ?>">
					</div>
					<div class="form-group">
						<label for="vehicle_type" class="control-label">Loại phương tiện</label>
						<select name="vehicle_type" id="vehicle_type" class="custom-select select2">
							<option <?php echo (isset($vehicle_type) && $vehicle_type == 'Ô tô') ? 'selected' : '' ?>>Ô tô</option>
							<option <?php echo (isset($vehicle_type) && $vehicle_type == 'Xe máy') ? 'selected' : '' ?>>Xe máy</option>
							<option <?php echo (isset($vehicle_type) && $vehicle_type == 'Xe tải') ? 'selected' : '' ?>>Xe tải</option>
							<option <?php echo (isset($vehicle_type) && $vehicle_type == 'Xe khách') ? 'selected' : '' ?>>Xe khách</option>
						</select>
					</div>
					<div class="form-group">
						<label for="vehicle_brand" class="control-label">Nhãn hiệu / dòng xe</label>
						<input type="text" class="form-control form" name="vehicle_brand" value="<?php echo isset($vehicle_brand) ? $vehicle_brand : '' ?>">
					</div>
					<div class="form-group">
						<label for="vehicle_color" class="control-label">Màu xe</label>
						<input type="text" class="form-control form" name="vehicle_color" value="<?php echo isset($vehicle_color) ? $vehicle_color : '' ?>">
					</div>
					<div class="form-group">
						<label for="license_type" class="control-label">Hạng GPLX</label>
						<select name="license_type" id="license_type" class="custom-select select2">
							<option <?php echo (isset($license_type) && $license_type == 'A1') ? 'selected' : '' ?>>A1</option>
							<option <?php echo (isset($license_type) && $license_type == 'A2') ? 'selected' : '' ?>>A2</option>
							<option <?php echo (isset($license_type) && $license_type == 'B1') ? 'selected' : '' ?>>B1</option>
							<option <?php echo (isset($license_type) && $license_type == 'B2') ? 'selected' : '' ?>>B2</option>
							<option <?php echo (isset($license_type) && $license_type == 'C') ? 'selected' : '' ?>>C</option>
							<option <?php echo (isset($license_type) && $license_type == 'D') ? 'selected' : '' ?>>D</option>
						</select>
					</div>
					<div class="form-group">
						<label for="nationality" class="control-label">Quốc tịch</label>
						<input type="text" class="form-control form" required name="nationality" value="<?php echo isset($nationality) ? $nationality : 'Việt Nam' ?>">
					</div>
					<div class="form-group">
						<label for="" class="control-label">Ảnh hồ sơ</label>
						<div class="custom-file">
						<input type="hidden" name="image_path" value="<?php echo isset($image_path) ? $image_path : '' ?>">
						<input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
						<label class="custom-file-label" for="customFile">Chọn tệp</label>
						</div>
					</div>
					<div class="form-group d-flex justify-content-center">
						<img align="center" src="<?php echo validate_image(isset($image_path) ? $image_path : '') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="driver-form">Lưu</button>
		<a class="btn btn-flat btn-default" href="?page=drivers">Hủy</a>
	</div>
</div>
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
	$(document).ready(function(){
		$('#driver-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_driver",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("Đã xảy ra lỗi",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.href = "./?page=drivers";
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else{
						alert_toast("Đã xảy ra lỗi",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})
	})
</script>
