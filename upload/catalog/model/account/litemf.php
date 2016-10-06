<?php
class ModelAccountLitemf extends Model {
	public function addAddress($user_id, $data) {
        $date = date_create($data['issue_date']);
        $date = date_format($date, 'Y-m-d H:i:s');
		$this->db->query("DELETE FROM " . DB_PREFIX . "litemf_address WHERE user_id = '" . (int)$user_id . "'");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "litemf_address` SET user_id = '" . (int)$user_id . "', first_name = '" . $data['first_name'] . "', last_name = '" . $data['last_name'] . "', middle_name = '" . $data['middle_name'] . "', street = '" . $data['street'] . "', house = '" . $data['house'] . "', city = '" . $data['city'] . "', region = '" . $data['region'] . "', zip_code = '" . $data['zip_code'] . "', phone = '" . $data['phone'] . "', series = '" . $data['series'] . "', number = '" . $data['number'] . "', issue_date = '" . $date . "', issued_by = '" . $data['issued_by'] . "'");

		$address_id = $this->db->getLastId();

		return $address_id;
	}

	public function getAddress($user_id) {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "litemf_address WHERE user_id = '" . (int)$user_id . "'");

		if ($address_query->num_rows) {
			return $address_query->rows;
		} else {
			return false;
		}
	}
}