<?php
namespace Opencart\System\Library\Cache;
/**
 * Class File
 *
 * @package Opencart\System\Library\Cache
 */
class File {
	/**
	 * @var int
	 */
	private int $expire;

	/**
	 * Constructor
	 *
	 * @param int $expire
	 */
	public function __construct(int $expire = 3600) {
		$this->expire = $expire;
	}

	/**
	 * Get
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get(string $key) {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		foreach ($files as $file) {
			$time = substr(strrchr($file, '.'), 1);

			if ($time < time()) {
				if (!@unlink($file)) {
					clearstatcache(false, $file);
				}
			} else {
				$contents = file_get_contents($file);

				if ($contents === false) {
					error_log('Failed to read cache file: ' . $file);
					return [];
				}

				$data = json_decode($contents, true);

				if (json_last_error() !== JSON_ERROR_NONE) {
					error_log('Failed to decode cache file: ' . $file . ' - ' . json_last_error_msg());
					return [];
				}

				return $data;
			}
		}

		return [];
	}

	/**
	 * Set
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $expire
	 *
	 * @return void
	 */
	public function set(string $key, $value, int $expire = 0): void {
		$this->delete($key);

		if (!$expire) {
			$expire = $this->expire;
		}

		$result = file_put_contents(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $expire), json_encode($value));

		if ($result === false) {
			error_log('Failed to write cache file for key: ' . $key);
		}
	}

	/**
	 * Delete
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function delete(string $key): void {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
			foreach ($files as $file) {
				if (!@unlink($file)) {
					clearstatcache(false, $file);
				}
			}
		}
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		$files = glob(DIR_CACHE . 'cache.*');

		if ($files && mt_rand(1, 100) == 1) {
			foreach ($files as $file) {
				$time = substr(strrchr($file, '.'), 1);

				if ($time < time()) {
					if (!@unlink($file)) {
						clearstatcache(false, $file);
					}
				}
			}
		}
	}
}
