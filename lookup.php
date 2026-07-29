<?php
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$records = false;

if($keyword !== ''){
    $search = "%{$keyword}%";
    $stmt = $conn->prepare("SELECT r.*, d.license_id_no, d.name AS driver_name,
            MAX(CASE WHEN m.meta_field = 'plate_no' THEN m.meta_value END) AS plate_no,
            MAX(CASE WHEN m.meta_field = 'cccd_no' THEN m.meta_value END) AS cccd_no,
            MAX(CASE WHEN m.meta_field = 'vehicle_type' THEN m.meta_value END) AS vehicle_type,
            MAX(CASE WHEN m.meta_field = 'vehicle_brand' THEN m.meta_value END) AS vehicle_brand,
            MAX(CASE WHEN m.meta_field = 'vehicle_color' THEN m.meta_value END) AS vehicle_color
        FROM offense_list r
        INNER JOIN drivers_list d ON r.driver_id = d.id
        LEFT JOIN drivers_meta m ON d.id = m.driver_id
        GROUP BY r.id
        HAVING d.license_id_no LIKE ? OR r.ticket_no LIKE ? OR plate_no LIKE ? OR cccd_no LIKE ?
        ORDER BY UNIX_TIMESTAMP(r.date_created) DESC");
    $stmt->bind_param("ssss", $search, $search, $search, $search);
    $stmt->execute();
    $records = $stmt->get_result();
}

function lookup_offense_items($conn, $record_id){
    return $conn->query("SELECT i.*, o.code, o.name FROM offense_items i INNER JOIN offenses o ON i.offense_id = o.id WHERE i.driver_offense_id = '{$record_id}'");
}
?>
<section class="py-5">
    <div class="container px-4 px-lg-5">
        <div class="mb-4">
            <a href="<?php echo base_url ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Quay lại trang tra cứu
            </a>
        </div>
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title mb-0">Tra cứu vi phạm giao thông</h3>
            </div>
            <div class="card-body">
                <form action="<?php echo base_url ?>" method="get" class="row mb-3">
                    <input type="hidden" name="p" value="lookup">
                    <div class="col-md-9 mb-2">
                        <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($keyword) ?>" placeholder="Nhập biển số xe, số CCCD, số GPLX hoặc số biên bản" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-primary btn-block" type="submit">
                            <i class="fa fa-search"></i> Tra cứu
                        </button>
                    </div>
                </form>
                <div class="text-muted small">
                    Có thể tra cứu bằng biển số xe, số CCCD, số giấy phép lái xe hoặc số biên bản.
                </div>
            </div>
        </div>

        <?php if($keyword === ''): ?>
            <div class="alert alert-info">Vui lòng nhập thông tin để tra cứu vi phạm.</div>
        <?php elseif($records && $records->num_rows > 0): ?>
            <?php while($row = $records->fetch_assoc()): ?>
                <?php $items = lookup_offense_items($conn, $row['id']); ?>
                <div class="card mb-4 violation-card" id="receipt-<?php echo $row['id'] ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Biên bản #<?php echo htmlspecialchars($row['ticket_no']) ?></h5>
                        <?php if($row['status'] == 1): ?>
                            <span class="badge badge-success">Đã nộp phạt</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Chưa nộp phạt</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-7">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Thời gian vi phạm</dt>
                                    <dd class="col-sm-8"><?php echo date("d/m/Y H:i", strtotime($row['date_created'])) ?></dd>
                                    <dt class="col-sm-4">Địa điểm</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($row['location'] ?? 'Chưa cập nhật') ?></dd>
                                    <dt class="col-sm-4">Biển số xe</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($row['plate_no'] ?? 'Chưa cập nhật') ?></dd>
                                    <dt class="col-sm-4">Số GPLX</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($row['license_id_no']) ?></dd>
                                    <dt class="col-sm-4">Số CCCD</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($row['cccd_no'] ?? 'Chưa cập nhật') ?></dd>
                                    <dt class="col-sm-4">Người vi phạm</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($row['driver_name']) ?></dd>
                                    <dt class="col-sm-4">Phương tiện</dt>
                                    <dd class="col-sm-8">
                                        <?php echo htmlspecialchars(trim(($row['vehicle_type'] ?? '').' '.($row['vehicle_brand'] ?? '').' '.($row['vehicle_color'] ?? '')) ?: 'Chưa cập nhật') ?>
                                    </dd>
                                    <dt class="col-sm-4">Hạn nộp phạt</dt>
                                    <dd class="col-sm-8"><?php echo !empty($row['due_date']) ? date("d/m/Y", strtotime($row['due_date'])) : 'Chưa cập nhật' ?></dd>
                                    <dt class="col-sm-4">Tổng tiền cần nộp</dt>
                                    <dd class="col-sm-8 font-weight-bold text-danger"><?php echo format_currency_vnd($row['total_amount']) ?></dd>
                                </dl>
                            </div>
                            <div class="col-lg-5">
                                <h6>Bằng chứng vi phạm</h6>
                                <?php if(!empty($row['evidence_path'])): ?>
                                    <?php $evidence_url = validate_image($row['evidence_path']); ?>
                                    <?php if($row['evidence_type'] == 'video'): ?>
                                        <video src="<?php echo $evidence_url ?>" controls class="w-100 border"></video>
                                    <?php else: ?>
                                        <a href="<?php echo $evidence_url ?>" target="_blank">
                                            <img src="<?php echo $evidence_url ?>" class="img-fluid img-thumbnail" alt="Bằng chứng vi phạm">
                                        </a>
                                    <?php endif; ?>
                                    <a class="btn btn-sm btn-outline-primary mt-2" href="<?php echo $evidence_url ?>" download>
                                        <i class="fa fa-download"></i> Tải bằng chứng
                                    </a>
                                <?php else: ?>
                                    <div class="alert alert-secondary">Chưa có ảnh/video bằng chứng.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr>
                        <h6>Danh sách lỗi vi phạm</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Mã lỗi</th>
                                        <th>Lỗi vi phạm</th>
                                        <th class="text-right">Mức phạt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($item = $items->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['code']) ?></td>
                                            <td><?php echo htmlspecialchars($item['name']) ?></td>
                                            <td class="text-right"><?php echo format_currency_vnd($item['fine']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="alert alert-info mb-0">
                                    <b>Thanh toán:</b> Chuyển khoản ngân hàng<br>
                                    <b>Ngân hàng:</b> ViettinBank<br>
                                    <b>Mã ngân hàng:</b> 10422511655978<br>
                                    <b>Nội dung chuyển khoản:</b> VPGT-<?php echo htmlspecialchars($row['ticket_no']) ?>
                                </div>
                            </div>
                            <div class="col-md-5 text-md-right mt-3 mt-md-0">
                                <?php if($row['status'] == 1): ?>
                                    <button class="btn btn-success" type="button" onclick="print_receipt('receipt-<?php echo $row['id'] ?>')">
                                        <i class="fa fa-file-pdf"></i> Xuất biên lai
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-primary" type="button" onclick="print_receipt('receipt-<?php echo $row['id'] ?>')">
                                        <i class="fa fa-credit-card"></i> In hướng dẫn thanh toán
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-warning">
                Không tìm thấy vi phạm phù hợp với từ khóa "<?php echo htmlspecialchars($keyword) ?>".
            </div>
        <?php endif; ?>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header"><b>Hỗ trợ / Khiếu nại</b></div>
                    <div class="card-body">
                        <p>Nếu thông tin chưa chính xác, vui lòng gửi phản hồi kèm số biên bản, biển số xe và nội dung cần hỗ trợ.</p>
                        <form>
                            <input class="form-control mb-2" placeholder="Họ tên">
                            <input class="form-control mb-2" placeholder="Số điện thoại / email">
                            <textarea class="form-control mb-2" rows="3" placeholder="Nội dung phản hồi"></textarea>
                            <button type="button" class="btn btn-outline-primary">Gửi phản hồi</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header"><b>Câu hỏi thường gặp</b></div>
                    <div class="card-body">
                        <p><b>Tôi có thể tra cứu bằng gì?</b><br>Biển số xe, số CCCD, số GPLX hoặc số biên bản.</p>
                        <p><b>Khi nào cần nộp phạt?</b><br>Vui lòng nộp trước hạn nộp phạt hiển thị trên từng biên bản.</p>
                        <p><b>Biên lai ở đâu?</b><br>Khi biên bản được xác nhận đã nộp, bạn có thể xuất biên lai từ kết quả tra cứu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
function print_receipt(id){
    var content = document.getElementById(id).cloneNode(true);
    var win = window.open('', '_blank', 'width=1000,height=800');
    win.document.write('<html><head><title>Biên lai vi phạm giao thông</title>');
    win.document.write('<link rel="stylesheet" href="<?php echo base_url ?>dist/css/adminlte.css">');
    win.document.write('</head><body class="p-4">');
    win.document.write(content.outerHTML);
    win.document.write('</body></html>');
    win.document.close();
    setTimeout(function(){ win.print(); }, 500);
}
</script>
