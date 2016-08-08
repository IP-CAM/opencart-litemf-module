<?php
class ControllerCheckoutLitemf extends Controller {
	public function getCost() {
		$json = array();
		$this->load->model('setting/setting');
		$apiKey = 'e2f1a1ec2c5c51867d757879ad1f8789cb20c223';
		if (isset($this->request->get['kladr'])) {
			$data = '{
				"id":"56f1089cc9541",
				"method":"getDeliveryPrice",
				"params":{
					"country_from":373,
					"country_to":3159,
					"weight":1500,
					"zone":"'.substr($this->request->get['kladr'], 0, 11).'",
					"filter":{}
				}
			}';
			$json = $this->sendRequest($data, $apiKey);
		}
		$jsonCostArray = json_decode($json);
		if(!empty($jsonCostArray->result->data)) {
			$cost = $this->currency->convert($jsonCostArray->result->data[0]->price, 'EUR', $this->config->get('config_currency'));
			$jsonCostArray->price = $this->currency->format($cost, $this->config->get('config_currency'));
			$jsonCostArray->cost = $cost;
			$jsonCostArray->status = true;
		} else {
			$jsonCostArray->status = false;
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($jsonCostArray));
	}

	public function getPoint() {
		$points = [];
		$json = array();
		$this->load->model('setting/setting');
		$apiKey = 'e2f1a1ec2c5c51867d757879ad1f8789cb20c223';
		if (isset($this->request->get['kladr'])) {
			$data = '{
					"id":"56531b1f08ba7",
					"method":"getDeliveryPointList",
					"limit":"9999",
					"params":{
						"filter":{
							"kladr":"'.substr($this->request->get['kladr'], 0, 11).'"
						}
					}
				}';
			$json = $this->sendRequest($data, $apiKey);
		}
		$jsonArray = json_decode($json);
		foreach ($jsonArray->result->data as $point) {
			$data = '{
			"id":"56f1089cc9541",
			"method":"getDeliveryPrice",
			"params":{
				"country_from":373,
				"country_to":3159,
				"weight":1500,
				"zone":"'.substr($this->request->get['kladr'], 0, 11).'",
				"delivery_point":"'.$point->id.'",
				"filter":{
				}
			}
		}';
			$jsonCost = $this->sendRequest($data, $apiKey);
			$jsonCostArray = json_decode($jsonCost);
			$cost = $this->currency->convert(
				$jsonCostArray->result->data[0]->price,
				'EUR',
				$this->config->get('config_currency')
			);
			$point->price = $this->currency->format($cost, $this->config->get('config_currency'));
			$point->cost = $cost;
			$point->config_currency = $this->config->get('config_currency');
			$points[] = $point;
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($points));
	}

	public function getDeliveryMethod() {
		$this->load->model('setting/setting');
		$apiKey = 'e2f1a1ec2c5c51867d757879ad1f8789cb20c223';
		$data = '{
				"id":"55ddc54443838",
				"method":"getDeliveryMethod",
				"params":{
					"country_from":373,
					"country_to":3159
				}
			}';
		$methods = $this->sendRequest($data, $apiKey);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput($methods);
	}

	public function getKladr($state, $city) {
		$url = 'https://dadata.ru/api/v2/clean';
		$token = 'a5fdcbf8e1ea0b34803ee92b4f433344915cab0c';
		$secret = '712ccc777043d8fdbead3afbdf4bb2be79602fec';
		$data = array(
			"structure" => array("ADDRESS"),
			"data" => array(array("Воронежская область, Воронеж"))
		);

		$options = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => array(
					'Content-type: application/json',
					'Authorization: Token ' . $token,
					'X-Secret: ' . $secret
				),
				'content' => json_encode($data),
			),
		);
		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		return $result;
	}


	protected function sendRequest($data, $apiKey) {
		$ch = curl_init('http://api.dev.litemf.com/v2/rpc');
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

	protected function createAddressJson($data) {
		$data_string = '{
			"id":"55deae2ec2f67",
			"method":"createAddress",
			"params":{
				"data":{
					"format":"separated",
					"name":{
						"last_name":"Сидоров",
						"first_name":"Петр",
						"middle_name":"Иванович"
					},
					"delivery_country":3159,
					"first_line":{
						"street":"академика Королева",
						"house":"12"
					},
					"city":"Москва",
					"region":"Московская область",
					"zip_code":"127427",
					"phone":{
						"country":"7",
						"code":"800",
						"number":"4444500"
					},
					"passport":{
						"series":"0913",
						"number":"8683591",
						"issue_date":"2013-10-14",
						"issued_by":"Отделом УФМС России по московской обл."
					}
				}
			}
		}';

		return $data_string;
	}
}
