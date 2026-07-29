<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Danh mục lỗi vi phạm</h3>
		<?php if($_settings->userdata('type') == 1): ?>
		<div class="card-tools">
			<a href="?page=maintenance/manage_offense" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Thêm lỗi vi phạm</a>
		</div>
		<?php endif; ?>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped table-hover">
			<?php if($_settings->userdata('type') == 1): ?>
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="25%">
					<col width="10%">
					<col width="10%">
					<col width="15%">
				</colgroup>
			<?php else: ?>
				<colgroup>
					<col width="10%">
					<col width="15%">
					<col width="25%">
					<col width="30%">
					<col width="10%">
					<col width="10%">
				</colgroup>
			<?php endif; ?>
				<thead>
					<tr>
						<th>#</th>
						<th>Ngày tạo</th>
						<th>Tên lỗi</th>
						<th>Mô tả</th>
						<th>Mức phạt</th>
						<th>Trạng thái</th>
						<?php if($_settings->userdata('type') == 1): ?>
						<th>Thao tác</th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `offenses` order by unix_timestamp(date_created) desc ");
						while($row = $qry->fetch_assoc()):
                            $row['description'] = strip_tags(stripslashes(html_entity_decode($row['description'])));
					?>
						<tr title="<?php echo $row['description'] ?>">
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo '['.$row['code'].'] - '.$row['name'] ?></td>
							<td ><p class="truncate m-0"><?php echo $row['description'] ?></p></td>
							<td class="text-right"><?php echo format_currency_vnd($row['fine']) ?></td>
							<td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success">Đang áp dụng</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Ngừng áp dụng</span>
                                <?php endif; ?>
                            </td>
							<?php if($_settings->userdata('type') == 1): ?>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Thao tác
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=maintenance/manage_offense&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Sửa</a>
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
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Bạn có chắc muốn xóa vĩnh viễn lỗi vi phạm này?","delete_offense",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})
	function delete_offense($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_offense",
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
