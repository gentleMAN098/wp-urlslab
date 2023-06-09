<?php
/**
 * SnapshotApi
 * PHP version 7.4
 *
 * @category Class
 * @package  OpenAPI\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * URLSLAB API
 *
 * optimize your website with SEO
 *
 * The version of the OpenAPI document: 1.0.0
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 6.3.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace OpenAPI\Client\Urlslab;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use OpenAPI\Client\ApiException;
use OpenAPI\Client\Configuration;
use OpenAPI\Client\HeaderSelector;
use OpenAPI\Client\ObjectSerializer;

/**
 * SnapshotApi Class Doc Comment
 *
 * @category Class
 * @package  OpenAPI\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class SnapshotApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'getSnapshots' => [
            'application/json',
        ],
        'getSnapshotsHistory' => [
            'text/plain',
        ],
    ];

/**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null,
        $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation getSnapshots
     *
     * Get screenshot of url
     *
     * @param  \OpenAPI\Client\Model\DomainDataRetrievalDataRequest $domain_data_retrieval_data_request Url to get related urls (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshots'] to see the possible values for this operation
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\Model\DomainDataRetrievalScreenshotResponse[]
     */
    public function getSnapshots($domain_data_retrieval_data_request, string $contentType = self::contentTypes['getSnapshots'][0])
    {
        list($response) = $this->getSnapshotsWithHttpInfo($domain_data_retrieval_data_request, $contentType);
        return $response;
    }

    /**
     * Operation getSnapshotsWithHttpInfo
     *
     * Get screenshot of url
     *
     * @param  \OpenAPI\Client\Model\DomainDataRetrievalDataRequest $domain_data_retrieval_data_request Url to get related urls (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshots'] to see the possible values for this operation
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\Model\DomainDataRetrievalScreenshotResponse[], HTTP status code, HTTP response headers (array of strings)
     */
    public function getSnapshotsWithHttpInfo($domain_data_retrieval_data_request, string $contentType = self::contentTypes['getSnapshots'][0])
    {
        $request = $this->getSnapshotsRequest($domain_data_retrieval_data_request, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\Model\DomainDataRetrievalScreenshotResponse[]' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\OpenAPI\Client\Model\DomainDataRetrievalScreenshotResponse[]' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\Model\DomainDataRetrievalScreenshotResponse[]', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\Model\DomainDataRetrievalScreenshotResponse[]';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\Model\DomainDataRetrievalScreenshotResponse[]',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getSnapshotsAsync
     *
     * Get screenshot of url
     *
     * @param  \OpenAPI\Client\Model\DomainDataRetrievalDataRequest $domain_data_retrieval_data_request Url to get related urls (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshots'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getSnapshotsAsync($domain_data_retrieval_data_request, string $contentType = self::contentTypes['getSnapshots'][0])
    {
        return $this->getSnapshotsAsyncWithHttpInfo($domain_data_retrieval_data_request, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getSnapshotsAsyncWithHttpInfo
     *
     * Get screenshot of url
     *
     * @param  \OpenAPI\Client\Model\DomainDataRetrievalDataRequest $domain_data_retrieval_data_request Url to get related urls (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshots'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getSnapshotsAsyncWithHttpInfo($domain_data_retrieval_data_request, string $contentType = self::contentTypes['getSnapshots'][0])
    {
        $returnType = '\OpenAPI\Client\Model\DomainDataRetrievalScreenshotResponse[]';
        $request = $this->getSnapshotsRequest($domain_data_retrieval_data_request, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getSnapshots'
     *
     * @param  \OpenAPI\Client\Model\DomainDataRetrievalDataRequest $domain_data_retrieval_data_request Url to get related urls (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshots'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getSnapshotsRequest($domain_data_retrieval_data_request, string $contentType = self::contentTypes['getSnapshots'][0])
    {

        // verify the required parameter 'domain_data_retrieval_data_request' is set
        if ($domain_data_retrieval_data_request === null || (is_array($domain_data_retrieval_data_request) && count($domain_data_retrieval_data_request) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $domain_data_retrieval_data_request when calling getSnapshots'
            );
        }


        $resourcePath = '/v1/snapshot';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($domain_data_retrieval_data_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($domain_data_retrieval_data_request));
            } else {
                $httpBody = $domain_data_retrieval_data_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('X-URLSLAB-KEY');
        if ($apiKey !== null) {
            $headers['X-URLSLAB-KEY'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getSnapshotsHistory
     *
     * Get history of snapshot of url
     *
     * @param  string $url Url to get the history of snapshots (required)
     * @param  string $last_id lastId of event (optional)
     * @param  string $last_timestamp lastTimestamp of event (optional)
     * @param  int $limit limit of events (optional)
     * @param  string $body body (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshotsHistory'] to see the possible values for this operation
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\Model\DomainDataRetrievalUrlSnapshotMultiResponse
     */
    public function getSnapshotsHistory($url, $last_id = null, $last_timestamp = null, $limit = null, $body = null, string $contentType = self::contentTypes['getSnapshotsHistory'][0])
    {
        list($response) = $this->getSnapshotsHistoryWithHttpInfo($url, $last_id, $last_timestamp, $limit, $body, $contentType);
        return $response;
    }

    /**
     * Operation getSnapshotsHistoryWithHttpInfo
     *
     * Get history of snapshot of url
     *
     * @param  string $url Url to get the history of snapshots (required)
     * @param  string $last_id lastId of event (optional)
     * @param  string $last_timestamp lastTimestamp of event (optional)
     * @param  int $limit limit of events (optional)
     * @param  string $body (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshotsHistory'] to see the possible values for this operation
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\Model\DomainDataRetrievalUrlSnapshotMultiResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getSnapshotsHistoryWithHttpInfo($url, $last_id = null, $last_timestamp = null, $limit = null, $body = null, string $contentType = self::contentTypes['getSnapshotsHistory'][0])
    {
        $request = $this->getSnapshotsHistoryRequest($url, $last_id, $last_timestamp, $limit, $body, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\Model\DomainDataRetrievalUrlSnapshotMultiResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\OpenAPI\Client\Model\DomainDataRetrievalUrlSnapshotMultiResponse' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\Model\DomainDataRetrievalUrlSnapshotMultiResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\Model\DomainDataRetrievalUrlSnapshotMultiResponse';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\Model\DomainDataRetrievalUrlSnapshotMultiResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getSnapshotsHistoryAsync
     *
     * Get history of snapshot of url
     *
     * @param  string $url Url to get the history of snapshots (required)
     * @param  string $last_id lastId of event (optional)
     * @param  string $last_timestamp lastTimestamp of event (optional)
     * @param  int $limit limit of events (optional)
     * @param  string $body (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshotsHistory'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getSnapshotsHistoryAsync($url, $last_id = null, $last_timestamp = null, $limit = null, $body = null, string $contentType = self::contentTypes['getSnapshotsHistory'][0])
    {
        return $this->getSnapshotsHistoryAsyncWithHttpInfo($url, $last_id, $last_timestamp, $limit, $body, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getSnapshotsHistoryAsyncWithHttpInfo
     *
     * Get history of snapshot of url
     *
     * @param  string $url Url to get the history of snapshots (required)
     * @param  string $last_id lastId of event (optional)
     * @param  string $last_timestamp lastTimestamp of event (optional)
     * @param  int $limit limit of events (optional)
     * @param  string $body (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshotsHistory'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getSnapshotsHistoryAsyncWithHttpInfo($url, $last_id = null, $last_timestamp = null, $limit = null, $body = null, string $contentType = self::contentTypes['getSnapshotsHistory'][0])
    {
        $returnType = '\OpenAPI\Client\Model\DomainDataRetrievalUrlSnapshotMultiResponse';
        $request = $this->getSnapshotsHistoryRequest($url, $last_id, $last_timestamp, $limit, $body, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getSnapshotsHistory'
     *
     * @param  string $url Url to get the history of snapshots (required)
     * @param  string $last_id lastId of event (optional)
     * @param  string $last_timestamp lastTimestamp of event (optional)
     * @param  int $limit limit of events (optional)
     * @param  string $body (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getSnapshotsHistory'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getSnapshotsHistoryRequest($url, $last_id = null, $last_timestamp = null, $limit = null, $body = null, string $contentType = self::contentTypes['getSnapshotsHistory'][0])
    {

        // verify the required parameter 'url' is set
        if ($url === null || (is_array($url) && count($url) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $url when calling getSnapshotsHistory'
            );
        }






        $resourcePath = '/v1/snapshot/history';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $url,
            'url', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            true // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $last_id,
            'lastId', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $last_timestamp,
            'lastTimestamp', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $limit,
            'limit', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($body)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($body));
            } else {
                $httpBody = $body;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('X-URLSLAB-KEY');
        if ($apiKey !== null) {
            $headers['X-URLSLAB-KEY'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
