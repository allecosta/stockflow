<?php

require_once('../config.php');

Class Master extends DBConnection 
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

	function captureErr() {
		if (!$this->conn->error) {
			return false;
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;

			return json_encode($resp);
			exit;
		}
	}

	function saveSupplier() 
	{
		extract($_POST);

		$data = "";

		foreach ($_POST as $key =>$value) {
			if (!in_array($key,array('id'))) {
				if (!empty($data)) {
					$data .=",";
				} 

				$data .= " `{$key}`='{$value}' ";
			}
		}

		$check = $this->conn->query("
			SELECT 
				* 
			FROM 
				`supplier_list` 
			WHERE 
				`name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;

		if ($this->captureErr()) {
			return $this->captureErr();
		}
			
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Este nome de fornecedor já existe.";

			return json_encode($resp);
			exit;
		}

		if (empty($id)) {
			$sql = "INSERT INTO `supplier_list` set {$data} ";
			$save = $this->conn->query($sql);
		} else {
			$sql = "UPDATE `supplier_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}

		if ($save) {
			$resp['status'] = 'success';

			if (empty($id)) {
				$res['msg'] = "Novo fornecedor salvo com sucesso.";
				$id = $this->conn->insert_id;
			} else {
				$res['msg'] = "Fornecedor atualiazado com sucesso.";
			}

			$this->settings->setFlashData('success',$res['msg']);

		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}

		return json_encode($resp);
	}

	function deleteSupplier() 
	{
		extract($_POST);

		$del = $this->conn->query("DELETE FROM `supplier_list` where id = '{$id}'");

		if ($del) {
			$resp['status'] = 'success';
			$this->settings->setFlashData('success',"Fornecedor excluido com sucesso.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}

		return json_encode($resp);

	}

	function saveItem() 
	{
		extract($_POST);

		$data = "";

		foreach ($_POST as $key => $value) {
			if (!in_array($key, ['id'])) {
				$value = $this->conn->real_escape_string($value);

				if (!empty($data)) {
					$data .=",";
				} 

				$data .= " `{$key}`='{$value}' ";
			}
		}

		$check = $this->conn->query("
			SELECT 
				* 
			FROM 
				`item_list` 
			WHERE 
				`name` = '{$name}' AND `supplier_id` = '{$supplier_id}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;

		if ($this->captureErr()) {
			return $this->captureErr();
		}
			
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Este item já existe no fornecedor selecionado.";

			return json_encode($resp);
			exit;
		}

		if (empty($id)) {
			$sql = "INSERT INTO `item_list` SET {$data} ";
			$save = $this->conn->query($sql);
		} else {
			$sql = "UPDATE `item_list` set {$data} WHERE id = '{$id}' ";
			$save = $this->conn->query($sql);
		}

		if ($save) {
			$resp['status'] = 'success';

			if(empty($id)) {
				$this->settings->setFlashData('success'," Novo item salvo com sucesso.");
			} else {
				$this->settings->setFlashData('success'," Item atualizado com sucesso.");
			}
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}

		return json_encode($resp);
	}

	function deleteItem()
	{
		extract($_POST);

		$del = $this->conn->query("DELETE FROM `item_list` WHERE id = '{$id}'");

		if ($del) {
			$resp['status'] = 'success';
			$this->settings->setFlashData('success'," Item  excluido com sucesso.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}

		return json_encode($resp);

	}

	function savePurchaseOrder() 
	{
		if (empty($_POST['id'])) {
			$prefix = "PO";
			$code = sprintf("%'.04d",1);

			while (true) {
				$checkCode = $this->conn->query("
					SELECT * FROM `purchase_order_list` WHERE po_code ='".$prefix.'-'.$code."' ")->num_rows;

				if ($checkCode > 0) {
					$code = sprintf("%'.04d",$code + 1);
				} else {
					break;
				}
			}

			$_POST['po_code'] = $prefix."-".$code;
		}

		extract($_POST);

		$data = "";

		foreach ($_POST as $key =>$value) {
			if (!in_array($key,array('id')) && !is_array($_POST[$key])) {
				if (!is_numeric($value)) {
					$value= $this->conn->real_escape_string($value);
				}
				
				if (!empty($data)) {
					$data .=", ";
				} 

				$data .=" `{$key}` = '{$value}' ";
			}
		}

		if (empty($id)) {
			$sql = "INSERT INTO `purchase_order_list` SET {$data}";
		} else {
			$sql = "UPDATE `purchase_order_list` SET {$data} WHERE id = '{$id}'";
		}

		$save = $this->conn->query($sql);

		if ($save) {
			$resp['status'] = 'success';

			if (empty($id)) {
				$poID= $this->conn->insert_id;
			} else {
				$poID = $id;
			}
			
			$resp['id'] = $poID;
			$data = "";

			foreach ($itemID as $key => $value) {
				if (!empty($data)) {
					$data .=", ";
				} 

				$data .= "('{$poID}','{$value}','{$qty[$key]}','{$price[$key]}','{$unit[$key]}','{$total[$key]}')";
			}

			if (!empty($data)) {
				$this->conn->query("DELETE FROM `po_items` WHERE po_id = '{$poID}'");
				$save = $this->conn->query("
					INSERT INTO 
						`po_items` (`po_id`,`item_id`,`quantity`,`price`,`unit`,`total`) 
					VALUES 
						{$data}");

				if (!$save) {
					$resp['status'] = 'failed';

					if (empty($id)) {
						$this->conn->query("DELETE FROM `purchase_order_list` WHERE id '{$poID}'");
					}

					$resp['msg'] = 'Falha ao salvar a ordem de comprar. Error: '.$this->conn->error;
					$resp['sql'] = "INSERT INTO `po_items` (`po_id`,`item_id`,`quantity`,`price`,`unit`,`total`) VALUES {$data}";
				}
			}
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Ocorreu um erro. Error: '.$this->conn->error;
		}
		if ($resp['status'] == 'success') {
			if (empty($id)) {
				$this->settings->setFlashData('success'," Nova ordem de compra foi criado com sucesso.");
			} else {
				$this->settings->setFlashData('success'," Detalhes da ordem de compra atualizado com sucesso.");
			}
		}

		return json_encode($resp);
	}

	function deletePurchaseOrder() 
	{
		extract($_POST);

		$backOrderList = $this->conn->query("SELECT * FROM back_order_list WHERE po_id = '{$id}'");
		$del = $this->conn->query("DELETE FROM `purchase_order_list` WHERE id = '{$id}'");

		if ($del) {
			$resp['status'] = 'success';
			$this->settings->setFlashData('success',"Detalhes de ordem de compra excluido com sucesso.");

			if ($backOrderList->num_rows > 0) {
				$backOrderRes = $backOrderList->fetch_all(MYSQLI_ASSOC);
				$receivingIds = array_column($backOrderRes, 'receiving_id');
				$backOrderIds = array_column($backOrderRes, 'id');
			}

			$query = $this->conn->query("
				SELECT 
					* 
				FROM 
					receiving_list 
				WHERE 
					(form_id='{$id}' and from_order = '1') ".(isset($receivingIds) && count($receivingIds) > 0 ? "OR id in (".(implode(',',$receivingIds)).") OR (form_id in (".(implode(',',$backOrderIds)).") and from_order = '2') " : "" )." ");

			while ($row = $query->fetch_assoc()) {
				$this->conn->query("DELETE FROM `stock_list` WHERE id in ({$row['stock_ids']}) ");
			}
			
			$this->conn->query("
				DELETE FROM 
					receiving_list 
				WHERE (form_id='{$id}' and from_order = '1') ".(isset($receivingIds) && count($receivingIds) > 0 ? "OR id in (".(implode(',',$receivingIds)).") OR (form_id in (".(implode(',',$backOrderIds)).") and from_order = '2') " : "" )." ");
	
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}

		return json_encode($resp);

	}

	function saveReceiving()
	{
		if (empty($_POST['id'])) {
			$prefix = "BO";
			$code = sprintf("%'.04d",1);

			while (true) {
				$checkCode = $this->conn->query("
					SELECT 
						* 
					FROM 
						`back_order_list` 
					WHERE bo_code ='".$prefix.'-'.$code."' ")->num_rows;

				if ($checkCode > 0) {
					$code = sprintf("%'.04d",$code + 1);
				} else {
					break;
				}
			}

			$_POST['bo_code'] = $prefix."-".$code;

		} else {
			$get = $this->conn->query("SELECT * FROM back_order_list WHERE receiving_id = '{$_POST['id']}' ");

			if ($get->num_rows > 0) {
				$res = $get->fetch_array();
				$backOrderId = $res['id'];
				$_POST['bo_code'] = $res['bo_code'];
			} else {

				$prefix = "BO";
				$code = sprintf("%'.04d",1);

				while (true) {
					$checkCode = $this->conn->query("
						SELECT 
							* 
						FROM 
							`back_order_list` 
						WHERE 
							bo_code ='".$prefix.'-'.$code."' ")->num_rows;

					if ($checkCode > 0) {
						$code = sprintf("%'.04d",$code + 1);
					} else {
						break;
					}
				}

				$_POST['bo_code'] = $prefix."-".$code;

			}
		}

		extract($_POST);

		$data = "";

		foreach ($_POST as $key =>$value) {

			if (!in_array($key, ['id','bo_code','supplier_id','po_id']) && !is_array($_POST[$key])) {
				if (!is_numeric($value)) {
					$value = $this->conn->real_escape_string($value);
				}
				
				if (!empty($data)) {
					$data .=", ";
				} 

				$data .=" `{$key}` = '{$value}' ";
			}
		}

		if (empty($id)) {
			$sql = "INSERT INTO `receiving_list` SET {$data}";
		} else {
			$sql = "UPDATE `receiving_list` SET {$data} WHERE id = '{$id}'";
		}

		$save = $this->conn->query($sql);

		if ($save) {
			$resp['status'] = 'success';

			if (empty($id)) {
				$receivingId = $this->conn->insert_id;
			} else {
				$receivingId = $id;
			}
			
			$resp['id'] = $receivingId;

			if (!empty($id)) {
				$stockIds = $this->conn->query("
					SELECT 
						stock_ids 
					FROM 
						`receiving_list` 
					WHERE 
						id = '{$id}'")->fetch_array()['stock_ids'];

				$this->conn->query("DELETE FROM `stock_list` WHERE id IN ({$stockIds})");
			}

			$stockIds= [];

			foreach ($itemId as $key =>$value) {
				if (!empty($data)) {
					$data .=", ";
				} 

				$sql = "INSERT INTO stock_list (`item_id`,`quantity`,`price`,`unit`,`total`,`type`) VALUES ('{$value}','{$qty[$key]}','{$price[$key]}','{$unit[$key]}','{$total[$key]}','1')";
				$this->conn->query($sql);
				$stockIds[] = $this->conn->insert_id;

				if ($qty[$key] < $oqty[$key]) {
					$backOrderIds[] = $key;
				}
			}

			if (count($stockIds) > 0) {
				$stockIds = implode(',',$stockIds);
				$this->conn->query("UPDATE `receiving_list` SET stock_ids = '{$stockIds}' WHERE id = '{$receivingId}'");
			}

			if (isset($backOrderIds)) {
				$this->conn->query("UPDATE `purchase_order_list` SET status = 1 WHERE id = '{$purchaseOrderId}'");
				if($from_order == 2){
					$this->conn->query("UPDATE `back_order_list` SET status = 1 WHERE id = '{$formId}'");
				}

				if (!isset($backOrderId)) {
					$sql = "INSERT INTO `back_order_list` SET 
							bo_code = '{$backOrderCode}',	
							receiving_id = '{$receivingId}',	
							po_id = '{$purchaseOrderId}',	
							supplier_id = '{$supplierId}',	
							discount_perc = '{$discountPerc}',	
							tax_perc = '{$taxPerc}'
						";
				} else {
					$sql = "UPDATE `back_order_list` SET 
							receiving_id = '{$receivingId}',	
							po_id = '{$formId}',	
							supplier_id = '{$supplierId}',	
							discount_perc = '{$discounPerc}',	
							tax_perc = '{$taxPerc}',
							WHERE bo_id = '{$backOrderId}'
						";
				}

				$backOrderSave = $this->conn->query($sql);

				if (!isset($backOrderId)) {
					$backOrderId = $this->conn->insert_id;
				}
				
				$sTotal =0; 
				$data = "";

				foreach ($itemId as $key => $value) {
					if (!in_array($key,$backOrderIds)) {
						continue;
					}
						
					$total = ($oqty[$key] - $qty[$key]) * $price[$key];
					$sTotal += $total;

					if (!empty($data)) {
						$data.= ", ";
					} 

					$data .= " ('{$backOrderId}','{$value}','".($oqty[$key] - $qty[$key])."','{$price[$key]}','{$unit[$key]}','{$total}') ";

				}

				$this->conn->query("DELETE FROM `bo_items` 	WHERE bo_id='{$backOrderId}'");

				$saveBoItems = $this->conn->query("
					INSERT INTO 
						`bo_items` (`bo_id`,`item_id`,`quantity`,`price`,`unit`,`total`) 
					VALUES 
						{$data}");

				if ($save_bo_items) {
					$discount = $sTotal * ($discountPerc /100);
					$sTotal -= $discount;
					$tax = $sTotal * ($taxPerc /100);
					$sTotal += $tax;
					$amount = $sTotal;
					$this->conn->query("
						UPDATE 
							back_order_list 
						SET 
							amount = '{$amount}', discount='{$discount}', tax = '{$tax}' WHERE id = '{$backOrderId}'");
				}
			} else {
				$this->conn->query("UPDATE `purchase_order_list` SET status = 2 WHERE id = '{$purchaseOrderId}'");

				if ($fromOrder == 2) {
					$this->conn->query("UPDATE `back_order_list` SET status = 2 WHERE id = '{$formId}'");
				}
			}
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Ocorreu um erro. Error: '.$this->conn->error;
		}
		if ($resp['status'] == 'success') {
			if (empty($id)) {
				$this->settings->setFlashData('success'," Novo estoque recebido com sucesso.");
			}else{
				$this->settings->setFlashData('success'," Detalhes do estoque atualizado com sucesso");
			}
		}

		return json_encode($resp);
	}

	function deleteReceiving() 
	{
		extract($_POST);

		$query = $this->conn->query("SELECT * FROM  receiving_list WHERE id='{$id}' ");

		if ($query->num_rows > 0) {
			$res = $query->fetch_array();
			$ids = $res['stock_ids'];
		}

		if (isset($ids) && !empty($ids)) {
			$this->conn->query("DELETE FROM stock_list WHERE id IN ($ids) ");
		}
		
		$delete = $this->conn->query("DELETE FROM receiving_list WHERE id='{$id}' ");

		if ($delete) {
			$resp['status'] = 'success';
			$this->settings->setFlashData('success',"Detalhes do pedido excluido com sucesso.");

			if (isset($res)) {
				if ($res['from_order'] == 1) {
					$this->conn->query("UPDATE purchase_order_list SET status = 0 WHERE id = '{$res['form_id']}' ");
				}
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}

		return json_encode($resp);

	}

	function deleteBackOrder() 
	{
		extract($_POST);

		$backOrder = $this->conn->query("SELECT * FROM `back_order_list` WHERE id = '{$id}'");

		if ($backOrder->num_rows > 0) {
			$backOrderRes = $backOrder->fetch_array();
		}
		
		$delete = $this->conn->query("DELETE FROM `back_order_list` WHERE id = '{$id}'");

		if ($delete) {
			$resp['status'] = 'success';
			$this->settings->setFlashData('success',"Dados do pedido de compra excluido com sucesso.");

			$query = $this->conn->query("
				SELECT 
					`stock_ids` 
				FROM  
					receiving_list 
				WHERE 
					form_id ='{$id}' AND from_order = '2' ");

			if ($query->num_rows > 0) {
				$res = $query->fetch_array();
				$ids = $res['stock_ids'];
				$this->conn->query("DELETE FROM stock_list WHERE id in ($ids) ");
				$this->conn->query("DELETE FROM receiving_list WHERE form_id='{$id}' AND from_order = '2' ");
			}

			if (isset($backOrderRes)) {
				$check = $this->conn->query("
					SELECT 
						* 
					FROM 
						`receiving_list` 
					WHERE 
						from_order = 1 AND form_id = '{$backOrderRes['po_id']}' ");

				if ($check->num_rows > 0) {
					$this->conn->query("UPDATE `purchase_order_list` SET status = 1 WHERE id = '{$backOrderRes['po_id']}' ");
				}else{
					$this->conn->query("UPDATE `purchase_order_list` SET status = 0 WHERE id = '{$backOrderRes['po_id']}' ");
				}
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}

		return json_encode($resp);
	}

	function saveReturn() 
	{
		if (empty($_POST['id'])) {
			$prefix = "R";
			$code = sprintf("%'.04d",1);

			while (true) {
				$checkCode = $this->conn->query("
					SELECT 
						* 
					FROM 
						`return_list` 
					WHERE 
						return_code ='".$prefix.'-'.$code."' ")->num_rows;

				if ($checkCode > 0) {
					$code = sprintf("%'.04d",$code + 1);
				} else {
					break;
				}
			}

			$_POST['return_code'] = $prefix."-".$code;
		}

		extract($_POST);

		$data = "";

		foreach ($_POST as $key => $value) {
			if (!in_array($key, ['id']) && !is_array($_POST[$key])) {
				if (!is_numeric($value)) {
					$value = $this->conn->real_escape_string($value);
				}
				
				if (!empty($data)) {
					$data .=", ";
				} 

				$data .=" `{$key}` = '{$value}' ";
			}
		}

		if (empty($id)) {
			$sql = "INSERT INTO `return_list` SET {$data}";
		} else {
			$sql = "UPDATE `return_list` set {$data} WHERE id = '{$id}'";
		}

		$save = $this->conn->query($sql);

		if ($save) {
			$resp['status'] = 'success';

			if (empty($id)) {
				$return_id = $this->conn->insert_id;
			} else {
				$return_id = $id;
			}

			$resp['id'] = $returnId;
			$data = "";
			$sIds = [];

			$get = $this->conn->query("SELECT * FROM `return_list` WHERE id = '{$returnId}'");

			if ($get->num_rows > 0) {
				$res = $get->fetch_array();

				if (!empty($res['stock_ids'])) {
					$this->conn->query("DELETE FROM `stock_list` WHERE id IN ({$res['stock_ids']}) ");
				}
			}

			foreach ($itemId as $key =>$value) {
				$sql = "INSERT INTO `stock_list` SET item_id='{$value}', `quantity` = '{$qty[$key]}', `unit` = '{$unit[$key]}', `price` = '{$price[$key]}', `total` = '{$total[$key]}', `type` = 2 ";
				$save = $this->conn->query($sql);

				if ($save) {
					$sIds[] = $this->conn->insert_id;
				}
			}

			$sIds = implode(',',$sIds);
			$this->conn->query("UPDATE `return_list` SET stock_ids = '{$sIds}' WHERE id = '{$returnId}'");
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Ocorreu um erro. Error: '.$this->conn->error;
		}

		if ($resp['status'] == 'success') {
			if (empty($id)) {
				$this->settings->setFlashData('success'," Novo registro de item devolvido excluido com sucesso.");
			}else{
				$this->settings->setFlashData('success'," Registro de item devolvido atualizado com sucesso.");
			}
		}

		return json_encode($resp);
	}

	function deleteReturn() 
	{
		extract($_POST);

		$get = $this->conn->query("SELECT * FROM return_list WHERE id = '{$id}'");

		if ($get->num_rows > 0) {
			$res = $get->fetch_array();
		}

		$delete = $this->conn->query("DELETE FROM `return_list` WHERE id = '{$id}'");

		if ($delete) {
			$resp['status'] = 'success';
			$this->settings->setFlashData('success',"Registros de itens devolvido excluídos com sucesso.");

			if (isset($res)) {
				$this->conn->query("DELETE FROM `stock_list` WHERE id IN ({$res['stock_ids']})");
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}

		return json_encode($resp);

	}

	function savesale() {
		if (empty($_POST['id'])) {
			$prefix = "SALE";
			$code = sprintf("%'.04d",1);

			while (true) {
				$checkCode = $this->conn->query("
					SELECT 
						* 
					FROM 
						`sales_list` 
					WHERE 
						sales_code ='".$prefix.'-'.$code."' ")->num_rows;

				if ($checkCode > 0) {
					$code = sprintf("%'.04d",$code+1);
				} else {
					break;
				}
			}

			$_POST['sales_code'] = $prefix."-".$code;
		}

		extract($_POST);

		$data = "";

		foreach ($_POST as $key =>$value) {
			if (!in_array($key, ['id']) && !is_array($_POST[$key])) {
				if (!is_numeric($value)) {
					$value = $this->conn->real_escape_string($value);
				}
				
				if (!empty($data)) {
					$data .=", ";
				} 

				$data .=" `{$key}` = '{$value}' ";
			}
		}

		if (empty($id)) {
			$sql = "INSERT INTO `sales_list` SET {$data}";
		} else {
			$sql = "UPDATE `sales_list` SET {$data} WHERE id = '{$id}'";
		}

		$save = $this->conn->query($sql);

		if ($save) {
			$resp['status'] = 'success';

			if (empty($id)) {
				$saleId = $this->conn->insert_id;
			} else {
				$saleId = $id;
			}
			
			$resp['id'] = $saleId;
			$data = "";
			$sIds = [];
			$get = $this->conn->query("SELECT * FROM `sales_list` WHERE id = '{$saleId}'");

			if ($get->num_rows > 0) {
				$res = $get->fetch_array();

				if (!empty($res['stock_ids'])) {
					$this->conn->query("DELETE FROM `stock_list` WHERE id IN ({$res['stock_ids']}) ");
				}
			}

			foreach ($itemId as $key =>$value) {
				$sql = "INSERT INTO `stock_list` SET item_id='{$value}', `quantity` = '{$qty[$key]}', `unit` = '{$unit[$key]}', `price` = '{$price[$key]}', `total` = '{$total[$key]}', `type` = 2 ";
				$save = $this->conn->query($sql);

				if ($save) {
					$sIds[] = $this->conn->insert_id;
				}
			}

			$sIds = implode(',',$sIds);
			$this->conn->query("UPDATE `sales_list` SET stock_ids = '{$sIds}' WHERE id = '{$saleId}'");

		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Ocorreu um erro. Error: '.$this->conn->error;
		}

		if ($resp['status'] == 'success') {
			if (empty($id)) {
				$this->settings->setFlashData('success'," Novo registro de vendas foi criado com sucesso.");
			} else {
				$this->settings->setFlashData('success'," Registros de vendas atualizado com sucesso.");
			}
		}

		return json_encode($resp);
	}

	function deleteSale() 
	{
		extract($_POST);

		$get = $this->conn->query("SELECT * FROM sales_list WHERE id = '{$id}'");

		if ($get->num_rows > 0) {
			$res = $get->fetch_array();
		}

		$delete = $this->conn->query("DELETE FROM `sales_list` WHERE id = '{$id}'");

		if ($delete) {
			$resp['status'] = 'success';
			$this->settings->setFlashData('success',"Registro de vendas excluido com sucesso.");

			if (isset($res)) {
				$this->conn->query("DELETE FROM `stock_list` WHERE id IN ({$res['stock_ids']})");
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}

		return json_encode($resp);

	}
}

$master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();

switch ($action) {
	case 'save_supplier':
		echo $master->saveSupplier();
	break;
	case 'delete_supplier':
		echo $master->deleteSupplier();
	break;
	case 'save_item':
		echo $master->saveItem();
	break;
	case 'delete_item':
		echo $master->deleteItem();
	break;
	// case 'get_item':
	// 	echo $master->get_item();
	// break;
	case 'save_po':
		echo $master->savePurchaseOrder();
	break;
	case 'delete_po':
		echo $master->deletePurchaseOrder();
	break;
	case 'save_receiving':
		echo $master->saveReceiving();
	break;
	case 'delete_receiving':
		echo $master->deleteReceiving();
	break;
	case 'save_return':
		echo $master->saveReturn();
	break;
	case 'delete_return':
		echo $master->deleteReturn();
	break;
	case 'save_sale':
		echo $master->saveSale();
	break;
	case 'delete_sale':
		echo $master->deleteSale();
	break;
	default:
		break;
}