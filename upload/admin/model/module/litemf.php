<?php

class ModelModuleLitemf extends Model
{
	public function getOrderStatusList() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status;");

		return $query->rows;
	}

	public function updateOrder($orderId, $orderStatusId) {
		$this->db->query("UPDATE  " . DB_PREFIX . "order SET order_status_id = '" . (int)$orderStatusId . "' WHERE order_id = '".$orderId."'");
	}

	public function sendRequest($data, $apiKey)
	{
		$ch = curl_init('https://api.litemf.com/v2/rpc');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'X-Auth-Api-Key: ' . $apiKey,
				'Content-Length: ' . strlen($data))
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS,   $data );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');

		$result = curl_exec($ch);

		return json_decode($result);
	}

	public function createIncomingPackages($orderId)
	{
		$package = $this->getLitemfPackageById($orderId);
		$opencartOrder = $this->getOpencartOrder($package['order_id']);
		$products =$this->getOrderProducts($package['order_id']);
		$data_string = [];
		foreach ($products as $product) {
			$cost = $this->currency->convert($product['total'], $opencartOrder['currency_code'], 'EUR');
			$data_string[] = '{
				"jsonrpc": "2.0",
				"id": 10,
				"method":"createIncomingPackage",
				"params":{
					"data":{
						"shop_name":"'.$opencartOrder['store_name'].'",
						"warehouse":4,
						"price":'.number_format($cost, 2, '.', '').',
						"name":"'.$product['name'].'",
						"partner_fid":"'.$product['order_product_id'].'&'.$opencartOrder['invoice_prefix'].'",
						"partner_url":"'.$opencartOrder['store_url'].'",
						"tracking":"'.$package['tracking'].'",
						"is_make_additional_photo":"n"
					}
				}
			}';
		}
		return $data_string;
	}

	public function createOutgoingPackage($orderId)
	{
		$package = $this->getLitemfPackageById($orderId);
		$opencartOrder = $this->getOpencartOrder($package['order_id']);
		$products =$this->getOrderProducts($package['order_id']);
		$point = is_null($package['delivery_point_id']) || $package['delivery_point_id'] == 0 ? '' : '"delivery_point":'.(int)$package['delivery_point_id'].',';
		$declaration = '';
		$numItems = count($products);
		$i = 0;
		foreach ($products as $product) {
			$cost = $this->currency->convert($product['total'], $opencartOrder['currency_code'], 'EUR');
			$declaration .= '{
				"description":"'.$product['name'].'",
								"quantity":'.$product['quantity'].',
								"value":'.number_format($cost, 2, '.', '').'
							}';
			if(++$i != $numItems) {
				$declaration .= ',';
			}
		}
		$data_string = '{
			"jsonrpc": "2.0",
			"id": 11,
			"method":"createOutgoingPackage",
			"params":{
				"data":{
					"incoming_packages":['. $package['incoming_packages'] .'],
					"delivery_method":'.$package['delivery_method_id'].',
					'.$point.'
					"name":"'.$opencartOrder['store_name'].'",
					"partner_fid":"'.$opencartOrder['order_id'].'&'.$opencartOrder['invoice_prefix'].'",
					"partner_url":"'.$opencartOrder['store_url'].'",
					"comment":"'.$opencartOrder['comment'].'",
					"address":'.$package['address_id'].',
					"sender":"'.$opencartOrder['store_name'].'",
					"declarations":[
						'.$declaration.'
					]
				}

			}
		}';

		return $data_string;
	}

	public function getPackages()
	{
		$data_string = '{
			"jsonrpc": "2.0",
			"id": "55df2998dfe7c",
			"method":"getIncomingPackage",
			"params":{
				"filter":{}
			}

		}';

		return $data_string;
	}

	public function createAddress($orderId)
	{
		$data = $this->getLitemfPackageById($orderId);
		$data['courier'] = '';
		$dataCourier = $this->getLitemfCourierById($orderId);
		if(isset($dataCourier)) {
			$data['street'] = $dataCourier['street'];
			$data['house'] = $dataCourier['house'];
			$data['phone'] = $dataCourier['phone'];
			$data['courier'] = ' "flat":"'.$dataCourier['number'].'", "email":"'.$dataCourier['email'].'", ';
		}
		$phone = $str = preg_replace("/[^0-9]/", '', $data['phone']);
		$date = new \DateTime($data['issue_date']);
		$data_string = '{
			"id":"55deae2ec2f67",
			"method":"createAddress",
			"params":{
				"data":{
					"format":"separated",
					"name":{
						"last_name":"'.$data['last_name'].'",
						"first_name":"'.$data['first_name'].'",
						"middle_name":"'.$data['middle_name'].'"
					},
					"delivery_country":3159,
					'.$data['courier'].'
					"first_line":{
						"street":"'.$data['street'].'",
						"house":"'.$data['house'].'"
					},
					"city":"'.$data['city'].'",
					"region":"'.$data['region'].'",
					"zip_code":"'.$data['zip_code'].'",
					"phone":{
						"country":"'.substr($phone, 0, 1).'",
						"code":"'.substr($phone, 1, 3).'",
						"number":"'.substr($phone, 4, 10).'"
					},
					"passport":{
						"series":"'.$data['series'].'",
						"number":"'.$data['number'].'",
						"issue_date":"'.$date->format('Y-m-d').'",
						"issued_by":"'.$data['issued_by'].'"
					}
				}
			}
		}';

		return $data_string;
	}

	public function checkPackage()
	{
		$this->load->language('module/litemf-api');
		$this->load->model('setting/setting');
		$this->load->model('module/litemf');
		$this->load->model('sale/order');
		$settings = $this->model_setting_setting->getSetting('config');

		$file = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp');
		$xmlCurrency = simplexml_load_string($file);
		foreach ($xmlCurrency as $item) {
			if ($item->CharCode == 'USD') {
				$currency = (int) $item->Value;
			}
		}

		$apiKey = $this->config->get('litemf_api_key');
		$data['filter_order_status'] = $this->config->get('litemf_get_order');
		$orders = $this->model_sale_order->getOrders($data);
		foreach ($orders as $o) {
			$order = $this->model_sale_order->getOrder($o['order_id']);
			$package = $this->createPackage($order, $currency, $settings['config_invoice_prefix']);
			$address = $this->createAddress($order);
			$request = $this->sendRequest($package, $apiKey);
			$request = $this->sendRequest($address, $apiKey);

			$this->model_module_litemf->updateOrder($order['order_id'],  $this->config->get('litemf_set_order'));
		}
		$importPackage = $this->sendRequest($this->getPackages(), $apiKey);
		$importPackage = json_decode($importPackage);
		foreach ($importPackage->result->data as $package) {
			$arr = explode('&', $package->partner_fid);
			if ($package->status == 'recived' && isset($arr[1]) && $arr[1] == $settings['config_invoice_prefix']) {
				$this->model_module_litemf->updateOrder($arr[0],  $this->config->get('litemf_set_delivery_order'));
			}
		}
	}

	public function getLitemfPackage()
	{
		$this->db->query("DELETE l FROM " . DB_PREFIX . "litemf_orders AS l INNER JOIN " . DB_PREFIX . "order AS o ON ( l.order_id = o.order_id ) WHERE o.order_status_id =0");
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "litemf_address AS la LEFT JOIN  " . DB_PREFIX . "litemf_orders AS lo ON ( la.litemf_orders = lo.id ) WHERE lo.status =  'unsend'");

		return $query->rows;
	}

	public function getLitemfPackageSend()
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "litemf_address AS la LEFT JOIN  " . DB_PREFIX . "litemf_orders AS lo ON ( la.litemf_orders = lo.id ) WHERE lo.status =  'send'");

		return $query->rows;
	}

	public function getLitemfPackageById($id)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "litemf_address AS la LEFT JOIN  " . DB_PREFIX . "litemf_orders AS lo ON ( la.litemf_orders = lo.id ) WHERE lo.status =  'unsend' AND la.litemf_orders= '" . $id . "'");

		return $query->row;
	}

	public function getLitemfCourierById($id)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "litemf_courier_address AS la LEFT JOIN  " . DB_PREFIX . "litemf_orders AS lo ON ( la.litemf_orders = lo.id ) WHERE lo.status =  'unsend' AND la.litemf_orders= '" . $id . "'");

		return $query->row;
	}

	public function updateLitemfPackage($data)
	{
		$this->db->query("UPDATE " . DB_PREFIX . "litemf_orders SET `tracking`='" . $data['tracking'] . "' WHERE id='" . (int)$data['order_id'] . "'");
		$this->db->query("UPDATE " . DB_PREFIX . "litemf_address SET first_name='" . $data['first_name'] . "', last_name='" . $data['last_name'] . "', middle_name='" . $data['middle_name'] . "', street='" . $data['street'] . "', house='" . $data['house'] . "', city='" . $data['city'] . "', region='" . $data['region'] . "', zip_code='" . $data['zip_code'] . "', phone='" . $data['phone'] . "', series='" . $data['series'] . "', number='" . $data['number'] . "', issue_date='" . $data['issue_date'] . "', issued_by='" . $data['issued_by'] . "' WHERE litemf_orders='" . $data['order_id'] . "';");
	}

	public function getOpencartOrder($orderId) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id ='" . $orderId . "';");

		return $query->row;
	}

	public function getOrderProducts($orderId) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id ='" . $orderId . "';");

		return $query->rows;
	}

	public function updateLitemfOrder($order_id, $address_id, $incomingPackage)
	{
		$this->db->query("UPDATE " . DB_PREFIX . "litemf_orders SET `address_id`='" . $address_id . "', `incoming_packages`='" . $incomingPackage . "' WHERE id='" . (int)$order_id . "'");
	}

	public function updateLitemfOrderStatus($order_id)
	{
		$this->db->query("UPDATE " . DB_PREFIX . "litemf_orders SET `status`='send' WHERE id='" . (int)$order_id . "'");
	}
}
