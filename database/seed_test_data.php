<?php
require_once(__DIR__ . '/../initialize.php');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error . PHP_EOL);
}
$conn->set_charset('utf8mb4');

function esc($value) {
    global $conn;
    return $conn->real_escape_string((string)$value);
}

function insert_meta($driver_id, $rows) {
    global $conn;
    $values = [];
    foreach ($rows as $field => $value) {
        $values[] = "('".(int)$driver_id."','".esc($field)."','".esc($value)."')";
    }
    if (!empty($values)) {
        $conn->query("INSERT INTO drivers_meta (`driver_id`,`meta_field`,`meta_value`) VALUES " . implode(',', $values));
    }
}

function pick($items) {
    return $items[array_rand($items)];
}

function seed_date($days_ago, $hour_offset = 0) {
    return date('Y-m-d H:i:s', strtotime("-{$days_ago} days +{$hour_offset} hours"));
}

echo "Cleaning old seeded data...\n";
$seed_users = [];
$qry = $conn->query("SELECT id FROM users WHERE username LIKE 'seeduser%'");
while ($row = $qry->fetch_assoc()) {
    $seed_users[] = (int)$row['id'];
}

if (!empty($seed_users)) {
    $user_ids = implode(',', $seed_users);
    $driver_ids = [];
    $qry = $conn->query("SELECT DISTINCT driver_id FROM drivers_meta WHERE meta_field = 'user_id' AND meta_value IN ({$user_ids})");
    while ($row = $qry->fetch_assoc()) {
        $driver_ids[] = (int)$row['driver_id'];
    }
    if (!empty($driver_ids)) {
        $ids = implode(',', $driver_ids);
        $conn->query("DELETE FROM offense_list WHERE driver_id IN ({$ids})");
        $conn->query("DELETE FROM drivers_list WHERE id IN ({$ids})");
    }
    $conn->query("DELETE FROM users WHERE id IN ({$user_ids})");
}
$conn->query("DELETE FROM offenses WHERE code LIKE 'VPGT-%'");

echo "Creating offense catalog...\n";
$offenses = [
    ['VPGT-2001', 'Vượt đèn đỏ', 'Không chấp hành tín hiệu đèn giao thông.', 5000000],
    ['VPGT-2002', 'Đi quá tốc độ từ 10 đến 20 km/h', 'Điều khiển xe chạy quá tốc độ quy định.', 1200000],
    ['VPGT-2003', 'Đi quá tốc độ trên 20 km/h', 'Điều khiển xe chạy quá tốc độ ở mức nghiêm trọng.', 7000000],
    ['VPGT-2004', 'Không đội mũ bảo hiểm', 'Người điều khiển hoặc người ngồi trên xe mô tô không đội mũ bảo hiểm.', 500000],
    ['VPGT-2005', 'Đi sai làn đường', 'Điều khiển phương tiện không đúng làn đường quy định.', 2500000],
    ['VPGT-2006', 'Dừng đỗ sai quy định', 'Dừng, đỗ xe tại nơi có biển cấm hoặc gây cản trở giao thông.', 900000],
    ['VPGT-2007', 'Không chấp hành hiệu lệnh CSGT', 'Không tuân thủ hiệu lệnh của người điều khiển giao thông.', 6000000],
    ['VPGT-2008', 'Sử dụng điện thoại khi lái xe', 'Sử dụng điện thoại di động khi đang điều khiển phương tiện.', 1000000],
    ['VPGT-2009', 'Không thắt dây an toàn', 'Người lái hoặc hành khách không thắt dây an toàn theo quy định.', 800000],
    ['VPGT-2010', 'Chở quá số người quy định', 'Chở quá số người được phép trên phương tiện.', 1500000],
    ['VPGT-2011', 'Không có bảo hiểm trách nhiệm dân sự', 'Không mang hoặc không có bảo hiểm bắt buộc còn hiệu lực.', 600000],
    ['VPGT-2012', 'Đi ngược chiều', 'Đi vào đường cấm hoặc đi ngược chiều trên tuyến đường một chiều.', 5000000],
    ['VPGT-2013', 'Vi phạm nồng độ cồn mức 1', 'Điều khiển phương tiện khi trong máu hoặc hơi thở có nồng độ cồn.', 8000000],
    ['VPGT-2014', 'Vi phạm nồng độ cồn mức cao', 'Điều khiển phương tiện khi nồng độ cồn vượt mức nghiêm trọng.', 35000000],
    ['VPGT-2015', 'Không nhường đường cho xe ưu tiên', 'Không giảm tốc độ hoặc nhường đường cho xe ưu tiên.', 3000000],
    ['VPGT-2016', 'Che khuất biển số', 'Biển số bị che lấp, không rõ hoặc không đúng quy định.', 1000000],
    ['VPGT-2017', 'Không bật xi nhan khi chuyển hướng', 'Chuyển hướng hoặc chuyển làn không có tín hiệu báo trước.', 600000],
    ['VPGT-2018', 'Chở hàng quá tải', 'Phương tiện chở hàng vượt tải trọng cho phép.', 12000000],
];

