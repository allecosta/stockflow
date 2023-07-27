<?php

require_once('../config.php');

Class Users extends DBConnection 
{
	private $settings;

	public function __construct()
	{
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
	}

	public function __destruct()
	{
		parent::__destruct();
	}

	public function saveUsers()
	{
		extract($_POST);

		$oId = $id;
		$data = '';

		if (isset($oldpassword)) {
			if (password_hash($oldpassword, PASSWORD_BCRYPT) != $this->settings->userdata('password')) {
				return 4;
			}
		}

		$chk = $this->conn->query("
			SELECT 
				* 
			FROM 
				`users` WHERE username ='{$username}' ".($id > 0 ? " and id!= '{$id}' " : ""))->num_rows;

		if ($chk > 0) {
			return 3;
			exit;
		}

		foreach ($_POST as $key => $value) {
			if (in_array($key, ['firstname','middlename','lastname','username','type'])) {
				if (!empty($data)) {
					$data .=" , ";
				} 

				$data .= " {$key} = '{$value}' ";
			}
		}

		if (!empty($password)) {
			$password = password_hash($password, PASSWORD_BCRYPT);
			if (!empty($data)) {
				$data .=" , ";
			} 

			$data .= " `password` = '{$password}' ";
		}

		if (empty($id)) {
			$query = $this->conn->query("INSERT INTO users SET {$data}");

			if ($query) {
				$id = $this->conn->insert_id;
				$this->settings->setFlashData('success','Detalhes do usuário salvo com sucesso.');
				$resp['status'] = 1;
			} else {
				$resp['status'] = 2;
			}
		} else {
			$query = $this->conn->query("UPDATE users set $data where id = {$id}");

			if ($query) {
				$this->settings->setFlashData('success','Detalhes do usuário atualizado com sucesso.');

				if ($id == $this->settings->userdata('id')) {
					foreach ($_POST as $key => $value) {
						if ($key != 'id') {
							if (!empty($data)) {
								$data .=" , ";
							} 

							$this->settings->setUserData($key,$value);
						}
					}
				}

				$resp['status'] = 1;

			} else {
				$resp['status'] = 2;
			}
			
		}

		if ($resp['status'] == 1) {
			$data="";

			foreach ($_POST as $key => $value) {
				if (!in_array($key, ['id','firstname','middlename','lastname','username','password','type','oldpassword'])) {
					if (!empty($data)) {
						$data .=", ";
					}

					$value = $this->conn->real_escape_string($value);
					$data .= "('{$id}','{$key}', '{$value}')";
				}
			}

			if (!empty($data)) {
				$this->conn->query("DELETE * FROM `user_meta` WHERE user_id = '{$id}' ");
				$save = $this->conn->query("INSERT INTO `user_meta` (user_id,`meta_field`,`meta_value`) VALUES {$data}");

				if (!$save) {
						$resp['status'] = 2;

					if (empty($oId)) {
						$this->conn->query("DELETE * FROM `users` WHERE id = '{$id}' ");
					}
				}
			}
		}
		
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = 'uploads/avatar-'.$id.'.png';
			$dirPath = BASE_APP. $fname;
			$upload = $_FILES['img']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = ['image/png','image/jpeg'];

			if (!in_array($type,$allowed)) {
				$resp['msg'].=" Mas a imagem não foi carregada devido a um tipo de arquivo inválido.";
			} else {
				$newHeight = 200; 
				$newWidth = 200; 
		
				list($width, $height) = getimagesize($upload);

				$tImage = imagecreatetruecolor($newWidth, $newHeight);

				imagealphablending( $tImage, false );
				imagesavealpha( $tImage, true );

				$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);

				imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

				if ($gdImg) {
						if (is_file($dirPath)) {
							unlink($dirPath);
						}
						
						$uploadedImg = imagepng($tImage,$dirPath);

						imagedestroy($gdImg);
						imagedestroy($tImage);
				} else {
				$resp['msg'].=" Mas a imagem falhou ao carregar devido a um motivo desconhecido.";
				}
			}

			if (isset($uploadedImg)) {
				$this->conn->query("
					UPDATE 
						users 
					SET 
						`avatar` = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) 
					WHERE id = '{$id}' ");

				if ($id == $this->settings->userdata('id')) {
						$this->settings->setUserData('avatar',$fname);
				}
			}
		}

		if (isset($resp['msg'])) {
			$this->settings->setFlashData('success',$resp['msg']);
		}
		
		return  $resp['status'];
	}

	public function deleteUsers() 
	{
		extract($_POST);

		$avatar = $this->conn->query("SELECT avatar FROM users WHERE id = '{$id}'")->fetch_array()['avatar'];
		$query = $this->conn->query("DELETE FROM users WHERE id = $id");

		if ($query) {
			$avatar = explode("?",$avatar)[0];

			$this->settings->setFlashData('success','Detalhes do usuário excluído com sucesso.');

			if (is_file(BASE_APP.$avatar)) {
				unlink(BASE_APP.$avatar);
			}
				
			$resp['status'] = 'success';

		} else {
			$resp['status'] = 'failed';
		}

		return json_encode($resp);
	}
	
	public function savesUsers() 
	{
		extract($_POST);

		$data = "";

		foreach ($_POST as $key => $value) {
			if (!in_array($key, ['id','password'])) {
				if (!empty($data)) {
					$data .= ", ";
				} 

				$data .= " `{$key}` = '{$value}' ";
			}
		}

		if (!empty($password)) {
			$data .= ", `password` = '". password_hash($password, PASSWORD_BCRYPT)."' ";
		}
		
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);

			if ($move) {
				$data .=" , avatar = '{$fname}' ";

				if (isset($_SESSION['userdata']['avatar']) && is_file('../'.$_SESSION['userdata']['avatar'])) {
					unlink('../'.$_SESSION['userdata']['avatar']);
				}
			}
		}

		$sql = "UPDATE students SET {$data} WHERE id = $id";
		$save = $this->conn->query($sql);

		if ($save) {
			$this->settings->setFlashData('success','Detalhes do usuário atualizado com sucesso.');

			foreach ($_POST as $key => $value) {
				if (!in_array($key, ['id','password'])) {
					if (!empty($data)) {
						$data .=" , ";
					} 

					$this->settings->setUserData($key,$value);
				}
			}

			if (isset($fname) && isset($move)) {
				$this->settings->setUserData('avatar',$fname);
			}
			
			return 1;

		} else {
			$resp['error'] = $sql;
			return json_encode($resp);
		}
	} 
}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);

switch ($action) {
	case 'save':
		echo $users->saveUsers();
	break;
	// case 'fsave':
	// 	echo $users->save_fusers();
	// break;
	case 'ssave':
		echo $users->savesUsers();
	break;
	case 'delete':
		echo $users->deleteUsers();
	break;
	default:
		break;
}