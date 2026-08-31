<?php
/**
 * Map a numeric transaction status code to a human-readable label.
 *
 * @param int $status
 *
 * @return string
 */
function oc_transaction_status_label(int $status): string {
	return match ($status) {
		0 => 'Failed',
		1 => 'Success',
		2 => 'Pending',
		4 => 'Refund',
		5 => 'Processing',
		default => 'UnKnown',
	};
}

/**
 * Extract filter values from a GET array using a definitions map.
 *
 * @param array $get         The request GET array
 * @param array $definitions ['param_name' => 'default_value', ...]
 *
 * @return array
 */
function oc_extract_filters(array $get, array $definitions): array {
	$filters = [];

	foreach ($definitions as $key => $default) {
		$filters[$key] = $get[$key] ?? $default;
	}

	return $filters;
}

/**
 * Build a URL query string from GET parameters.
 *
 * @param array $get       The request GET array
 * @param array $params    Ordered list of parameter names to include
 * @param array $fallbacks ['param_name' => 'fallback_value', ...] always included
 *
 * @return string
 */
function oc_build_filter_url(array $get, array $params, array $fallbacks = []): string {
	$url = '';

	foreach ($params as $param) {
		if (isset($get[$param])) {
			$url .= '&' . $param . '=' . urlencode(html_entity_decode((string)$get[$param], ENT_QUOTES, 'UTF-8'));
		} elseif (isset($fallbacks[$param])) {
			$url .= '&' . $param . '=' . urlencode(html_entity_decode((string)$fallbacks[$param], ENT_QUOTES, 'UTF-8'));
		}
	}

	return $url;
}

/**
 * Generate pagination results text using OpenCart's standard sprintf pattern.
 *
 * @param string $format  The language string with %d placeholders
 * @param int    $total
 * @param int    $page
 * @param int    $limit
 *
 * @return string
 */
function oc_pagination_text(string $format, int $total, int $page, int $limit): string {
	return sprintf(
		$format,
		($total) ? (($page - 1) * $limit) + 1 : 0,
		((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
		$total,
		ceil($total / $limit)
	);
}

/**
 * Fetch common transaction summary statistics from a history model.
 *
 * @param object $model
 * @param array  $filter_data
 *
 * @return array
 */
function oc_transaction_summary(object $model, array $filter_data): array {
	return [
		'product_total_sale'        => $model->getTotalSales($filter_data),
		'product_total_failed'      => $model->getTotalFailed($filter_data),
		'product_total_pending'     => $model->getTotalPending($filter_data),
		'product_total_success'     => $model->getTotalSuccess($filter_data),
		'product_total_adminprofit' => $model->getTotalAdminProfit($filter_data),
		'product_total_agentprofit' => $model->getTotalAgentProfit($filter_data),
		'product_total_surcharge'   => $model->getTotalAgentSurcharge($filter_data),
		'product_total_upword'      => $model->getTotalUpwordProfit($filter_data),
	];
}

/**
 * Extract error-warning and success flash message from session.
 *
 * @param array $errors
 * @param array &$session_data
 *
 * @return array
 */
function oc_flash_messages(array $errors, array &$session_data): array {
	$data = [];

	$data['error_warning'] = $errors['warning'] ?? '';

	if (isset($session_data['success'])) {
		$data['success'] = $session_data['success'];
		unset($session_data['success']);
	} else {
		$data['success'] = '';
	}

	return $data;
}
