<?php
/**
 * XlsxSpreadsheetCell
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
 * Swagger Codegen version: 2.4.32
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
 * XlsxSpreadsheetCell Class Doc Comment
 *
 * @category Class
 * @description Cell in an Excel Spreadsheet worksheet
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class XlsxSpreadsheetCell implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'XlsxSpreadsheetCell';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'path' => 'string',
        'text_value' => 'string',
        'cell_identifier' => 'string',
        'style_index' => 'int',
        'formula' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'path' => null,
        'text_value' => null,
        'cell_identifier' => null,
        'style_index' => 'int32',
        'formula' => null
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
        'path' => 'Path',
        'text_value' => 'TextValue',
        'cell_identifier' => 'CellIdentifier',
        'style_index' => 'StyleIndex',
        'formula' => 'Formula'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'path' => 'setPath',
        'text_value' => 'setTextValue',
        'cell_identifier' => 'setCellIdentifier',
        'style_index' => 'setStyleIndex',
        'formula' => 'setFormula'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'path' => 'getPath',
        'text_value' => 'getTextValue',
        'cell_identifier' => 'getCellIdentifier',
        'style_index' => 'getStyleIndex',
        'formula' => 'getFormula'
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
        $this->container['path'] = isset($data['path']) ? $data['path'] : null;
        $this->container['text_value'] = isset($data['text_value']) ? $data['text_value'] : null;
        $this->container['cell_identifier'] = isset($data['cell_identifier']) ? $data['cell_identifier'] : null;
        $this->container['style_index'] = isset($data['style_index']) ? $data['style_index'] : null;
        $this->container['formula'] = isset($data['formula']) ? $data['formula'] : null;
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
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->container['path'];
    }

    /**
     * Sets path
     *
     * @param string $path The Path of the location of this object; leave blank for new rows
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->container['path'] = $path;

        return $this;
    }

    /**
     * Gets text_value
     *
     * @return string
     */
    public function getTextValue()
    {
        return $this->container['text_value'];
    }

    /**
     * Sets text_value
     *
     * @param string $text_value Text value of the cell
     *
     * @return $this
     */
    public function setTextValue($text_value)
    {
        $this->container['text_value'] = $text_value;

        return $this;
    }

    /**
     * Gets cell_identifier
     *
     * @return string
     */
    public function getCellIdentifier()
    {
        return $this->container['cell_identifier'];
    }

    /**
     * Sets cell_identifier
     *
     * @param string $cell_identifier Cell reference of the cell, e.g. A1, Z22, etc.
     *
     * @return $this
     */
    public function setCellIdentifier($cell_identifier)
    {
        $this->container['cell_identifier'] = $cell_identifier;

        return $this;
    }

    /**
     * Gets style_index
     *
     * @return int
     */
    public function getStyleIndex()
    {
        return $this->container['style_index'];
    }

    /**
     * Sets style_index
     *
     * @param int $style_index Identifier for the style to apply to this style
     *
     * @return $this
     */
    public function setStyleIndex($style_index)
    {
        $this->container['style_index'] = $style_index;

        return $this;
    }

    /**
     * Gets formula
     *
     * @return string
     */
    public function getFormula()
    {
        return $this->container['formula'];
    }

    /**
     * Sets formula
     *
     * @param string $formula formula
     *
     * @return $this
     */
    public function setFormula($formula)
    {
        $this->container['formula'] = $formula;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    #[\ReturnTypeWillChange]
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
    #[\ReturnTypeWillChange]
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
    #[\ReturnTypeWillChange]
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
    #[\ReturnTypeWillChange]
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


