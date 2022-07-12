<?php

/**
 * GoldService.
 */

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class GoldService.
 */
class GoldService implements GoldServiceInterface
{
    /**
     * @var HttpClientInterface Http client interface
     */
    private HttpClientInterface $client;

    /**
     * Constructor.
     *
     * @param HttpClientInterface $client Http client interface
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Function that calculates average prices from given array (date range).
     * 'cena' (price) element is written in Polish, because NBP api returns response in such a way.
     *
     * @param array $array Array to calculate average prices from
     *
     * @return float Return value shortened to 2 decimal places
     */
    public function calculateAvg(array $array): float
    {
        $sum = 0;
        $number = 0;
        foreach ($array as $data) {
            $sum = $sum + $data['cena'];
            ++$number;
        }
        $avgValue = $sum / $number;

        return round($avgValue, 2);
    }

    /**
     * Checks if date format (ISO-8601) is correct.
     *
     * @param string $date   Date to compare
     * @param string $format Given format
     *
     * @return bool Boolean return
     */
    public function checkIfCorrectFormat(string $date, string $format): bool
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    /**
     * Checks if date range is correct.
     * In NBP api gold prices archive starts from 2013-01-2, so every day before that would end with response from NBP api that isn't a json response.
     *
     * @param string $date Date to compare
     *
     * @return bool Boolean return
     */
    public function checkIfCorrectDate(string $date): bool
    {
        if ($date > '2013-01-01T00:00:00+00:00') {
            return true;
        }

        return false;
    }

    /**
     * Function that makes http request using Http client.
     *
     * @param string $url Url for external request
     *
     * @return array Array of decoded json response
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function makeExternalRequest(string $url): array
    {
        $response = $this->client->request('GET', $url);
        $content = $response->getContent();

        return json_decode($content, true);
    }
}
