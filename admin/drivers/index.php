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
		<h3 class="card-title"><?php echo $is_admin ? 'Quản lý phương tiện và người vi phạm' : 'Danh sách xe của tôi' ?></h3>
		<?php if($is_admin): ?>
		<div class="card-tools">
			<a href="?page=drivers/manage_driver" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Thêm hồ sơ</a>
		</div>
		<?php endif; ?>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-stripped">
				<thead>
					<tr>
						<th>#</th>
						<th>Biển số</th>
						<th>Số GPLX</th>
						<th>Số CCCD</th>
						<th>Chủ xe</th>
						<th>Phương tiện</th>
						<?php if($is_admin): ?><th>Thao tác</th><?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT d.*,
						MAX(CASE WHEN m.meta_field='plate_no' THEN m.meta_value END) as plate_no,
						MAX(CASE WHEN m.meta_field='cccd_no' THEN m.meta_value END) as cccd_no,
						MAX(CASE WHEN m.meta_field='vehicle_type' THEN m.meta_value END) as vehicle_type,
						MAX(CASE WHEN m.meta_field='vehicle_brand' THEN m.meta_value END) as vehicle_brand
						FROM `drivers_list` d
						LEFT JOIN drivers_meta m ON d.id = m.driver_id
						LEFT JOIN drivers_meta owner ON d.id = owner.driver_id AND owner.meta_field = 'user_id'
						{$where_user}
						GROUP BY d.id
						ORDER BY unix_timestamp(d.date_created) desc ");
					while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['plate_no'] ?: 'N/A' ?></td>
							<td><?php echo $row['license_id_no'] ?></td>
							<td><?php echo $row['cccd_no'] ?: 'N/A' ?></td>
							<td><span class="mr-2"><a href="javascript:void(0)" class="view_details badge badge-dark text-light" data-id="<?php echo $row['id'] ?>"> <i class="fa fa-eye"></i></a></span> <?php echo $row['name'] ?></td>
							<td><?php echo trim(($row['vehicle_type'] ?: '').' '.($row['vehicle_brand'] ?: '')) ?: 'N/A' ?></td>
							<?php if($is_admin): ?>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Thao tác
				                    <span class="sr-only">Mở menu</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=drivers/manage_driver&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Sửa</a>
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
			_conf("Bạn có chắc muốn xóa vĩnh viễn hồ sơ này?","delete_driver",[$(this).attr('data-id')])
		})
		$('.view_details').click(function(){
			uni_modal("<i class='fa fa-id-card'></i> Thông tin phương tiện","drivers/view_details.php?id="+$(this).attr('data-id'),'large')
		})
		$('.table').dataTable({
			columnDefs: [
				{ orderable: false, targets: [<?php echo $is_admin ? '6' : '4' ?>] }
			]
		});
	})
	function delete_driver($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_driver",
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
