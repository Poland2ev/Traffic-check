<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<style>
    .img-avatar{
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
    }
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Danh sách tài khoản</h3>
		<div class="card-tools">
			<a href="?page=user/manage_user" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Thêm tài khoản</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>#</th>
						<th>Ảnh</th>
						<th>Họ tên</th>
						<th>Tên đăng nhập</th>
						<th>Vai trò</th>
						<th>Số GPLX</th>
						<th>Số CCCD</th>
						<th>Biển số</th>
						<th>Thao tác</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT u.*, concat(u.firstname,' ',u.lastname) as name,
                        MAX(CASE WHEN dm.meta_field='license_id_no' THEN dm.meta_value END) as license_id_no,
                        MAX(CASE WHEN dm.meta_field='cccd_no' THEN dm.meta_value END) as cccd_no,
                        GROUP_CONCAT(DISTINCT CASE WHEN dm.meta_field='plate_no' AND dm.meta_value != '' THEN dm.meta_value END ORDER BY dm.meta_value SEPARATOR ', ') as plate_no
                        FROM users u
                        LEFT JOIN drivers_meta owner ON owner.meta_field='user_id' AND owner.meta_value = u.id
                        LEFT JOIN drivers_meta dm ON dm.driver_id = owner.driver_id
                        WHERE u.id != '1' AND u.id != '{$_settings->userdata('id')}'
                        GROUP BY u.id
                        ORDER BY concat(u.firstname,' ',u.lastname) asc ");
					while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class="text-center"><img src="<?php echo validate_image($row['avatar']) ?>" class="img-avatar img-thumbnail p-0 border-2" alt="user_avatar"></td>
							<td><?php echo ucwords($row['name']) ?></td>
							<td><p class="m-0 truncate-1"><?php echo $row['username'] ?></p></td>
							<td><?php echo ($row['type'] == 1) ? 'Admin' : 'User' ?></td>
							<td><?php echo $row['license_id_no'] ?: 'N/A' ?></td>
							<td><?php echo $row['cccd_no'] ?: 'N/A' ?></td>
							<td><?php echo $row['plate_no'] ?: 'N/A' ?></td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Thao tác
				                    <span class="sr-only">Mở menu</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=user/manage_user&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Sửa</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Xóa</a>
				                  </div>
							</td>
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
			_conf("Bạn có chắc muốn xóa vĩnh viễn tài khoản này?","delete_user",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})
	function delete_user($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Users.php?f=delete",
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
