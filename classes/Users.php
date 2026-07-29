<?php
require_once('../config.php');

Class Users extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	private function esc($value){
		return $this->conn->real_escape_string($value ?? '');
	}
	private function arr_value($arr, $idx){
		return isset($arr[$idx]) ? trim($arr[$idx]) : '';
	}
	private function has_digit($value){
		return preg_match('/\d/u', $value);
	}
	private function response_error($message){
		return 'error:'.$message;
	}
	private function validate_user_payload(){
		$type = (int)($_POST['type'] ?? 2);
		$lastname = trim($_POST['lastname'] ?? '');
		$firstname = trim($_POST['firstname'] ?? '');
		$cccd = trim($_POST['cccd_no'] ?? '');
		$contact = trim($_POST['contact'] ?? '');
		$plates = $_POST['plate_no'] ?? [];
		$types = $_POST['vehicle_type'] ?? [];
		$brands = $_POST['vehicle_brand'] ?? [];
		$colors = $_POST['vehicle_color'] ?? [];

		foreach(['plates','types','brands','colors'] as $var){
			if(!is_array($$var))
				$$var = [$$var];
		}

		if($lastname === '' || $firstname === '')
			return 'Vui lòng nhập đầy đủ Họ và Tên.';
		if($this->has_digit($lastname))
			return 'Họ không được chứa số.';
		if($this->has_digit($firstname))
			return 'Tên không được chứa số.';
		if($cccd !== '' && !preg_match('/^\d+$/', $cccd))
			return 'Số CCCD chỉ được nhập chữ số.';
		if($contact === '')
			return 'Vui lòng nhập số điện thoại.';
		if(!preg_match('/^\d{10}$/', $contact))
			return 'Số điện thoại chỉ được nhập số và phải đủ 10 chữ số.';

		$row_count = max(count($plates), count($types), count($brands), count($colors), 1);
		$complete_vehicle_count = 0;
		for($i = 0; $i < $row_count; $i++){
			$plate = $this->arr_value($plates, $i);
			$vehicle_type = $this->arr_value($types, $i);
			$vehicle_brand = $this->arr_value($brands, $i);
			$vehicle_color = $this->arr_value($colors, $i);
			$has_any_vehicle_data = $plate !== '' || $vehicle_type !== '' || $vehicle_brand !== '' || $vehicle_color !== '';
			$is_complete_vehicle = $plate !== '' && $vehicle_type !== '' && $vehicle_brand !== '' && $vehicle_color !== '';

			if($is_complete_vehicle){
				$complete_vehicle_count++;
				continue;
			}
			if($has_any_vehicle_data && ($plate !== '' || $vehicle_brand !== '' || $vehicle_color !== ''))
				return 'Mỗi phương tiện đã nhập cần có đủ biển số, loại phương tiện, nhãn hiệu và màu xe.';
		}

		if($type == 2 && $complete_vehicle_count < 1)
			return 'Tài khoản User bắt buộc phải có ít nhất 1 phương tiện được điền đầy đủ.';
		return '';
	}
	private function owned_driver_ids($user_id){
		$ids = [];
		$qry = $this->conn->query("SELECT driver_id FROM drivers_meta WHERE meta_field = 'user_id' AND meta_value = '{$user_id}'");
		if($qry){
			while($row = $qry->fetch_assoc()){
				$ids[] = (int)$row['driver_id'];
			}
		}
		return $ids;
	}
	private function driver_belongs_to_user($driver_id, $user_id){
		$driver_id = (int)$driver_id;
		$user_id = (int)$user_id;
		if($driver_id <= 0 || $user_id <= 0)
			return false;
		$chk = $this->conn->query("SELECT driver_id FROM drivers_meta WHERE driver_id = '{$driver_id}' AND meta_field = 'user_id' AND meta_value = '{$user_id}' LIMIT 1");
		return ($chk && $chk->num_rows > 0);
	}
	private function save_driver_meta($driver_id, $rows){
		$meta_keys = ['user_id','firstname','lastname','license_id_no','cccd_no','contact','present_address','permanent_address','plate_no','vehicle_type','vehicle_brand','vehicle_color'];
		$this->conn->query("DELETE FROM drivers_meta WHERE driver_id = '{$driver_id}' AND meta_field IN ('".implode("','", $meta_keys)."')");
		$values = [];
		foreach($rows as $field => $value){
			$field = $this->esc($field);
			$value = $this->esc($value);
			$values[] = "('{$driver_id}','{$field}','{$value}')";
		}
		if(!empty($values))
			return $this->conn->query("INSERT INTO drivers_meta (`driver_id`,`meta_field`,`meta_value`) VALUES ".implode(',', $values));
		return true;
	}
	private function save_user_profile($user_id){
		$user_id = (int)$user_id;
		$type = (int)($_POST['type'] ?? 2);
		$lastname_raw = trim($_POST['lastname'] ?? '');
		$firstname_raw = trim($_POST['firstname'] ?? '');
		$lastname = $this->esc($lastname_raw);
		$firstname = $this->esc($firstname_raw);
		$fullname = $this->esc(trim($lastname_raw.' '.$firstname_raw));
		$license_raw = trim($_POST['license_id_no'] ?? '');
		$cccd_raw = trim($_POST['cccd_no'] ?? '');
		$contact_raw = trim($_POST['contact'] ?? '');
		$address_raw = trim($_POST['address'] ?? '');

		$vehicle_ids = $_POST['vehicle_id'] ?? [];
		$plates = $_POST['plate_no'] ?? [];
		$types = $_POST['vehicle_type'] ?? [];
		$brands = $_POST['vehicle_brand'] ?? [];
		$colors = $_POST['vehicle_color'] ?? [];
		foreach(['vehicle_ids','plates','types','brands','colors'] as $var){
			if(!is_array($$var))
				$$var = [$$var];
		}

		$row_count = max(count($vehicle_ids), count($plates), count($types), count($brands), count($colors), 1);
		$has_profile = $license_raw !== '' || $cccd_raw !== '' || $contact_raw !== '' || $address_raw !== '';
		$existing_ids = $this->owned_driver_ids($user_id);
		$kept_ids = [];

		if($type == 1 && !$has_profile && trim(implode('', $plates).implode('', $brands).implode('', $colors)) === '')
			return true;

		for($i = 0; $i < $row_count; $i++){
			$driver_id = (int)$this->arr_value($vehicle_ids, $i);
			$plate = $this->arr_value($plates, $i);
			$vehicle_type = $this->arr_value($types, $i);
			$vehicle_brand = $this->arr_value($brands, $i);
			$vehicle_color = $this->arr_value($colors, $i);
			$has_vehicle = $plate !== '' && $vehicle_type !== '' && $vehicle_brand !== '' && $vehicle_color !== '';

			if(!$has_profile && !$has_vehicle)
				continue;
			if(!$has_vehicle)
				continue;

			$license_id_no = $license_raw !== '' ? $license_raw : 'USER-'.$user_id.'-'.($i + 1);
			$license_id_no_esc = $this->esc($license_id_no);
			if($driver_id > 0 && $this->driver_belongs_to_user($driver_id, $user_id)){
				$this->conn->query("UPDATE drivers_list SET license_id_no = '{$license_id_no_esc}', name = '{$fullname}' WHERE id = '{$driver_id}'");
			}else{
				$this->conn->query("INSERT INTO drivers_list SET license_id_no = '{$license_id_no_esc}', name = '{$fullname}'");
				$driver_id = (int)$this->conn->insert_id;
			}

			$kept_ids[] = $driver_id;
			$this->save_driver_meta($driver_id, [
				'user_id' => $user_id,
				'firstname' => $firstname_raw,
				'lastname' => $lastname_raw,
				'license_id_no' => $license_raw,
				'cccd_no' => $cccd_raw,
				'contact' => $contact_raw,
				'present_address' => $address_raw,
				'permanent_address' => $address_raw,
				'plate_no' => strtoupper($plate),
				'vehicle_type' => $vehicle_type,
				'vehicle_brand' => $vehicle_brand,
				'vehicle_color' => $vehicle_color,
			]);
		}

		foreach($existing_ids as $old_id){
			if(!in_array($old_id, $kept_ids)){
				$this->conn->query("DELETE FROM drivers_meta WHERE driver_id = '{$old_id}' AND meta_field = 'user_id'");
			}
		}
		return true;
	}
	public function save_users(){
		$validation_msg = $this->validate_user_payload();
		if($validation_msg !== '')
			return $this->response_error($validation_msg);

		$id = $_POST['id'] ?? '';
		$user_fields = array('firstname','lastname','username','type');
		$data = '';
		foreach($user_fields as $k){
			if(isset($_POST[$k])){
				if(!empty($data)) $data .=" , ";
				$v = $this->esc($_POST[$k]);
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(!empty($_POST['password'])){
			$password = md5($_POST['password']);
			if(!empty($data)) $data .=" , ";
			$data .= " `password` = '{$password}' ";
		}

		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
			if($move){
				$data .=" , avatar = '{$fname}' ";
			}
		}
		if(empty($id)){
			$chk = $this->conn->query("SELECT id FROM users WHERE username = '".$this->esc($_POST['username'])."'")->num_rows;
			if($chk > 0) return 2;
			$qry = $this->conn->query("INSERT INTO users set {$data}");
			if($qry){
				$user_id = $this->conn->insert_id;
				$this->save_user_profile($user_id);
				$this->settings->set_flashdata('success','Thông tin người dùng đã được lưu.');
				return 1;
			}
			return 2;
		}else{
			$id = (int)$id;
			$chk = $this->conn->query("SELECT id FROM users WHERE username = '".$this->esc($_POST['username'])."' AND id != '{$id}'")->num_rows;
			if($chk > 0) return 2;
			$qry = $this->conn->query("UPDATE users set $data where id = {$id}");
			if($qry){
				$this->save_user_profile($id);
				$this->settings->set_flashdata('success','Thông tin người dùng đã được cập nhật.');
				return 1;
			}
			return "UPDATE users set $data where id = {$id}";
		}
	}
	public function change_password(){
		$id = (int)$this->settings->userdata('id');
		$current_password = $_POST['current_password'] ?? '';
		$new_password = $_POST['new_password'] ?? '';
		$confirm_password = $_POST['confirm_password'] ?? '';
		if($id <= 0 || $current_password === '' || $new_password === '' || $confirm_password === ''){
			return json_encode(['status' => 'failed', 'msg' => 'Vui lòng nhập đầy đủ thông tin mật khẩu.']);
		}
		if($new_password !== $confirm_password){
			return json_encode(['status' => 'failed', 'msg' => 'Mật khẩu mới và xác nhận mật khẩu không khớp.']);
		}
		if(strlen($new_password) < 6){
			return json_encode(['status' => 'failed', 'msg' => 'Mật khẩu mới cần có ít nhất 6 ký tự.']);
		}
		$qry = $this->conn->query("SELECT password FROM users WHERE id = '{$id}' LIMIT 1");
		if(!$qry || $qry->num_rows < 1){
			return json_encode(['status' => 'failed', 'msg' => 'Không tìm thấy tài khoản.']);
		}
		$row = $qry->fetch_assoc();
		if($row['password'] !== md5($current_password)){
			return json_encode(['status' => 'failed', 'msg' => 'Mật khẩu hiện tại không đúng.']);
		}
		$password = md5($new_password);
		$update = $this->conn->query("UPDATE users SET password = '{$password}' WHERE id = '{$id}'");
		if($update)
			return json_encode(['status' => 'success', 'msg' => 'Mật khẩu đã được cập nhật.']);
		return json_encode(['status' => 'failed', 'msg' => 'Không thể cập nhật mật khẩu.']);
	}
	public function delete_users(){
		$id = $_POST['id'] ?? '';
		$avatar = $this->conn->query("SELECT avatar FROM users where id = '{$id}'")->fetch_array()['avatar'];
		$qry = $this->conn->query("DELETE FROM users where id = $id");
		if($qry){
			$this->settings->set_flashdata('success','Người dùng đã được xóa.');
			if(is_file(base_app.$avatar))
				unlink(base_app.$avatar);
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save':
		echo $users->save_users();
	break;
	case 'change_password':
		echo $users->change_password();
	break;
	case 'delete':
		echo $users->delete_users();
	break;
	default:
		break;
}
?>
