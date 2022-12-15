<?php
/**
 * DocxTableSingleTableFill
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * convertapi
 *
 * Convert API lets you effortlessly convert file formats and types.
 *
 * OpenAPI spec version: v1
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.3.1
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Model;

use \ArrayAccess;
use \Swagger\Client\ObjectSerializer;

/**
 * DocxTableSingleTableFill Class Doc Comment
 *
 * @category Class
 * @description Single table fill request in a multi-table fill operation
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class DocxTableSingleTableFill implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'DocxTableSingleTableFill';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'table_start_tag' => 'string',
        'table_end_tag' => 'string',
        'data_to_fill_in' => '\Swagger\Client\Model\DocxTableTableFillTableRow[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'table_start_tag' => null,
        'table_end_tag' => null,
        'data_to_fill_in' => null
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
        'table_start_tag' => 'TableStartTag',
        'table_end_tag' => 'TableEndTag',
        'data_to_fill_in' => 'DataToFillIn'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'table_start_tag' => 'setTableStartTag',
        'table_end_tag' => 'setTableEndTag',
        'data_to_fill_in' => 'setDataToFillIn'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'table_start_tag' => 'getTableStartTag',
        'table_end_tag' => 'getTableEndTag',
        'data_to_fill_in' => 'getDataToFillIn'
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
        $this->container['table_start_tag'] = isset($data['table_start_tag']) ? $data['table_start_tag'] : null;
        $this->container['table_end_tag'] = isset($data['table_end_tag']) ? $data['table_end_tag'] : null;
        $this->container['data_to_fill_in'] = isset($data['data_to_fill_in']) ? $data['data_to_fill_in'] : null;
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
     * Gets table_start_tag
     *
     * @return string
     */
    public function getTableStartTag()
    {
        return $this->container['table_start_tag'];
    }

    /**
     * Sets table_start_tag
     *
     * @param string $table_start_tag Start tag that delineates the beginning of the table
     *
     * @return $this
     */
    public function setTableStartTag($table_start_tag)
    {
        $this->container['table_start_tag'] = $table_start_tag;

        return $this;
    }

    /**
     * Gets table_end_tag
     *
     * @return string
     */
    public function getTableEndTag()
    {
        return $this->container['table_end_tag'];
    }

    /**
     * Sets table_end_tag
     *
     * @param string $table_end_tag End tag that delineates the end of the table
     *
     * @return $this
     */
    public function setTableEndTag($table_end_tag)
    {
        $this->container['table_end_tag'] = $table_end_tag;

        return $this;
    }

    /**
     * Gets data_to_fill_in
     *
     * @return \Swagger\Client\Model\DocxTableTableFillTableRow[]
     */
    public function getDataToFillIn()
    {
        return $this->container['data_to_fill_in'];
    }

    /**
     * Sets data_to_fill_in
     *
     * @param \Swagger\Client\Model\DocxTableTableFillTableRow[] $data_to_fill_in Data set to populate the table with
     *
     * @return $this
     */
    public function setDataToFillIn($data_to_fill_in)
    {
        $this->container['data_to_fill_in'] = $data_to_fill_in;

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