$offense_ids = [];
foreach ($offenses as $item) {
    [$code, $name, $description, $fine] = $item;
    $conn->query("INSERT INTO offenses (`code`,`name`,`description`,`fine`,`status`,`date_created`) VALUES ('".esc($code)."','".esc($name)."','".esc($description)."','".(float)$fine."',1,NOW())");
    $offense_ids[] = (int)$conn->insert_id;
}

echo "Creating 30 users, vehicles, and offense records...\n";
$lastnames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Phan', 'Vũ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Mai'];
$firstnames = ['Minh Anh', 'Quang Huy', 'Thu Hà', 'Gia Bảo', 'Khánh Linh', 'Tuấn Kiệt', 'Bảo Ngọc', 'Hoài Nam', 'Thanh Trúc', 'Đức Anh', 'Phương Thảo', 'Nhật Minh', 'An Nhiên', 'Hữu Phước', 'Mỹ Duyên'];
$districts = ['Cầu Giấy', 'Thanh Xuân', 'Hoàng Mai', 'Đống Đa', 'Ba Đình', 'Long Biên', 'Hà Đông', 'Nam Từ Liêm', 'Bình Thạnh', 'Quận 1', 'Thủ Đức', 'Hải Châu'];
$streets = ['Nguyễn Trãi', 'Giải Phóng', 'Trường Chinh', 'Lê Văn Lương', 'Phạm Văn Đồng', 'Võ Chí Công', 'Điện Biên Phủ', 'Cộng Hòa', 'Xa lộ Hà Nội', 'Hoàng Diệu'];
$vehicle_types = ['Ô tô', 'Xe máy', 'Xe tải', 'Xe khách'];
$brands = [
    'Ô tô' => ['Toyota Vios', 'Hyundai Accent', 'Kia Seltos', 'Mazda 3', 'Honda City', 'Ford Ranger'],
    'Xe máy' => ['Honda Vision', 'Honda Air Blade', 'Yamaha Exciter', 'Yamaha Grande', 'Honda SH', 'Suzuki Raider'],
    'Xe tải' => ['Hyundai Mighty', 'Isuzu QKR', 'Hino 300', 'Thaco Towner'],
    'Xe khách' => ['Thaco Bus', 'Hyundai County', 'Samco Felix', 'Fuso Rosa'],
];
$colors = ['Trắng', 'Đen', 'Đỏ', 'Xanh dương', 'Bạc', 'Xám', 'Vàng', 'Nâu'];
$officers = [
    ['Nguyễn Văn Cường', 'CSGT-00124'],
    ['Trần Quốc Hưng', 'CSGT-00231'],
    ['Lê Minh Tuấn', 'CSGT-00452'],
    ['Phạm Hải Yến', 'CSGT-00619'],
    ['Đặng Hoàng Long', 'CSGT-00788'],
];
$locations = [
    'Nút giao Nguyễn Trãi - Khuất Duy Tiến',
    'Đường Giải Phóng, gần bến xe Giáp Bát',
    'Cầu vượt Mai Dịch',
    'Ngã tư Sở, quận Đống Đa',
    'Đại lộ Thăng Long, km số 6',
    'Đường Phạm Văn Đồng, gần công viên Hòa Bình',
    'Vòng xoay Điện Biên Phủ',
    'Xa lộ Hà Nội, đoạn trước Metro',
    'Đường Cộng Hòa, quận Tân Bình',
    'Ngã sáu Phù Đổng',
];
$evidence_images = [
    'https://images.unsplash.com/photo-1503376780353-7e6692767b70',
    'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d',
    'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7',
    'https://images.unsplash.com/photo-1506521781263-d8422e82f27a',
    'https://images.unsplash.com/photo-1511919884226-fd3cad34687c',
];
$evidence_video = 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4';
$payment_methods = ['Chuyển khoản ngân hàng', 'Ví điện tử', 'Nộp trực tiếp'];

$user_count = 0;
$vehicle_count = 0;
$record_count = 0;

