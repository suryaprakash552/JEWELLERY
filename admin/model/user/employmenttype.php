<?php
namespace Opencart\Admin\Model\User;
/**
 * Class Language
 *
 * Can be loaded using $this->load->model('localisation/language');
 *
 * @package Opencart\Admin\Model\Localisation
 */
class Employmenttype extends \Opencart\System\Engine\Model {
	/**
	 * Add Language
	 *
	 * Create a new language record in the database.
	 *
	 * @param array<string, mixed> $data array of data
	 *
	 * @return int returns the primary key of the new language record
	 *
	 * @example
	 *
	 * $language_data = [
	 *     'name'       => 'Language Name',
	 *     'code'       => 'Language Code',
	 *     'locale'     => 'Language Locale',
	 *     'extension'  => '',
	 *     'sort_order' => 0,
	 *     'status'     => 0
	 * ];
	 *
	 * $this->load->model('localisation/language');
	 *
	 * $language_id = $this->model_localisation_language->addLanguage($language_data);
	 */
	public function addEmploymenttype(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "employmenttype` SET `name` = '" . $this->db->escape((string)$data['name']) . "'");

		$this->cache->delete('employmenttype');

		$employmenttype_id = $this->db->getLastId();

		return $employmenttype_id;
	}

	/**
	 * Edit Language
	 *
	 * Edit language record in the database.
	 *
	 * @param int                  $language_id primary key of the language record
	 * @param array<string, mixed> $data        array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $language_data = [
	 *     'name'       => 'Language Name',
	 *     'code'       => 'Language Code',
	 *     'locale'     => 'Language Locale',
	 *     'extension'  => '',
	 *     'sort_order' => 0,
	 *     'status'     => 1
	 * ];
	 *
	 * $this->load->model('localisation/language');
	 *
	 * $this->model_localisation_language->editLanguage($language_id, $language_data);
	 */
	public function editEmploymenttype(int $employmenttype_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "employmenttype` SET `name` = '" . $this->db->escape((string)$data['name']) . "' WHERE `employmenttype_id` = '" . (int)$employmenttype_id . "'");

		$this->cache->delete('employmenttype');
	}

	/**
	 * Delete Language
	 *
	 * Delete language record in the database.
	 *
	 * @param int $language_id primary key of the language record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('localisation/language');
	 *
	 * $this->model_localisation_language->deleteLanguage($language_id);
	 */
	public function deleteEmploymenttype(int $employmenttype_id): void {

		$this->db->query("DELETE FROM `" . DB_PREFIX . "employmenttype` WHERE `employmenttype_id` = '" . (int)$employmenttype_id . "'");

		$this->cache->delete('employmenttype');

	}

	/**
	 * Get Language
	 *
	 * Get the record of the language record in the database.
	 *
	 * @param int $language_id primary key of the language record
	 *
	 * @return array<string, mixed> language record that has language ID
	 *
	 * @example
	 *
	 * $this->load->model('localisation/language');
	 *
	 * $language_info = $this->model_localisation_language->getLanguage($language_id);
	 */
	public function getEmploymenttypeById(int $employmenttype_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "employmenttype` WHERE `employmenttype_id` = '" . (int)$employmenttype_id . "'");

		$employmenttype = $query->row;

		return $employmenttype;
	}

	/**
	 * Get Language By Code
	 *
	 * @param string $code
	 *
	 * @return array<string, mixed>
	 *
	 * @example
	 *
	 * $this->load->model('localisation/language');
	 *
	 * $language_info = $this->model_localisation_language->getLanguageByCode($code);
	 */
	public function getEmploymenttypeByCode(string $code): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "employmenttype` WHERE `code` = '" . $this->db->escape($code) . "'");

		$employmenttype = $query->row;

		if ($employmenttype) {
			$employmenttype['image'] = HTTP_CATALOG;

			if (!$employmenttype['extension']) {
				$employmenttype['image'] .= 'catalog/';
			} else {
				$employmenttype['image'] .= 'extension/' . $employmenttype['extension'] . '/catalog/';
			}

			$employmenttype['image'] .= 'language/' . $employmenttype['code'] . '/' . $employmenttype['code'] . '.png';
		}

		return $employmenttype;
	}
		public function getEmploymenttypeByName(string $name): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "employmenttype` WHERE `name` = '" . $this->db->escape($name) . "'");

		$employmenttype = $query->row;
        
		return $employmenttype;
	}

	/**
	 * Get Languages
	 *
	 * Get the record of the language records in the database.
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return array<string, array<string, mixed>> language records
	 *
	 * @example
	 *
	 * $filter_data = [
	 *     'sort'  => 'name',
	 *     'order' => 'DESC',
	 *     'start' => 0,
	 *     'limit' => 10
	 * ];
	 *
	 * $this->load->model('localisation/language');
	 *
	 * $languages = $this->model_localisation_language->getLanguages($filter_data);
	 */
	public function getEmploymenttypes(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "employmenttype`";

		$sort_data = [
			'name',
			'sort_order'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `sort_order`, `name`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$results = $this->cache->get('language.' . md5($sql));

		if (!$results) {
			$query = $this->db->query($sql);

			$results = $query->rows;

			$this->cache->set('employmenttype.' . md5($sql), $results);
		}

		$employmenttype_data = [];

		foreach ($results as $result) {

			$employmenttype_data[] = $result;
		}

		return $employmenttype_data;
	}

	/**
	 * Get Languages By Extension
	 *
	 * @param string $extension
	 *
	 * @return array<int, array<string, mixed>>
	 *
	 * @example
	 *
	 * $this->load->model('localisation/language');
	 *
	 * $results = $this->model_localisation_language->getLanguagesByExtension($extension);
	 */
	public function getEmploymenttypesByExtension(string $extension): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "employmenttype` WHERE `extension` = '" . $this->db->escape($extension) . "'");

		return $query->rows;
	}

	/**
	 * Get Total Languages
	 *
	 * Get the total number of language records in the database.
	 *
	 * @return int total number of language records
	 *
	 * @example
	 *
	 * $this->load->model('localisation/language');
	 *
	 * $language_total = $this->model_localisation_language->getTotalLanguages();
	 */
	public function getTotalEmploymenttypes(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "employmenttype`");
		return (int)$query->row['total'];
	}
}
