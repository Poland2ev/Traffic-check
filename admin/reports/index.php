<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : date("Y-m-d",strtotime(date('Y-m-d').' -3 days'));
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : date("Y-m-d");
$summary = $conn->query("SELECT COUNT(*) as total_cases,
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as paid_cases,
    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as unpaid_cases,
    SUM(CASE WHEN status = 1 THEN total_amount ELSE 0 END) as revenue
    FROM offense_list
    WHERE date(date_created) between '{$date_start}' and '{$date_end}'")->fetch_assoc();
$popular = $conn->query("SELECT o.code, o.name, COUNT(*) as total
    FROM offense_items i
    INNER JOIN offenses o ON i.offense_id = o.id
    WHERE date(i.date_created) between '{$date_start}' and '{$date_end}'
    GROUP BY i.offense_id
    ORDER BY total DESC
    LIMIT 5");
?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Báo cáo và thống kê vi phạm</h3>
	</div>
	<div class="card-body">
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label for="date_start" class="control-label">Từ ngày</label>
                    <input type="date" class="form-control" id="date_start" value="<?php echo date("Y-m-d",strtotime($date_start)) ?>">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="date_end" class="control-label">Đến ngày</label>
                    <input type="date" class="form-control" id="date_end" value="<?php echo date("Y-m-d",strtotime($date_end)) ?>">
                </div>
            </div>
            <div class="col-2 row align-items-end pb-1">
                <div class="w-100">
                    <div class="form-group d-flex justify-content-between align-middle">
                        <button class="btn btn-flat btn-default bg-lightblue" type="button" id="filter"><i class="fa fa-filter"></i> Lọc</button>
                        <button class="btn btn-flat btn-success" type="button" id="print"><i class="fa fa-print"></i> In/PDF</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid" id="print_out">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="info-box"><span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span><div class="info-box-content"><span class="info-box-text">Tổng vi phạm</span><span class="info-box-number"><?php echo number_format($summary['total_cases'] ?? 0) ?></span></div></div>
                </div>
                <div class="col-md-3">
                    <div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-check"></i></span><div class="info-box-content"><span class="info-box-text">Đã nộp phạt</span><span class="info-box-number"><?php echo number_format($summary['paid_cases'] ?? 0) ?></span></div></div>
                </div>
                <div class="col-md-3">
                    <div class="info-box"><span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span><div class="info-box-content"><span class="info-box-text">Chưa nộp phạt</span><span class="info-box-number"><?php echo number_format($summary['unpaid_cases'] ?? 0) ?></span></div></div>
                </div>
                <div class="col-md-3">
                    <div class="info-box"><span class="info-box-icon bg-primary"><i class="fas fa-coins"></i></span><div class="info-box-content"><span class="info-box-text">Doanh thu</span><span class="info-box-number"><?php echo format_currency_vnd($summary['revenue'] ?? 0) ?></span></div></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-hover table-stripped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Thời gian</th>
                                <th>Số biên bản</th>
                                <th>Biển số</th>
                                <th>Số GPLX</th>
                                <th>Địa điểm</th>
                                <th>Trạng thái</th>
                                <th>Tổng phạt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $qry = $conn->query("SELECT r.*, d.license_id_no, MAX(CASE WHEN m.meta_field='plate_no' THEN m.meta_value END) as plate_no
                                FROM `offense_list` r
                                inner join `drivers_list` d on r.driver_id = d.id
                                left join drivers_meta m on d.id = m.driver_id
                                where date(r.date_created) between '{$date_start}' and '{$date_end}'
                                group by r.id
                                order by unix_timestamp(r.date_created) desc ");
                            while($row = $qry->fetch_assoc()):
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td><?php echo date("d/m/Y H:i",strtotime($row['date_created'])) ?></td>
                                    <td><?php echo $row['ticket_no'] ?></td>
                                    <td><?php echo $row['plate_no'] ?: 'N/A' ?></td>
                                    <td><?php echo $row['license_id_no'] ?></td>
                                    <td><?php echo $row['location'] ?: 'N/A' ?></td>
                                    <td class="text-center"><?php echo $row['status'] == 1 ? '<span class="badge badge-success">Đã nộp</span>' : '<span class="badge badge-warning">Chưa nộp</span>' ?></td>
                                    <td class="text-right"><?php echo format_currency_vnd($row['total_amount']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if($qry->num_rows <=0 ): ?>
                                <tr><th class="text-center" colspan='8'>Không có dữ liệu.</th></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h5>Lỗi phổ biến</h5>
                    <table class="table table-bordered table-sm">
                        <thead><tr><th>Lỗi</th><th class="text-right">Số lượt</th></tr></thead>
                        <tbody>
                        <?php while($row = $popular->fetch_assoc()): ?>
                            <tr><td>[<?php echo $row['code'] ?>] <?php echo $row['name'] ?></td><td class="text-right"><?php echo number_format($row['total']) ?></td></tr>
                        <?php endwhile; ?>
                        <?php if($popular->num_rows <= 0): ?>
                            <tr><td colspan="2" class="text-center">Không có dữ liệu.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
        $('#filter').click(function(){
            location.replace("./?page=reports&date_start="+($('#date_start').val())+"&date_end="+($('#date_end').val()));
        })
        $('#print').click(function(){
            start_loader()
            var _h = $('head').clone()
            var _p = $('#print_out').clone();
            var _el = $('<div>')
            _el.append(_h)
            _el.append('<style>html, body, .wrapper {min-height: unset !important;}</style>')
            var rdate = "";
            if('<?php echo $date_start ?>' == '<?php echo $date_end ?>')
                rdate = "<?php echo date("d/m/Y",strtotime($date_start)) ?>";
            else
                rdate = "<?php echo date("d/m/Y",strtotime($date_start)) ?> - <?php echo date("d/m/Y",strtotime($date_end)) ?>";
            _p.prepend('<div class="d-flex mb-3 w-100 align-items-center justify-content-center">'+
            '<img class="mx-4" src="<?php echo validate_image($_settings->info('logo')) ?>" width="50px" height="50px"/>'+
            '<div class="px-2">'+
            '<h3 class="text-center"><?php echo $_settings->info('name') ?></h3>'+
            '<h3 class="text-center">Báo cáo vi phạm giao thông</h3>'+
            '<h4 class="text-center">'+rdate+'</h4>'+
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
