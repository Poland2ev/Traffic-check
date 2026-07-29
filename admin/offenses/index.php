<?php
$is_admin = $_settings->userdata('type') == 1;
$user_id = $_settings->userdata('id');
$where_user = $is_admin ? "" : " where owner.meta_value = '{$user_id}' ";
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title"><?php echo $is_admin ? 'Quản lý dữ liệu vi phạm' : 'Biên bản vi phạm của tôi' ?></h3>
		<?php if($is_admin): ?>
		<div class="card-tools">
			<a href="?page=offenses/manage_record" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Lập biên bản mới</a>
		</div>
		<?php endif; ?>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-stripped">
				<thead>
					<tr>
						<th>#</th>
						<th>Thời gian</th>
						<th>Số biên bản</th>
						<th>Biển số</th>
						<th>Số GPLX</th>
						<th>Địa điểm</th>
						<th>Hạn nộp</th>
						<th>Trạng thái</th>
						<th>Bằng chứng</th>
						<?php if($is_admin): ?><th>Thao tác</th><?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT r.*, d.license_id_no,
						MAX(CASE WHEN m.meta_field='plate_no' THEN m.meta_value END) as plate_no
						FROM `offense_list` r
						inner join `drivers_list` d on r.driver_id = d.id
						left join drivers_meta m on d.id = m.driver_id
						left join drivers_meta owner on d.id = owner.driver_id and owner.meta_field = 'user_id'
						{$where_user}
						group by r.id
						order by unix_timestamp(r.date_created) desc ");
					while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("d/m/Y H:i",strtotime($row['date_created'])) ?></td>
							<td><a href="javascript:void(0)" class="view_details" data-id="<?php echo $row['id'] ?>"><?php echo $row['ticket_no'] ?></a></td>
							<td><?php echo $row['plate_no'] ?: 'N/A' ?></td>
							<td><?php echo $row['license_id_no'] ?></td>
							<td><?php echo $row['location'] ?: 'N/A' ?></td>
							<td><?php echo !empty($row['due_date']) ? date("d/m/Y", strtotime($row['due_date'])) : 'N/A' ?></td>
							<td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success">Đã nộp</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Chưa nộp</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                            	<?php if(!empty($row['evidence_path'])): ?>
                            		<a href="<?php echo validate_image($row['evidence_path']) ?>" target="_blank" class="badge badge-info">Xem</a>
                            	<?php else: ?>
                            		<span class="badge badge-secondary">Chưa có</span>
                            	<?php endif; ?>
                            </td>
							<?php if($is_admin): ?>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Thao tác
				                    <span class="sr-only">Mở menu</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=offenses/manage_record&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Sửa</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Xóa</a>
				                  </div>
							</td>
							<?php endif; ?>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Bạn có chắc muốn xóa vĩnh viễn biên bản vi phạm này?","delete_offense",[$(this).attr('data-id')])
		})
		$('.view_details').click(function(){
			uni_modal("<i class='fa fa-ticket'></i> Chi tiết biên bản vi phạm","offenses/view_details.php?id="+$(this).attr('data-id'),'mid-large')
		})
		$('.table').dataTable({
			columnDefs:[{ orderable: false, targets: [8<?php echo $is_admin ? ',9' : '' ?>] }]
		});
	})
	function delete_offense($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_offense_record",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("Đã xảy ra lỗi.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("Đã xảy ra lỗi.",'error');
					end_loader();
				}
			}
		})
	}
</script>
