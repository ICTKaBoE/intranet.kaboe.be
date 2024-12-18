<?php
/**
 * DocxParagraph
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
 * DocxParagraph Class Doc Comment
 *
 * @category Class
 * @description A paragraph in a Word Document (DOCX) file; there is where text, content and formatting are stored - similar to the paragraph tag in HTML
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class DocxParagraph implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'DocxParagraph';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'paragraph_index' => 'int',
        'path' => 'string',
        'content_runs' => '\Swagger\Client\Model\DocxRun[]',
        'style_id' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'paragraph_index' => 'int32',
        'path' => null,
        'content_runs' => null,
        'style_id' => null
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
        'paragraph_index' => 'ParagraphIndex',
        'path' => 'Path',
        'content_runs' => 'ContentRuns',
        'style_id' => 'StyleID'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'paragraph_index' => 'setParagraphIndex',
        'path' => 'setPath',
        'content_runs' => 'setContentRuns',
        'style_id' => 'setStyleId'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'paragraph_index' => 'getParagraphIndex',
        'path' => 'getPath',
        'content_runs' => 'getContentRuns',
        'style_id' => 'getStyleId'
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
        $this->container['paragraph_index'] = isset($data['paragraph_index']) ? $data['paragraph_index'] : null;
        $this->container['path'] = isset($data['path']) ? $data['path'] : null;
        $this->container['content_runs'] = isset($data['content_runs']) ? $data['content_runs'] : null;
        $this->container['style_id'] = isset($data['style_id']) ? $data['style_id'] : null;
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
     * Gets paragraph_index
     *
     * @return int
     */
    public function getParagraphIndex()
    {
        return $this->container['paragraph_index'];
    }

    /**
     * Sets paragraph_index
     *
     * @param int $paragraph_index The index of the paragraph; 0-based
     *
     * @return $this
     */
    public function setParagraphIndex($paragraph_index)
    {
        $this->container['paragraph_index'] = $paragraph_index;

        return $this;
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
     * @param string $path The Path of the location of this Paragraph object; leave blank during creation
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->container['path'] = $path;

        return $this;
    }

    /**
     * Gets content_runs
     *
     * @return \Swagger\Client\Model\DocxRun[]
     */
    public function getContentRuns()
    {
        return $this->container['content_runs'];
    }

    /**
     * Sets content_runs
     *
     * @param \Swagger\Client\Model\DocxRun[] $content_runs The content runs in the paragraph - this is where text is stored; similar to a span in HTML
     *
     * @return $this
     */
    public function setContentRuns($content_runs)
    {
        $this->container['content_runs'] = $content_runs;

        return $this;
    }

    /**
     * Gets style_id
     *
     * @return string
     */
    public function getStyleId()
    {
        return $this->container['style_id'];
    }

    /**
     * Sets style_id
     *
     * @param string $style_id Style ID of the style applied to the paragraph; null if no style is applied
     *
     * @return $this
     */
    public function setStyleId($style_id)
    {
        $this->container['style_id'] = $style_id;

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


