<?php
namespace Opencart\System\Library\Cart;
/**
 * Class Curl
 *
 * @package Opencart\System\Library\Cart
 */
class Curl {
	/**
	 * @var string
	 */
	private string $url = '';
	/**
	 * @var array<string, mixed>
	 */
	private array $option = [];

	/**
	 * Constructor
	 *
	 * @param string $url
	 */
	public function __construct(string $url) {
		$this->url = $url;
	}

	/**
	 * Set Option
	 *
	 * @param string $key
	 * @param array  $data<string, mixed> array of data
	 *
	 * @return void
	 */
	public function setOption(string $key, array $data = []): void {
		$this->option[$key] = $data;
	}

	public function send(string $route, $data = []) {
		// Make remote call
		$url  = 'http://' . $this->url . 'index.php?route=' . $route;

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$response = curl_exec($curl);

		if ($response === false) {
			$error = curl_error($curl);
			curl_close($curl);
			throw new \Exception('Error: cURL request failed for route ' . $route . '! Message: ' . $error);
		}

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($status == 200) {
			$response_info = json_decode($response, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new \Exception('Error: Invalid JSON response for route ' . $route . '! JSON error: ' . json_last_error_msg());
			}
		} else {
			$response_info = [];
		}

		return $response_info;
	}
}
