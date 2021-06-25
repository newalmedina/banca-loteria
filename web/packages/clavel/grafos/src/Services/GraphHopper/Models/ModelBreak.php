<?php
/**
 * ModelBreak
 *
 * PHP version 5
 *
 * @category Class
 * @package  Clavel\Grafos\Services\GraphHopper
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * GraphHopper Directions API
 *
 * You use the GraphHopper Directions API to add route planning, navigation and route optimization to your software. E.g. the Routing API has turn instructions and elevation data and the Route Optimization API solves your logistic problems and supports various constraints like time window and capacity restrictions. Also it is possible to get all distances between all locations with our fast Matrix API.
 *
 * OpenAPI spec version: 1.0.0
 *
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.0-SNAPSHOT
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Clavel\Grafos\Services\GraphHopper\Models;

use \ArrayAccess;
use Clavel\Grafos\Services\GraphHopper\Helpers\ObjectSerializer;

/**
 * ModelBreak Class Doc Comment
 *
 * @category Class
 * @package  Clavel\Grafos\Services\GraphHopper
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class ModelBreak implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Break';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'earliest' => 'int',
        'latest' => 'int',
        'duration' => 'int',
        'max_driving_time' => 'int',
        'initial_driving_time' => 'int',
        'possible_split' => 'int[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'earliest' => 'int64',
        'latest' => 'int64',
        'duration' => 'int64',
        'max_driving_time' => 'int64',
        'initial_driving_time' => 'int64',
        'possible_split' => 'int64'
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'earliest' => 'earliest',
        'latest' => 'latest',
        'duration' => 'duration',
        'max_driving_time' => 'max_driving_time',
        'initial_driving_time' => 'initial_driving_time',
        'possible_split' => 'possible_split'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'earliest' => 'setEarliest',
        'latest' => 'setLatest',
        'duration' => 'setDuration',
        'max_driving_time' => 'setMaxDrivingTime',
        'initial_driving_time' => 'setInitialDrivingTime',
        'possible_split' => 'setPossibleSplit'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'earliest' => 'getEarliest',
        'latest' => 'getLatest',
        'duration' => 'getDuration',
        'max_driving_time' => 'getMaxDrivingTime',
        'initial_driving_time' => 'getInitialDrivingTime',
        'possible_split' => 'getPossibleSplit'
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
        return self::$swaggerModelName;
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
        $this->container['earliest'] = isset($data['earliest']) ? $data['earliest'] : null;
        $this->container['latest'] = isset($data['latest']) ? $data['latest'] : null;
        $this->container['duration'] = isset($data['duration']) ? $data['duration'] : null;
        $this->container['max_driving_time'] = isset($data['max_driving_time']) ? $data['max_driving_time'] : null;
        $this->container['initial_driving_time'] = isset($data['initial_driving_time']) ? $data['initial_driving_time'] : null;
        $this->container['possible_split'] = isset($data['possible_split']) ? $data['possible_split'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

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
        return true;
    }


    /**
     * Gets earliest
     *
     * @return int
     */
    public function getEarliest()
    {
        return $this->container['earliest'];
    }

    /**
     * Sets earliest
     *
     * @param int $earliest earliest start of break
     *
     * @return $this
     */
    public function setEarliest($earliest)
    {
        $this->container['earliest'] = $earliest;

        return $this;
    }

    /**
     * Gets latest
     *
     * @return int
     */
    public function getLatest()
    {
        return $this->container['latest'];
    }

    /**
     * Sets latest
     *
     * @param int $latest latest start of break
     *
     * @return $this
     */
    public function setLatest($latest)
    {
        $this->container['latest'] = $latest;

        return $this;
    }

    /**
     * Gets duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->container['duration'];
    }

    /**
     * Sets duration
     *
     * @param int $duration duration of break
     *
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->container['duration'] = $duration;

        return $this;
    }

    /**
     * Gets max_driving_time
     *
     * @return int
     */
    public function getMaxDrivingTime()
    {
        return $this->container['max_driving_time'];
    }

    /**
     * Sets max_driving_time
     *
     * @param int $max_driving_time max driving time without break
     *
     * @return $this
     */
    public function setMaxDrivingTime($max_driving_time)
    {
        $this->container['max_driving_time'] = $max_driving_time;

        return $this;
    }

    /**
     * Gets initial_driving_time
     *
     * @return int
     */
    public function getInitialDrivingTime()
    {
        return $this->container['initial_driving_time'];
    }

    /**
     * Sets initial_driving_time
     *
     * @param int $initial_driving_time initial driving time, i.e. the time your driver has already spent for driving
     *
     * @return $this
     */
    public function setInitialDrivingTime($initial_driving_time)
    {
        $this->container['initial_driving_time'] = $initial_driving_time;

        return $this;
    }

    /**
     * Gets possible_split
     *
     * @return int[]
     */
    public function getPossibleSplit()
    {
        return $this->container['possible_split'];
    }

    /**
     * Sets possible_split
     *
     * @param int[] $possible_split array of splits
     *
     * @return $this
     */
    public function setPossibleSplit($possible_split)
    {
        $this->container['possible_split'] = $possible_split;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
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
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}