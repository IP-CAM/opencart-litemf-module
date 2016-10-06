<?php
class ModelShippingLitemfDelivery extends Model
{
	function getQuote($address)
	{
		$this->load->language('shipping/litemf_delivery');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('pickup_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('pickup_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$quote_data = array();
			if (!empty($this->session->data['shipping_address']['city'])) {
				$kladrResponse = $this->getKladr($this->session->data['shipping_address']['city']);
				try {
					$kladr = '';
					foreach ($kladrResponse->suggestions as $suggestion) {
						if ($suggestion->data->city_kladr_id == $suggestion->data->kladr_id) {
							$kladr = $suggestion->data->kladr_id;
							break;
						}
					}
					$weight = $this->cart->getWeight();
					$methods = $this->getDeliveryMethod();
					foreach ($methods->result->data as $method) {
						$cost = '';
						if (!empty($kladr)) {
							$cost = $this->getCost($weight, $kladr, $method->id);
							$cost = $this->currency->convert($cost, 'EUR', $this->config->get('config_currency'));
						}
						if ($method->name == 'Express') {
							$title = 'Доставка курьером';
						} else {
							$title = 'Доставка в пункт выдачи';
						}
						if ((string)$cost != '') {
							$quote_data['litemf_delivery_' . $method->id] = array(
								'code' => 'litemf_delivery.litemf_delivery_' . $method->id,
								'title' => $title,
								'cost' => $cost,
								'tax_class_id' => $this->config->get('shiptor_delivery_tax_class_id'),
								'text' => $this->currency->format($this->tax->calculate($cost, $this->config->get('shiptor_delivery_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
							);
						}
					}
				} catch (Exception $e) {}

				$method_data = array(
					'code' => 'litemf_delivery',
					'title' => $this->language->get('text_title'),
					'quote' => $quote_data,
					'sort_order' => $this->config->get('litemf_delivery_sort_order'),
					'error' => false
				);
			}
		}

		return $method_data;
	}

	public function addOrderLitemf($data)
	{
		$date = date_create($data['passport']['issue_date']);
		$date = date_format($date, 'Y-m-d H:i:s');
		$pointId = $data['passport']['delivery_point_id'] == 'courier' ? null : $data['passport']['delivery_point_id'];
		$this->db->query("INSERT INTO `" . DB_PREFIX . "litemf_address` SET first_name = '" . $data['passport']['first_name'] . "', last_name = '" . $data['passport']['last_name'] . "', middle_name = '" . $data['passport']['middle_name'] . "', street = '" . $data['passport']['street'] . "', house = '" . $data['passport']['house'] . "', city = '" . $data['passport']['city'] . "', region = '" . $data['passport']['region'] . "', zip_code = '" . $data['passport']['zip_code'] . "', phone = '" . $data['passport']['phone'] . "', series = '" . $data['passport']['series'] . "', number = '" . $data['passport']['number'] . "', issue_date = '" . $date . "', issued_by = '" . $data['passport']['issued_by'] . "'");
		$litemf_address_id = $this->db->getLastId();
		$this->db->query("INSERT INTO `" . DB_PREFIX . "litemf_orders` SET litemf_address_id = '" . (int)$litemf_address_id . "', status = 'unsend', user_id = '" . (int)$data['user_id'] . "', order_id = '" . $this->db->escape($data['order_id']) . "', delivery_method_id = '" . $data['passport']['delivery_method_id'] . "', delivery_point_id = '" . $pointId . "'");
		$litemf_order_id = $this->db->getLastId();
		if (isset($data['courier']) && is_null($pointId)) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "litemf_courier_address` SET litemf_orders = '" . (int)$litemf_order_id . "', street = '" . $data['courier']['street'] . "', house = '" . $data['courier']['house'] . "', email = '" . $data['courier']['email'] . "', phone = '" . $data['courier']['phone'] . "', number = '" . $data['courier']['number'] . "'");
		}
	}

	protected function sendRequest($data, $apiKey)
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

		return $result;
	}

	public function getCost($weight, $kladr, $deliveryMethodId)
	{
		$this->load->model('setting/setting');
		$apiKey = $this->config->get('litemf_api_key');
		$data = '{
			"id":"56f1089cc9541",
			"method":"getDeliveryPrice",
			"params":{
				"country_from":373,
				"country_to":3159,
				"weight":'.$weight.',
				"zone":"'.substr($kladr, 0, 11).'",
				"filter":{
					"delivery_method":['.$deliveryMethodId.']
				}
			}
		}';
		$json = $this->sendRequest($data, $apiKey);
		$jsonCostArray = json_decode($json);

		return $jsonCostArray->result->data[0]->price;
	}

	public function getDeliveryMethod()
	{
		$this->load->model('setting/setting');
		$apiKey = $this->config->get('litemf_api_key');
		$data = '{
				"id":"55ddc54443838",
				"method":"getDeliveryMethod",
				"params":{
					"country_from":373,
					"country_to":3159
				}
			}';
		$methods = $this->sendRequest($data, $apiKey);

		return json_decode($methods);
	}

	/**
	 * @param mixed $city
	 * @return mixed|null
	 */
	public function getKladr($city)
	{
		$url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';
		$token = 'a5fdcbf8e1ea0b34803ee92b4f433344915cab0c';
		$data = array(
			"query" => $city
		);
		$options = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => array(
					'Content-type: application/json',
					'Authorization: Token ' . $token
				),
				'content' => json_encode($data),
			),
		);
		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		return json_decode($result);
	}
}