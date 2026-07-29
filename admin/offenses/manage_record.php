<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `offense_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Cập nhật " : "Lập mới " ?> biên bản vi phạm</h3>
	</div>
	<div class="card-body">
		<form action="" id="offense-form">
			<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
			<input type="hidden" name="evidence_type" id="evidence_type" value="<?php echo isset($evidence_type) ? $evidence_type : '' ?>">
			<div class="row">
				<div class="col-6">
					<h5 class="border-bottom pb-2">Thông tin biên bản</h5>
					<div class="form-group">
						<label class="control-label" for="date_created">Thời gian vi phạm</label>
						<input type="datetime-local" class="form-control" name="date_created" id="date_created" value="<?php echo isset($date_created) ? date("Y-m-d\\TH:i",strtotime($date_created)) : date("Y-m-d\\TH:i") ?>" required>
					</div>
					<div class="form-group">
						<label class="control-label" for="ticket_no">Số biên bản</label>
						<input type="text" class="form-control" name="ticket_no" id="ticket_no" value="<?php echo isset($ticket_no) ? $ticket_no : '' ?>" required>
					</div>
					<div class="form-group">
						<label class="control-label" for="driver_id">Người vi phạm / phương tiện</label>
						<select name="driver_id" id="driver_id" class="custom-select select2" required>
							<option value=""></option>
							<?php
							$driver = $conn->query("SELECT d.*, MAX(CASE WHEN m.meta_field='plate_no' THEN m.meta_value END) as plate_no FROM `drivers_list` d LEFT JOIN drivers_meta m ON d.id = m.driver_id GROUP BY d.id order by d.`name` asc ");
							while($row = $driver->fetch_assoc()):
							?>
							<option value="<?php echo $row['id'] ?>" <?php echo (isset($driver_id) && $driver_id == $row['id']) ? 'selected' : '' ?>>[<?php echo $row['license_id_no'] ?><?php echo !empty($row['plate_no']) ? ' - '.$row['plate_no'] : '' ?>] <?php echo ucwords($row['name']) ?></option>
							<?php endwhile; ?>
						</select>
					</div>
					<div class="form-group">
						<label class="control-label" for="location">Địa điểm vi phạm</label>
						<textarea name="location" id="location" class="form-control" rows="2" required><?php echo isset($location) ? $location : '' ?></textarea>
					</div>
					<div class="form-group">
						<label class="control-label" for="due_date">Hạn nộp phạt</label>
						<input type="date" class="form-control" name="due_date" id="due_date" value="<?php echo isset($due_date) && !empty($due_date) ? date("Y-m-d",strtotime($due_date)) : date("Y-m-d", strtotime("+30 days")) ?>" required>
					</div>
				</div>
				<div class="col-6">
					<h5 class="border-bottom pb-2">Cán bộ, thanh toán và bằng chứng</h5>
					<div class="form-group">
						<label class="control-label" for="officer_id">Mã cán bộ</label>
						<input type="text" class="form-control" name="officer_id" id="officer_id" value="<?php echo isset($officer_id) ? $officer_id : '' ?>" required>
					</div>
					<div class="form-group">
						<label class="control-label" for="officer_name">Tên cán bộ</label>
						<input type="text" class="form-control" name="officer_name" id="officer_name" value="<?php echo isset($officer_name) ? $officer_name : '' ?>" required>
					</div>
					<div class="form-group">
						<label class="control-label" for="status">Trạng thái nộp phạt</label>
						<select name="status" id="status" class="custom-select" required>
							<option value="0" <?php echo (isset($status) && $status == '0') ? 'selected' : '' ?>>Chưa nộp</option>
							<option value="1" <?php echo (isset($status) && $status == '1') ? 'selected' : '' ?>>Đã nộp</option>
						</select>
					</div>
					<div class="form-group">
						<label class="control-label" for="payment_method">Phương thức thanh toán</label>
						<select name="payment_method" id="payment_method" class="custom-select">
							<option <?php echo (isset($payment_method) && $payment_method == 'Chuyển khoản ngân hàng') ? 'selected' : '' ?>>Chuyển khoản ngân hàng</option>
							<option <?php echo (isset($payment_method) && $payment_method == 'Ví điện tử') ? 'selected' : '' ?>>Ví điện tử</option>
							<option <?php echo (isset($payment_method) && $payment_method == 'Nộp trực tiếp') ? 'selected' : '' ?>>Nộp trực tiếp</option>
						</select>
					</div>
					<div class="form-group">
						<label class="control-label" for="payment_reference">Mã giao dịch / mã thanh toán</label>
						<input type="text" class="form-control" name="payment_reference" id="payment_reference" value="<?php echo isset($payment_reference) ? $payment_reference : '' ?>" placeholder="VD: VPGT-123456789">
					</div>
					<div class="form-group">
						<label class="control-label" for="evidence_path">Link bằng chứng ảnh/video</label>
						<input type="url" class="form-control" id="evidence_path" name="evidence_path" value="<?php echo isset($evidence_path) ? $evidence_path : '' ?>" placeholder="https://example.com/evidence.jpg hoặc https://example.com/video.mp4">
						<small class="text-muted">Dùng link trực tiếp của ảnh hoặc video. Hệ thống sẽ tự nhận diện loại bằng chứng.</small>
						<div class="mt-2 <?php echo empty($evidence_path) ? 'd-none' : '' ?>" id="evidence-preview-wrap">
							<a href="<?php echo !empty($evidence_path) ? validate_image($evidence_path) : '#' ?>" target="_blank" id="evidence-preview-link">Xem bằng chứng</a>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-6">
					<h5 class="border-bottom border-light"><b>Danh sách lỗi vi phạm</b></h5>
					<div class="row">
						<div class="col-8">
							<div class="form-group">
								<select id="offense_id" class="custom-select select2">
									<option value=""></option>
									<?php
									$offenses = $conn->query("SELECT * FROM `offenses` where status = 1 order by `name` asc ");
									while($row = $offenses->fetch_assoc()):
									?>
									<option value="<?php echo $row['id'] ?>" data-fine="<?php echo $row['fine'] ?>" data-code="<?php echo $row['code'] ?>" data-name="<?php echo $row['name'] ?>">[<?php echo $row['code'] ?>] <?php echo ucwords($row['name']) ?></option>
									<?php endwhile; ?>
								</select>
							</div>
						</div>
						<div class="col-4">
							<button class="btn btn-flat btn-default bg-lightblue" type="button" id="add_to_list"><i class="fa fa-plus"></i> Thêm lỗi</button>
						</div>
					</div>
					<table class="table table-stripped table-hover" id="fine-list">
						<thead>
							<tr>
								<th>Mã lỗi</th>
								<th>Lỗi vi phạm</th>
								<th>Mức phạt</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
							if(isset($id)):
							$olist = $conn->query("SELECT i.*,o.code,o.name FROM `offense_items` i inner join `offenses` o on i.offense_id = o.id where i.driver_offense_id ='{$id}' ");
							while($row = $olist->fetch_assoc()):
							?>
							<tr>
								<td><?php echo $row['code'] ?>
									<input type="hidden" name="offense_id[]" value="<?php echo $row['offense_id'] ?>">
									<input type="hidden" name="fine[]" value="<?php echo $row['fine'] ?>">
								</td>
								<td><?php echo $row['name'] ?></td>
								<td class="fine text-right"><?php echo format_currency_vnd($row['fine']) ?></td>
								<td><button class="btn btn-sm btn-default text-danger" type="button" onclick="rem_item($(this))"><i class="fa fa-times"></i></button></td>
							</tr>
							<?php endwhile; ?>
							<?php endif; ?>
							<?php if(!isset($id) || (isset($olist) && $olist->num_rows <= 0)): ?>
							<tr id="td-none">
								<th colspan="4" class="text-center">Chưa thêm lỗi vi phạm.</th>
							</tr>
							<?php endif; ?>
						</tbody>
						<tfoot>
							<tr>
								<th class="text-center">Tổng cộng</th>
								<th colspan="2" class="text-right" id="total_amount"><?php echo isset($total_amount) ? format_currency_vnd($total_amount) : '0 VN&#272;' ?></th>
								<th><input type="hidden" name="total_amount" value="<?php echo isset($total_amount) ? $total_amount : 0 ?>"></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="col-6">
					<div class="form-group">
						<label for="remarks" class="control-label">Ghi chú / hướng xử lý</label>
						<textarea name="remarks" id="remarks" class="form-control" cols="30" rows="8" style="resize:none !important"><?php echo isset($remarks) ? $remarks : '' ?></textarea>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="offense-form">Lưu</button>
		<a class="btn btn-flat btn-default" href="?page=offenses">Hủy</a>
	</div>
</div>
<script>
    function rem_item(_this){
        _this.closest('tr').remove()
        calculate_total();
    }
    function calculate_total(){
        var total = 0 ;
        $('#fine-list input[name="fine[]"]').each(function(){
            total += parseFloat($(this).val())
        })
        $('#total_amount').text(parseFloat(total).toLocaleString('vi-VN') + ' VN\u0110')
        $('input[name="total_amount"]').val(parseFloat(total))
    }
    function updateEvidenceType(){
        var url = ($('#evidence_path').val() || '').trim();
        var cleanUrl = url.split('?')[0].toLowerCase();
        var isVideo = /\.(mp4|webm|ogg|mov|m4v)$/.test(cleanUrl);
        $('#evidence_type').val(url ? (isVideo ? 'video' : 'image') : '');
        if(url){
            $('#evidence-preview-link').attr('href', url);
            $('#evidence-preview-wrap').removeClass('d-none');
        }else{
            $('#evidence-preview-wrap').addClass('d-none');
        }
    }
	$(document).ready(function(){
        $('.select2').select2({placeholder:"Vui lòng chọn",width:"relative"})
        $('#evidence_path').on('input change', updateEvidenceType);
        updateEvidenceType();
        $('#add_to_list').click(function(){
            var offense_id =  $('#offense_id').val()
            if(!offense_id)
            	return false;
            var fine =  $('#offense_id option[value="'+offense_id+'"]').attr('data-fine')
            var offense =  $('#offense_id option[value="'+offense_id+'"]').attr('data-name')
            var code =  $('#offense_id option[value="'+offense_id+'"]').attr('data-code')
            var tr = $("<tr>")
            tr.append('<td>'+code+'<input type="hidden" name="offense_id[]" value="'+offense_id+'"><input type="hidden" name="fine[]" value="'+fine+'"></td>');
            tr.append('<td>'+offense+'</td>');
            tr.append('<td class="text-right">'+(parseFloat(fine).toLocaleString('vi-VN'))+' VN\u0110</td>');
            tr.append('<td><button class="btn btn-sm btn-default text-danger" type="button" onclick="rem_item($(this))"><i class="fa fa-times"></i></button></td>');
            $('#fine-list tbody').append(tr)
            if($('#td-none').length > 0)
             $('#td-none').remove();
             calculate_total();
             $('#offense_id').val('').trigger('change')
        })
		$('#offense-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			$('.err-msg').remove();
			start_loader();
            if($('[name="offense_id[]"]').length <= 0){
                alert_toast('Vui lòng thêm ít nhất 1 lỗi vi phạm','warning')
                end_loader();
                return false;
            }
            updateEvidenceType();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_offense_record",
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
                        end_loader();
                        uni_modal("<i class='fa fa-ticket'></i> Chi tiết biên bản vi phạm","offenses/view_details.php?id="+resp.id,'mid-large')
                        $('#uni_modal').on('hide.bs.modal',function(e){
                            location.href="./?page=offenses";
                        })
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
