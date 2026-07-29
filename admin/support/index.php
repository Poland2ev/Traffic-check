<div class="row">
	<div class="col-lg-8">
		<div class="card card-outline card-primary">
			<div class="card-header">
				<h3 class="card-title">Liên hệ hỗ trợ</h3>
			</div>
			<div class="card-body">
				<div class="alert alert-info">
					Bạn có thể gửi phản hồi nếu biên bản, bằng chứng hoặc thông tin phương tiện chưa chính xác.
				</div>
				<form id="support-form">
					<div class="form-group">
						<label>Họ tên</label>
						<input type="text" class="form-control" value="<?php echo ucwords($_settings->userdata('firstname').' '.$_settings->userdata('lastname')) ?>" readonly>
					</div>
					<div class="form-group">
						<label>Số biên bản hoặc biển số liên quan</label>
						<input type="text" class="form-control" placeholder="VD: 123456789 hoặc 30A-123.45">
					</div>
					<div class="form-group">
						<label>Nhóm hỗ trợ</label>
						<select class="custom-select">
							<option>Khiếu nại thông tin vi phạm</option>
							<option>Hỗ trợ thanh toán</option>
							<option>Yêu cầu cập nhật thông tin phương tiện</option>
							<option>Vấn đề khác</option>
						</select>
					</div>
					<div class="form-group">
						<label>Nội dung</label>
						<textarea class="form-control" rows="6" placeholder="Nhập nội dung cần hỗ trợ"></textarea>
					</div>
					<button type="button" class="btn btn-primary" onclick="alert_toast('Yêu cầu hỗ trợ đã được ghi nhận ở giao diện mẫu.','success')">
						<i class="fa fa-paper-plane"></i> Gửi yêu cầu
					</button>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card card-outline card-secondary">
			<div class="card-header">
				<h3 class="card-title">Thông tin hỗ trợ</h3>
			</div>
			<div class="card-body">
				<p><b>Thời gian tiếp nhận:</b><br>08:00 - 17:00, thứ Hai đến thứ Sáu</p>
				<p><b>Email:</b><br>hotro@vpgt.local</p>
				<p><b>Điện thoại:</b><br>1900 0000</p>
				<hr>
				<p class="text-muted mb-0">
					Phần này hiện là giao diện hỗ trợ nội bộ. Nếu cần gửi email/tạo ticket thật, cần tích hợp thêm SMTP hoặc hệ thống ticket.
				</p>
			</div>
		</div>
	</div>
</div>
