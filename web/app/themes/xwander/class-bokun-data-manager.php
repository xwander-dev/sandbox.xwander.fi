<?php
class Bokun_Data_Manager {
    private static $instance = null;
    private $details = null;
    private $experience_id = null;
    private $language = null;

    private function __construct() {}

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init($experience_id, $language) {
        if ($this->experience_id !== $experience_id || $this->language !== $language) {
            $this->experience_id = $experience_id;
            $this->language = $language;
            $this->details = $this->fetch_bokun_product_details($experience_id, $language);
        }
        return $this->details;
    }

    public function get_details() {
        return $this->details;
    }

    public function get_title() {
        return $this->details['title'] ?? null;
    }

    public function get_excerpt() {
        return $this->details['excerpt'] ?? null;
    }

    public function get_duration() {
        return $this->details['durationText'] ?? null;
    }

    public function get_description() {
        return $this->details['description'] ?? null;
    }

    public function get_price() {
        return $this->details['price'] ?? null;
    }

    public function get_currency() {
        return $this->details['currency'] ?? 'EUR';
    }

    public function get_agenda_items() {
        return $this->details['agendaItems'] ?? null;
    }

    private function fetch_bokun_product_details($experience_id, $language) {
        $api_url = 'https://api.bokun.io';
        $access_key = BOKUN_ACCESS_KEY;
        $secret_key = BOKUN_SECRET_KEY;
        $http_method = 'GET';
        $json_path = "/activity.json/{$experience_id}";
        $query_string = "lang={$language}";
        $utc_datetime = gmdate("Y-m-d H:i:s");

        $string_to_sign = "{$utc_datetime}{$access_key}{$http_method}{$json_path}?{$query_string}";
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $secret_key, true));
        $request_uri = $api_url . $json_path . '?' . $query_string;

        $response = wp_remote_get($request_uri, array(
            'headers' => array(
                'Accept' => 'application/json',
                'X-Bokun-AccessKey' => $access_key,
                'X-Bokun-Date' => $utc_datetime,
                'X-Bokun-Signature' => $signature,
            )
        ));

        if (is_wp_error($response)) {
            error_log('Request error: ' . $response->get_error_message());
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data)) {
            if (isset($data['status'])) {
                error_log('Bokun API response status: ' . $data['status']);
            }
            if (isset($data['message'])) {
                error_log('Bokun API response message: ' . $data['message']);
            }
            return null;
        }

        $cancellation = [];
        if (isset($data['cancellationPolicy']['id']) && $data['cancellationPolicy']['id'] == 203897) {
            $cancellation[] = [
                'type' => 'non_refundable',
            ];
        } elseif (isset($data['cancellationPolicy']['penaltyRules']) && is_array($data['cancellationPolicy']['penaltyRules'])) {
            foreach ($data['cancellationPolicy']['penaltyRules'] as $rule) {
                if (isset($rule['chargeType']) && $rule['chargeType'] === 'percentage') {
                    $percentage = $rule['percentage'] ?? 0;
                    if ($percentage == 0) {
                        continue;
                    }
                    $days = $rule['cutoffDays'] ?? 0;
                    $hours = $rule['cutoffHours'] ?? 0;

                    if ($hours >= 25) {
                        $additional_days = floor($hours / 24);
                        $hours = $hours % 24;
                        $days += $additional_days;
                    }

                    $cancellation[] = [
                        'type' => 'penalty',
                        'percentage' => $percentage,
                        'days' => $days,
                        'hours' => $hours,
                    ];
                }
            }
        }

        return [
            'title' => $data['title'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'description' => isset($data['description']) ? $this->strip_inline_styles($data['description']) : null,
            'included' => $data['included'] ?? null,
            'excluded' => $data['excluded'] ?? null,
            'durationText' => $data['durationText'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'attention' => $data['attention'] ?? null,
            'knowBeforeYouGoItems' => $data['knowBeforeYouGoItems'] ?? null,
            'minAge' => $data['minAge'] ?? null,
            'cancellation' => $cancellation,
            'price' => $data['nextDefaultPriceMoney']['amount'] ?? null,
            'currency' => $data['nextDefaultPriceMoney']['currency'] ?? 'EUR',
            'minPerBooking' => $data['rates'][0]['minPerBooking'] ?? 1,
            'maxPerBooking' => $data['rates'][0]['maxPerBooking'] ?? 12,
            'lastPublished' => isset($data['lastPublished']) ? date('Y-m-d', strtotime($data['lastPublished'])) : date('Y-m-d'),
            'agendaItems' => $data['agendaItems'] ?? null,
        ];
    }
	
	public function extract_list_items($html_content) {
		if (empty($html_content)) {
			return [];
		}

		$start_pos = strpos($html_content, '<ul>');
		if ($start_pos !== false) {
			$html_content = substr($html_content, $start_pos);
		}

		$end_pos = strpos($html_content, '</ul>');
		if ($end_pos !== false) {
			$html_content = substr($html_content, 0, $end_pos + 5);
		}

		$html_content = str_replace(['<ul>', '</ul>'], '', $html_content);
		$list_items = array_filter(array_map('trim', explode('</li>', $html_content)));

		$items = array_map(function($item) {
			return strip_tags(trim($item));
		}, $list_items);

		return $items;
	}

	public function strip_inline_styles($html) {
		$dom = new DOMDocument;
		libxml_use_internal_errors(true);

		$html = mb_convert_encoding($html, 'UTF-8', 'auto');
		$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();

		$xpath = new DOMXPath($dom);
		foreach ($xpath->query('//*[@style]') as $node) {
			$node->removeAttribute('style');
		}

		return $dom->saveHTML();
	}
}