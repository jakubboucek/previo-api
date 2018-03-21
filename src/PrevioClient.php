<?php

declare(strict_types=1);

namespace NAttreid\PrevioApi;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use NAttreid\PrevioApi\Hooks\PrevioConfig;
use Nette\SmartObject;
use Nette\Utils\ArrayHash;
use SimpleXMLElement;

/**
 * Class Client
 *
 * @author Attreid <attreid@gmail.com>
 */
class PrevioClient
{
	use SmartObject;

	/** @var Client */
	private $client;

	/** @var string */
	private $uri = 'https://api.previo.cz/x1/';

	/** @var PrevioConfig */
	private $config;

	/** @var bool */
	private $debug;

	public function __construct(bool $debug, PrevioConfig $config)
	{
		$this->config = $config;
		$this->debug = $debug;
	}

	private function getClient(): Client
	{
		if ($this->client === null) {
			$this->client = new Client(['base_uri' => $this->uri]);
		}
		return $this->client;
	}

	private function createXml(ArrayHash $data): string
	{
		$parser = function (ArrayHash $data) use (&$parser) {
			$result = '';
			foreach ($data as $tag => $value) {
				if ($value instanceof ArrayHash) {
					$value = $parser($value);
				}
				$result .= "<$tag>$value</$tag>\n";
			}
			return $result;
		};

		$result = $parser($data);

		return
			'<?xml version="1.0"?>' . "\n" .
			'<request>' .
			$result .
			'</request>';
	}

	/**
	 * @param string $url
	 * @param ArrayHash|null $args
	 * @return SimpleXMLElement|null
	 * @throws CredentialsNotSetException
	 * @throws PrevioClientException
	 */
	private function request(string $url, ArrayHash $args = null): ?SimpleXMLElement
	{
		if (empty($this->config->login) || empty($this->config->password) || empty($this->config->hotelId)) {
			throw new CredentialsNotSetException('Login, password and hotelId must be set');
		}

		if ($args === null) {
			$args = new ArrayHash;
		}
		$args->login = $this->config->login;
		$args->password = $this->config->password;
		$args->hotId = $this->config->hotelId;

		$request = new Request(
			'POST',
			$url,
			['Content-Type' => 'text/xml; charset=UTF8'],
			$this->createXml($args)
		);
		$response = $this->getClient()->send($request);

		$result = $response->getBody()->getContents();
		$xml = simplexml_load_string($result);

		if ($xml->getName() === 'error') {
			if ($this->debug) {
				throw new PrevioClientException((string) $xml->message, (int) $xml->code);
			} else {
				return null;
			}
		} else {
			return $xml;
		}

	}

	/**
	 * @return null|SimpleXMLElement
	 * @throws CredentialsNotSetException
	 * @throws PrevioClientException
	 */
	public function getHotel(): ?SimpleXMLElement
	{
		return $this->request('hotel/get/');
	}

	/**
	 * @return null|SimpleXMLElement
	 * @throws CredentialsNotSetException
	 * @throws PrevioClientException
	 */
	public function getReservations(): ?SimpleXMLElement
	{
		$data = new ArrayHash;

		$data->term = new ArrayHash;
		$date = new DateTimeImmutable;

		$data->term->from = $date->format('Y-m-d');
		$data->term->to = $date->modify('+1 year')->format('Y-m-d');

		return $this->request('hotel/searchReservations/', $data);
	}
}