for ($i = 1; $i <= 30; $i++) {
    $lastname = $lastnames[($i - 1) % count($lastnames)];
    $firstname = $firstnames[($i - 1) % count($firstnames)];
    $username = sprintf('seeduser%02d', $i);
    $password = md5('123456');
    $avatar = 'uploads/user_avatar.jpg';
    $conn->query("INSERT INTO users (`firstname`,`lastname`,`username`,`password`,`avatar`,`type`,`date_added`) VALUES ('".esc($firstname)."','".esc($lastname)."','".esc($username)."','{$password}','{$avatar}',2,NOW())");
    $user_id = (int)$conn->insert_id;
    $user_count++;

    $license = sprintf('GPLX-%06d', 720000 + $i);
    $cccd = sprintf('0%d%09d', ($i % 9) + 1, 260000000 + $i);
    $contact = '09' . sprintf('%08d', 12000000 + ($i * 3719));
    $address = (($i % 240) + 1) . ' ' . pick($streets) . ', ' . pick($districts);
    $vehicle_total = 1 + ($i % 3);

    for ($j = 1; $j <= $vehicle_total; $j++) {
        $type = $vehicle_types[($i + $j) % count($vehicle_types)];
        $plate_prefix = ($type == 'Xe máy') ? '29' : (($i % 2 == 0) ? '30' : '51');
        $plate_letter = chr(65 + (($i + $j) % 20));
        $plate = sprintf('%s%s-%03d.%02d', $plate_prefix, $plate_letter, 100 + $i * 3 + $j, 10 + $j * 7);
        $brand = pick($brands[$type]);
        $color = pick($colors);
        $name = $lastname . ' ' . $firstname;

        $conn->query("INSERT INTO drivers_list (`license_id_no`,`name`,`status`,`date_created`) VALUES ('".esc($license)."','".esc($name)."',1,NOW())");
        $driver_id = (int)$conn->insert_id;
        $vehicle_count++;
        insert_meta($driver_id, [
            'user_id' => $user_id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'license_id_no' => $license,
            'cccd_no' => $cccd,
            'contact' => $contact,
            'present_address' => $address,
            'permanent_address' => $address,
            'plate_no' => $plate,
            'vehicle_type' => $type,
            'vehicle_brand' => $brand,
            'vehicle_color' => $color,
            'driver_id' => $driver_id,
        ]);

        $case_total = (($i + $j) % 4 == 0) ? 0 : 1 + (($i + $j) % 2);
        for ($k = 1; $k <= $case_total; $k++) {
            $status = (($i + $j + $k) % 3 == 0) ? 1 : 0;
            $days_ago = 5 + (($i * 3 + $j * 7 + $k * 11) % 115);
            $due_offset = ($status == 1) ? 15 : ((($i + $k) % 2 == 0) ? -5 : 25);
            $due_date = date('Y-m-d', strtotime(seed_date($days_ago) . " +{$due_offset} days"));
            $officer = pick($officers);
            $ticket = sprintf('BB2026-%04d-%02d%02d', $i, $j, $k);
            if (($i + $j + $k) % 5 == 0) {
                $evidence_path = $evidence_video;
                $evidence_type = 'video';
            } else {
                $evidence_path = pick($evidence_images);
                $evidence_type = 'image';
            }

            $item_total = 1 + (($i + $j + $k) % 3);
            $selected = [];
            while (count($selected) < $item_total) {
                $oid = pick($offense_ids);
                if (!in_array($oid, $selected)) {
                    $selected[] = $oid;
                }
            }
            $total_amount = 0;
            foreach ($selected as $oid) {
                $fine_qry = $conn->query("SELECT fine FROM offenses WHERE id = '{$oid}'");
                $total_amount += (float)$fine_qry->fetch_assoc()['fine'];
            }
            $payment_method = $status == 1 ? pick($payment_methods) : '';
            $payment_reference = $status == 1 ? 'VPGT-' . str_replace('-', '', $ticket) : '';
            $remarks = $status == 1
                ? 'Người vi phạm đã hoàn tất nghĩa vụ nộp phạt. Có thể xuất biên lai để đối soát.'
                : 'Yêu cầu người vi phạm nộp phạt đúng hạn. Nếu có khiếu nại, gửi phản hồi kèm bằng chứng liên quan.';
            $date_created = seed_date($days_ago, ($i + $j + $k) % 8);

            $conn->query("INSERT INTO offense_list (`driver_id`,`officer_name`,`officer_id`,`ticket_no`,`location`,`total_amount`,`due_date`,`remarks`,`evidence_path`,`evidence_type`,`status`,`payment_method`,`payment_reference`,`date_created`) VALUES (
                '{$driver_id}',
                '".esc($officer[0])."',
                '".esc($officer[1])."',
                '".esc($ticket)."',
                '".esc(pick($locations))."',
                '{$total_amount}',
                '{$due_date}',
                '".esc($remarks)."',
                '".esc($evidence_path)."',
                '".esc($evidence_type)."',
                '{$status}',
                '".esc($payment_method)."',
                '".esc($payment_reference)."',
                '{$date_created}'
            )");
            $record_id = (int)$conn->insert_id;
            $record_count++;
            foreach ($selected as $oid) {
                $fine_qry = $conn->query("SELECT fine FROM offenses WHERE id = '{$oid}'");
                $fine = (float)$fine_qry->fetch_assoc()['fine'];
                $conn->query("INSERT INTO offense_items (`driver_offense_id`,`offense_id`,`fine`,`status`,`date_created`) VALUES ('{$record_id}','{$oid}','{$fine}','{$status}','{$date_created}')");
            }
        }
    }
}

echo "Seed complete.\n";
echo "Users: {$user_count}\n";
echo "Vehicles: {$vehicle_count}\n";
echo "Offense catalog items: " . count($offenses) . "\n";
echo "Offense records: {$record_count}\n";
echo "Login examples: seeduser01 / 123456, seeduser15 / 123456, seeduser30 / 123456\n";
?>
