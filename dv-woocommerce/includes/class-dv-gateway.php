<?php

namespace DvWoocommerce;

use DvNet\DvNetClient\Dto\WebhookMapper\ConfirmedWebhookResponse;
use DvNet\DvNetClient\MerchantClient;
use DvNet\DvNetClient\MerchantMapper;
use DvNet\DvNetClient\MerchantUtilsManager;
use DvNet\DvNetClient\SimpleHttpClient;
use DvNet\DvNetClient\WebhookMapper;
use WC_Payment_Gateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DV_Gateway extends WC_Payment_Gateway {
	private $url;
	private $apiKey;
	private $apiSecret;

	public function __construct() {
		$this->id                 = 'dv_gateway';
		$this->icon               = '';
		$this->has_fields         = false;
		$this->method_title       = 'DV.net Gateway';
		$this->method_description = 'Allows payments with DV.net';

		$this->init_form_fields();
		$this->init_settings();

		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->enabled      = $this->get_option( 'enabled' );

		$this->url    = $this->get_option( 'url' );
		$this->apiKey = $this->get_option( 'api_key' );
		$this->apiSecret = $this->get_option( 'api_secret' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		add_action( 'woocommerce_api_dv_gateway', [ $this, 'handle_webhook' ] );
	}

	public function init_form_fields() {
		$this->form_fields = [
			'enabled' => [
				'title'   => 'Enable/Disable',
				'type'    => 'checkbox',
				'label'   => 'Enable DV.net Gateway',
				'default' => 'yes'
			],
			'title' => [
				'title'       => 'Title',
				'type'        => 'text',
				'description' => 'This controls the title which the user sees during checkout.',
				'default'     => 'Pay with DV.net',
				'desc_tip'    => true,
			],
			'description' => [
				'title'       => 'Description',
				'type'        => 'textarea',
				'description' => 'This controls the description which the user sees during checkout.',
				'default'     => 'Pay via our secure payment gateway.',
			],
			'merchant_url' => [
				'title'       => 'Merchant Url',
				'type'        => 'url'
			],
			'api_key' => [
				'title'       => 'API Key',
				'description' => 'This key is using for communication from your store to merchant',
				'type'        => 'password',
			],
			'api_secret' => [
				'title'       => 'API Secret',
				'description' => 'This key is using for validation webhooks from merchant',
				'type'        => 'password',
			]
		];
	}

	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		$merchantUrl = $this->get_option( 'merchant_url' );
		$apiKey = $this->get_option( 'api_key' );

		if ( empty($merchantUrl) || empty($apiKey) ) {
			wc_add_notice( 'Payment gateway is not configured. Please contact support.', 'error' );
			return;
		}

		try {
			$result = (new MerchantClient(
				new SimpleHttpClient(),
				new MerchantMapper(),
				$merchantUrl,
				$apiKey,
			))->getExternalWallet(
				storeExternalId: $order->get_id(),
				email: $order->get_user()->user_email,
				amount: $order->get_total(),
				currency: $order->get_currency(),
			);

			return [
				'result'   => 'success',
				'redirect' => $result->payUrl,
			];
		} catch ( \Exception $e ) {
			wc_add_notice( 'Payment error: ' . $e->getMessage(), 'error' );
			return;
		}
	}

	public function handle_webhook() {
		$payload = file_get_contents( 'php://input' );

		if ( ! $payload ) {
			wp_die( 'No payload received', 'DV.net Webhook Error', [ 'response' => 400 ] );
		}

		try {
			$webhookResponse = new WebhookMapper();
			$hashChecker = new MerchantUtilsManager();
			$hashChecker->checkSign(
				clientSignature: $_SERVER['HTTP_X_SIGN'],
				clientKey: $this->apiSecret,
				requestBody: json_decode( $payload ),
			);


			$webhookData = $webhookResponse->mapWebhook( json_decode($payload, true) );

			if ( $webhookData instanceof ConfirmedWebhookResponse ) {
				$order_id = $webhookData->wallet->storeExternalId;
				$order    = wc_get_order( $order_id );

				if ( ! $order ) {
					wp_die( 'Order not found', 'DV.net Webhook Error', [ 'response' => 404 ] );
				}

				$order->payment_complete();
				$order->add_order_note( 'Payment successfully confirmed via DV.net Webhook.' );

				wp_send_json_success( 'Webhook processed successfully.' );
			} else {
				wp_send_json_success( 'Webhook received but not a confirmation. No action taken.' );
			}
		} catch ( \Exception $e ) {
			wp_die( 'Webhook processing error: ' . $e->getMessage(), 'DV.net Webhook Error', [ 'response' => 500 ] );
		}
	}
}

