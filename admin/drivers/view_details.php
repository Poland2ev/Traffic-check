<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $is_admin = $_settings->userdata('type') == 1;
    $access_filter = $is_admin ? "" : " and owner.meta_value = '{$_settings->userdata('id')}' ";
    $qry = $conn->query("SELECT d.* from `drivers_list` d left join drivers_meta owner on d.id = owner.driver_id and owner.meta_field = 'user_id' where d.id = '{$_GET['id']}' {$access_filter} ");
    $qry2 = $conn->query("SELECT * from `drivers_meta` where driver_id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
        echo '<div class="alert alert-danger m-3">Bạn không có quyền xem phương tiện này.</div>';
        exit;
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
    p,label{margin-bottom:5px;}
    #uni_modal .modal-footer{display:none !important;}
</style>
<div class="container-fluid">
    <div class="w-100 d-flex justify-content-end mb-2">
        <button class="btn btn-flat btn-sm btn-default bg-lightblue" type="button" id="print"><i class="fa fa-print"></i> In</button>
        <button class="btn btn-flat btn-sm btn-default bg-black" data-dismiss="modal"><i class="fa fa-times"></i> Đóng</button>
    </div>
    <div class="border border-dark px-2 py-2" id="print_out">
        <div class="row">
            <div class="col-md-9">
                <h4><b>Thông tin người vi phạm</b></h4>
                <p><b>Họ tên:</b> <?php echo $name ?></p>
                <p><b>Số GPLX:</b> <?php echo $license_id_no ?></p>
                <p><b>Số CCCD:</b> <?php echo $cccd_no ?? 'Chưa cập nhật' ?></p>
                <p><b>Số điện thoại:</b> <?php echo $contact ?? 'Chưa cập nhật' ?></p>
                <p><b>Địa chỉ:</b> <?php echo $present_address ?? 'Chưa cập nhật' ?></p>
                <hr>
                <h4><b>Thông tin phương tiện</b></h4>
                <p><b>Biển số:</b> <?php echo $plate_no ?? 'Chưa cập nhật' ?></p>
                <p><b>Loại phương tiện:</b> <?php echo $vehicle_type ?? 'Chưa cập nhật' ?></p>
                <p><b>Nhãn hiệu:</b> <?php echo $vehicle_brand ?? 'Chưa cập nhật' ?></p>
                <p><b>Màu xe:</b> <?php echo $vehicle_color ?? 'Chưa cập nhật' ?></p>
            </div>
            <div class="col-md-3 text-center">
                <img src="<?php echo validate_image($image_path ?? '') ?>" alt="Ảnh hồ sơ" class="img-thumbnail" id="cimg">
            </div>
        </div>
        <hr class='bg-dark border-dark'>
        <h4 class="text-center"><b>Lịch sử vi phạm và thanh toán</b></h4>
        <table class='table table-stripped px-4'>
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Số biên bản</th>
                    <th>Địa điểm</th>
                    <th>Tổng phạt</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $olist = $conn->query("SELECT * FROM `offense_list` where driver_id = '{$driver_id}' order by unix_timestamp(date_created) desc");
                while($row = $olist->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo date("d/m/Y H:i",strtotime($row['date_created'])) ?></td>
                    <td><?php echo $row['ticket_no'] ?></td>
                    <td><?php echo $row['location'] ?: 'Chưa cập nhật' ?></td>
                    <td class="text-right"><?php echo format_currency_vnd($row['total_amount']) ?></td>
                    <td><?php echo ($row['status'] == 1) ? "Đã nộp" : 'Chưa nộp' ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if($olist->num_rows <= 0): ?>
                <tr>
                    <th class="text-center" colspan="5">Không có dữ liệu.</th>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#print').click(function(){
            start_loader()
            var _h = $('head').clone()
            var _p = $('#print_out').clone();
            var _el = $('<div>')
            _el.append(_h)
            _el.append('<style>html, body, .wrapper {min-height: unset !important;}</style>')
            _p.prepend('<div class="d-flex mb-3 w-100 align-items-center justify-content-center">'+
            '<img class="mx-4" src="<?php echo validate_image($_settings->info('logo')) ?>" width="50px" height="50px"/>'+
            '<div class="px-2">'+
            '<h3 class="text-center"><?php echo $_settings->info('name') ?></h3>'+
            '<h3 class="text-center">Thông tin người vi phạm và phương tiện</h3>'+
            '</div>'+
            '</div><hr/>');
            _el.append(_p)
            var nw = window.open("","_blank","width=1200,height=1200")
                nw.document.write(_el.html())
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        nw.close()
                        end_loader()
                    }, 300);
                }, 500);
        })
    })
</script>
