<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $is_admin = $_settings->userdata('type') == 1;
    $access_filter = $is_admin ? "" : " and owner.meta_value = '{$_settings->userdata('id')}' ";
    $qry = $conn->query("SELECT r.*, d.license_id_no, d.name as driver,
        MAX(CASE WHEN m.meta_field='plate_no' THEN m.meta_value END) as plate_no,
        MAX(CASE WHEN m.meta_field='cccd_no' THEN m.meta_value END) as cccd_no
        FROM `offense_list` r
        inner join `drivers_list` d on r.driver_id = d.id
        left join drivers_meta m on d.id = m.driver_id
        left join drivers_meta owner on d.id = owner.driver_id and owner.meta_field = 'user_id'
        where r.id = '{$_GET['id']}'
        {$access_filter}
        GROUP BY r.id ");
    $qry2 = $conn->query("SELECT i.*,o.code,o.name from `offense_items` i inner join `offenses` o on i.offense_id = o.id where i.driver_offense_id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
        echo '<div class="alert alert-danger m-3">Bạn không có quyền xem biên bản này.</div>';
        exit;
    }
    $offense_arr = array();
	if($qry2->num_rows > 0){
        while($row = $qry2->fetch_assoc()){
            $offense_arr[]=$row;
        }
    }
}
?>
<div class="container-fluid">
    <div class="w-100 d-flex justify-content-end mb-2">
        <button class="btn btn-flat btn-sm btn-default bg-lightblue" type="button" id="print"><i class="fa fa-print"></i> In</button>
        <button class="btn btn-flat btn-sm btn-default bg-black" data-dismiss="modal"><i class="fa fa-times"></i> Đóng</button>
    </div>
    <div class="border border-dark px-2 py-2" id="print_out">
        <style>
            #uni_modal .modal-footer{display:none !important;}
            p,label{margin-bottom:5px;}
            .evidence-preview{max-height:260px; max-width:100%;}
        </style>
        <div class="row">
            <div class="col-md-8">
                <h4 class="text-center"><b>Biên bản vi phạm giao thông</b></h4>
                <hr>
                <p><b>Số biên bản:</b> <?php echo $ticket_no ?></p>
                <p><b>Thời gian:</b> <?php echo date("d/m/Y H:i",strtotime($date_created)) ?></p>
                <p><b>Địa điểm:</b> <?php echo !empty($location) ? $location : 'Chưa cập nhật' ?></p>
                <p><b>Số GPLX:</b> <?php echo $license_id_no ?></p>
                <p><b>Biển số xe:</b> <?php echo !empty($plate_no) ? $plate_no : 'Chưa cập nhật' ?></p>
                <p><b>Số CCCD:</b> <?php echo !empty($cccd_no) ? $cccd_no : 'Chưa cập nhật' ?></p>
                <p><b>Người vi phạm:</b> <?php echo $driver ?></p>
                <p><b>Mã cán bộ:</b> <?php echo $officer_id ?></p>
                <p><b>Tên cán bộ:</b> <?php echo $officer_name ?></p>
                <p><b>Trạng thái:</b> <?php echo ($status == 1) ? "Đã nộp phạt" : "Chưa nộp phạt" ?></p>
                <p><b>Hạn nộp:</b> <?php echo !empty($due_date) ? date("d/m/Y", strtotime($due_date)) : 'Chưa cập nhật' ?></p>
                <p><b>Mã thanh toán:</b> <?php echo !empty($payment_reference) ? $payment_reference : 'VPGT-'.$ticket_no ?></p>
            </div>
            <div class="col-md-4 text-center">
                <h6><b>Bằng chứng</b></h6>
                <?php if(!empty($evidence_path)): ?>
                    <?php $evidence_url = validate_image($evidence_path); ?>
                    <?php if($evidence_type == 'video'): ?>
                        <video src="<?php echo $evidence_url ?>" controls class="evidence-preview border"></video>
                    <?php else: ?>
                        <a href="<?php echo $evidence_url ?>" target="_blank">
                            <img src="<?php echo $evidence_url ?>" class="img-thumbnail evidence-preview" alt="Bằng chứng">
                        </a>
                    <?php endif; ?>
                    <div class="mt-2">
                        <a href="<?php echo $evidence_url ?>" download>Tải bằng chứng</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary">Chưa có bằng chứng.</div>
                <?php endif; ?>
            </div>
        </div>
        <hr class='bg-dark border-dark'>
        <h5 class="text-center"><b>Danh sách lỗi vi phạm</b></h5>
        <table class='table table-stripped px-4'>
            <thead>
                <tr>
                    <th>Mã lỗi</th>
                    <th>Lỗi vi phạm</th>
                    <th class="text-right">Mức phạt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($offense_arr as $row): ?>
                <tr>
                    <td><?php echo $row['code'] ?></td>
                    <td><?php echo $row['name'] ?></td>
                    <td class='text-right'><?php echo format_currency_vnd($row['fine']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($offense_arr) <= 0): ?>
                <tr>
                    <th class="text-center" colspan="3">Không có dữ liệu.</th>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class='text-center' colspan="2">Tổng cộng</th>
                    <th class="text-right"><?php echo format_currency_vnd($total_amount) ?></th>
                </tr>
            </tfoot>
        </table>
        <hr class="bg-dark border-dark">
        <b>Ghi chú:</b>
        <p><?php echo $remarks ?></p>
    </div>
</div>
<script>
    $(function(){
        $('#print').click(function(){
            start_loader()
            var _h = $('head').clone()
            var _p = $('#print_out').clone();
            var _el = $('<div>')
            _p.prepend('<div class="d-flex mb-3 w-100 align-items-center justify-content-center">'+
            '<div class="px-2">'+
            '<h5 class="text-center"><?php echo $_settings->info('name') ?></h5>'+
            '</div>'+
            '</div><hr/>');
            _el.append(_h)
            _el.append('<style>html, body, .wrapper {min-height: unset !important;}#print_out{width:70% !important;margin:auto}</style>')
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
