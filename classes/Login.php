<?php

require_once '../config.php';

class Login extends DBConnection 
{
	private $settings;

	public function __construct()
	{
		global $_settings;

		$this->settings = $_settings;

		parent::__construct();
		
		ini_set('display_error', 1);
	}

	public function __destruct()
	{
		parent::__destruct();
	}

	public function index()
	{
		echo "<h1>Access Denied</h1> <a href='".BASE_URL."'>Go Back.</a>";
	}

	public function login()
	{
		extract($_POST);

		$query = $this->conn->query("
			SELECT 
				* 
			FROM 
				users 
			WHERE 
				username = '$username' AND password = md5('$password')
			");

		if ($query->num_rows > 0) {
			foreach ($query->fetch_array() as $key => $value) {
				if (!is_numeric($key) && $key != 'password') {
					$this->settings->setUserData($key,$value);
				}
			}

			$this->settings->setUserData('login_type',1);

			return json_encode(array('status'=>'success'));

		} else {
			return json_encode([
				'status'=>'incorrect',
				'last_qry'=> "SELECT * FROM users WHERE username = '$username' AND password = md5('$password')"]);
		}
	}

	public function logout()
	{
		if ($this->settings->sessDes()) {
			redirect('admin/login.php');
		}
	}

	function loginUser() 
	{
		extract($_POST);

		$query = $this->conn->query("
			SELECT 
				* 
			FROM 
				users 
			WHERE 
				username = '$username' AND password = md5('$password') and `type` = 2"
			);

		if ($query->num_rows > 0) {
			foreach ($query->fetch_array() as $key => $value) {
				$this->settings->setUserData($key,$value);
			}

			$this->settings->setUserData('login_type',2);
			$resp['status'] = 'success';

		} else {
			$resp['status'] = 'incorrect';
		}

		if ($this->conn->error) {
			$resp['status'] = 'failed';
			$resp['_error'] = $this->conn->error;
		}

		return json_encode($resp);
	}

	public function logoutUser() 
	{
		if ($this->settings->sessDes()) {
			redirect('./');
		}
	}
}

$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();

switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'login_user':
		echo $auth->loginUser();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	case 'logout_user':
		echo $auth->logoutUser();
		break;
	default:
		echo $auth->index();
		break;
}

