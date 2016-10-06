<?php
class ControllerCheckoutLitemf extends Controller
{
	public function getCost()
	{
		$json = array();
		$this->load->model('setting/setting');
		$apiKey = $this->config->get('litemf_api_key');;
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

	public function getDeliveryPoints()
	{
		$points = [];
		$this->load->model('setting/setting');
		$apiKey = $this->config->get('litemf_api_key');
		$kladrResponse = $this->getKladr($this->request->get['city']);
		$kladr = null;
		foreach($kladrResponse->suggestions as $suggestion) {
			if ($suggestion->data->city_kladr_id == $suggestion->data->kladr_id) {
				$kladr = $suggestion->data->kladr_id;
				break;
			}
		}
		$data = '{
				"id":"56531b1f08ba7",
				"method":"getDeliveryPointList",
				"limit":"9999",
				"params":{
					"filter":{
						"kladr":"'.substr($kladr, 0, 11).'"
					}
				}
			}';
		$json = $this->sendRequest($data, $apiKey);
		$jsonArray = json_decode($json);
		foreach ($jsonArray->result->data as $point) {
			$data = '{
				"id":"56f1089cc9541",
				"method":"getDeliveryPrice",
				"params":{
					"country_from":373,
					"country_to":3159,
					"weight":'.$this->cart->getWeight().',
					"zone":"'.substr($kladr, 0, 11).'",
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

	public function getPassportInfoById()
	{
		$this->load->model('setting/setting');
		$this->load->model('account/litemf');
		$address_info = $this->model_account_litemf->getAddress($this->customer->getId());
		$data['status'] = true;
		$data['passport'] = $address_info;
		if (!$address_info) {
			$data['status'] = false;
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
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
		$methods = json_decode($methods);
		$methods->logged = $this->customer->isLogged();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($methods));
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
}
