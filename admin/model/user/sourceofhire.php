<?php
namespace Opencart\Admin\Model\User;
/**
 * Class Language
 *
 * Can be loaded using $this->load->model('localisation/language');
 *
 * @package Opencart\Admin\Model\Localisation
 */
class Sourceofhire extends \Opencart\System\Engine\Model {
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
	public function addSourceofhire(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sourceofhire` SET `name` = '" . $this->db->escape((string)$data['name']) . "'");

		$this->cache->delete('sourceofhire');

		$sourceofhire_id = $this->db->getLastId();

		return $sourceofhire_id;
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
	public function editSourceofhire(int $sourceofhire_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "sourceofhire` SET `name` = '" . $this->db->escape((string)$data['name']) . "' WHERE `sourceofhire_id` = '" . (int)$sourceofhire_id . "'");

		$this->cache->delete('sourceofhire');
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
	public function deleteSourceofhire(int $sourceofhire_id): void {

		$this->db->query("DELETE FROM `" . DB_PREFIX . "sourceofhire` WHERE `sourceofhire_id` = '" . (int)$sourceofhire_id . "'");

		$this->cache->delete('sourceofhire');

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
	public function getSourceofhireById(int $sourceofhire_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "sourceofhire` WHERE `sourceofhire_id` = '" . (int)$sourceofhire_id . "'");

		$sourceofhire = $query->row;

		return $sourceofhire;
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
	/*public function getSourceofhireByCode(string $code): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sourceofhire` WHERE `code` = '" . $this->db->escape($code) . "'");

		$sourceofhire = $query->row;

		if ($sourceofhire) {
			$sourceofhire['image'] = HTTP_CATALOG;

			if (!$sourceofhire['extension']) {
				$sourceofhire['image'] .= 'catalog/';
			} else {
				$sourceofhire['image'] .= 'extension/' . $sourceofhire['extension'] . '/catalog/';
			}

		sourceofhire['image'] .= 'language/' . $sourceofhire['code'] . '/' . $sourceofhire['code'] . '.png';
		}

		return $sourceofhire;
	}*/
		public function getSourceofhireByName(string $name): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sourceofhire` WHERE `name` = '" . $this->db->escape($name) . "'");

		$sourceofhire = $query->row;
        
		return $sourceofhire;
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
	public function getSourceofhires(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "sourceofhire`";

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

			$this->cache->set('sourceofhire.' . md5($sql), $results);
		}

		$sourceofhire_data = [];

		foreach ($results as $result) {

			$sourceofhire_data[] = $result;
		}

		return $sourceofhire_data;
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
	public function getsourceofhiresByExtension(string $extension): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sourceofhire` WHERE `extension` = '" . $this->db->escape($extension) . "'");

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
	public function getTotalSourceofhires(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "sourceofhire`");
		return (int)$query->row['total'];
	}
}
