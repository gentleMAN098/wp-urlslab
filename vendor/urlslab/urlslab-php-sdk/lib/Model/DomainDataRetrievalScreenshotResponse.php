<?php
/**
 * DomainDataRetrievalScreenshotResponse
 *
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

namespace OpenAPI\Client\Model;

use \ArrayAccess;
use \OpenAPI\Client\ObjectSerializer;

/**
 * DomainDataRetrievalScreenshotResponse Class Doc Comment
 *
 * @category Class
 * @package  OpenAPI\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class DomainDataRetrievalScreenshotResponse implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'domain.dataRetrieval.ScreenshotResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'domain_id' => 'string',
        'url_id' => 'string',
        'url' => 'string',
        'screenshot_id' => 'int',
        'url_title' => 'string',
        'url_meta_description' => 'string',
        'screenshot_status' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'domain_id' => null,
        'url_id' => null,
        'url' => null,
        'screenshot_id' => 'int64',
        'url_title' => null,
        'url_meta_description' => null,
        'screenshot_status' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'domain_id' => false,
		'url_id' => false,
		'url' => false,
		'screenshot_id' => true,
		'url_title' => true,
		'url_meta_description' => true,
		'screenshot_status' => false
    ];

    /**
      * If a nullable field gets set to null, insert it here
      *
      * @var boolean[]
      */
    protected array $openAPINullablesSetToNull = [];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return boolean[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param boolean[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Checks if a property is nullable
     *
     * @param string $property
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'domain_id' => 'domainId',
        'url_id' => 'urlId',
        'url' => 'url',
        'screenshot_id' => 'screenshotID',
        'url_title' => 'urlTitle',
        'url_meta_description' => 'urlMetaDescription',
        'screenshot_status' => 'screenshotStatus'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'domain_id' => 'setDomainId',
        'url_id' => 'setUrlId',
        'url' => 'setUrl',
        'screenshot_id' => 'setScreenshotId',
        'url_title' => 'setUrlTitle',
        'url_meta_description' => 'setUrlMetaDescription',
        'screenshot_status' => 'setScreenshotStatus'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'domain_id' => 'getDomainId',
        'url_id' => 'getUrlId',
        'url' => 'getUrl',
        'screenshot_id' => 'getScreenshotId',
        'url_title' => 'getUrlTitle',
        'url_meta_description' => 'getUrlMetaDescription',
        'screenshot_status' => 'getScreenshotStatus'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }

    public const SCREENSHOT_STATUS_PENDING = 'PENDING';
    public const SCREENSHOT_STATUS_AVAILABLE = 'AVAILABLE';
    public const SCREENSHOT_STATUS_UPDATING = 'UPDATING';
    public const SCREENSHOT_STATUS_BLOCKED = 'BLOCKED';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getScreenshotStatusAllowableValues()
    {
        return [
            self::SCREENSHOT_STATUS_PENDING,
            self::SCREENSHOT_STATUS_AVAILABLE,
            self::SCREENSHOT_STATUS_UPDATING,
            self::SCREENSHOT_STATUS_BLOCKED,
        ];
    }

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->setIfExists('domain_id', $data ?? [], null);
        $this->setIfExists('url_id', $data ?? [], null);
        $this->setIfExists('url', $data ?? [], null);
        $this->setIfExists('screenshot_id', $data ?? [], null);
        $this->setIfExists('url_title', $data ?? [], null);
        $this->setIfExists('url_meta_description', $data ?? [], null);
        $this->setIfExists('screenshot_status', $data ?? [], null);
    }

    /**
    * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
    * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
    * $this->openAPINullablesSetToNull array
    *
    * @param string $variableName
    * @param array  $fields
    * @param mixed  $defaultValue
    */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['domain_id'] === null) {
            $invalidProperties[] = "'domain_id' can't be null";
        }
        if ($this->container['url_id'] === null) {
            $invalidProperties[] = "'url_id' can't be null";
        }
        if ($this->container['url'] === null) {
            $invalidProperties[] = "'url' can't be null";
        }
        if ($this->container['screenshot_status'] === null) {
            $invalidProperties[] = "'screenshot_status' can't be null";
        }
        $allowedValues = $this->getScreenshotStatusAllowableValues();
        if (!is_null($this->container['screenshot_status']) && !in_array($this->container['screenshot_status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'screenshot_status', must be one of '%s'",
                $this->container['screenshot_status'],
                implode("', '", $allowedValues)
            );
        }

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets domain_id
     *
     * @return string
     */
    public function getDomainId()
    {
        return $this->container['domain_id'];
    }

    /**
     * Sets domain_id
     *
     * @param string $domain_id domain_id
     *
     * @return self
     */
    public function setDomainId($domain_id)
    {
        if (is_null($domain_id)) {
            throw new \InvalidArgumentException('non-nullable domain_id cannot be null');
        }
        $this->container['domain_id'] = $domain_id;

        return $this;
    }

    /**
     * Gets url_id
     *
     * @return string
     */
    public function getUrlId()
    {
        return $this->container['url_id'];
    }

    /**
     * Sets url_id
     *
     * @param string $url_id url_id
     *
     * @return self
     */
    public function setUrlId($url_id)
    {
        if (is_null($url_id)) {
            throw new \InvalidArgumentException('non-nullable url_id cannot be null');
        }
        $this->container['url_id'] = $url_id;

        return $this;
    }

    /**
     * Gets url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->container['url'];
    }

    /**
     * Sets url
     *
     * @param string $url url
     *
     * @return self
     */
    public function setUrl($url)
    {
        if (is_null($url)) {
            throw new \InvalidArgumentException('non-nullable url cannot be null');
        }
        $this->container['url'] = $url;

        return $this;
    }

    /**
     * Gets screenshot_id
     *
     * @return int|null
     */
    public function getScreenshotId()
    {
        return $this->container['screenshot_id'];
    }

    /**
     * Sets screenshot_id
     *
     * @param int|null $screenshot_id screenshot_id
     *
     * @return self
     */
    public function setScreenshotId($screenshot_id)
    {
        if (is_null($screenshot_id)) {
            array_push($this->openAPINullablesSetToNull, 'screenshot_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('screenshot_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['screenshot_id'] = $screenshot_id;

        return $this;
    }

    /**
     * Gets url_title
     *
     * @return string|null
     */
    public function getUrlTitle()
    {
        return $this->container['url_title'];
    }

    /**
     * Sets url_title
     *
     * @param string|null $url_title url_title
     *
     * @return self
     */
    public function setUrlTitle($url_title)
    {
        if (is_null($url_title)) {
            array_push($this->openAPINullablesSetToNull, 'url_title');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('url_title', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['url_title'] = $url_title;

        return $this;
    }

    /**
     * Gets url_meta_description
     *
     * @return string|null
     */
    public function getUrlMetaDescription()
    {
        return $this->container['url_meta_description'];
    }

    /**
     * Sets url_meta_description
     *
     * @param string|null $url_meta_description url_meta_description
     *
     * @return self
     */
    public function setUrlMetaDescription($url_meta_description)
    {
        if (is_null($url_meta_description)) {
            array_push($this->openAPINullablesSetToNull, 'url_meta_description');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('url_meta_description', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['url_meta_description'] = $url_meta_description;

        return $this;
    }

    /**
     * Gets screenshot_status
     *
     * @return string
     */
    public function getScreenshotStatus()
    {
        return $this->container['screenshot_status'];
    }

    /**
     * Sets screenshot_status
     *
     * @param string $screenshot_status screenshot_status
     *
     * @return self
     */
    public function setScreenshotStatus($screenshot_status)
    {
        if (is_null($screenshot_status)) {
            throw new \InvalidArgumentException('non-nullable screenshot_status cannot be null');
        }
        $allowedValues = $this->getScreenshotStatusAllowableValues();
        if (!in_array($screenshot_status, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'screenshot_status', must be one of '%s'",
                    $screenshot_status,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['screenshot_status'] = $screenshot_status;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


