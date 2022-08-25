<?php
/**
 * InsertDocxTableRowRequest
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
 * InsertDocxTableRowRequest Class Doc Comment
 *
 * @category Class
 * @description Input to a Insert Table Row request
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class InsertDocxTableRowRequest implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'InsertDocxTableRowRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'input_file_bytes' => 'string',
        'input_file_url' => 'string',
        'row_to_insert' => '\Swagger\Client\Model\DocxTableRow',
        'insert_placement' => 'string',
        'existing_table_path' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'input_file_bytes' => 'byte',
        'input_file_url' => null,
        'row_to_insert' => null,
        'insert_placement' => null,
        'existing_table_path' => null
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
        'input_file_bytes' => 'InputFileBytes',
        'input_file_url' => 'InputFileUrl',
        'row_to_insert' => 'RowToInsert',
        'insert_placement' => 'InsertPlacement',
        'existing_table_path' => 'ExistingTablePath'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'input_file_bytes' => 'setInputFileBytes',
        'input_file_url' => 'setInputFileUrl',
        'row_to_insert' => 'setRowToInsert',
        'insert_placement' => 'setInsertPlacement',
        'existing_table_path' => 'setExistingTablePath'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'input_file_bytes' => 'getInputFileBytes',
        'input_file_url' => 'getInputFileUrl',
        'row_to_insert' => 'getRowToInsert',
        'insert_placement' => 'getInsertPlacement',
        'existing_table_path' => 'getExistingTablePath'
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
        $this->container['input_file_bytes'] = isset($data['input_file_bytes']) ? $data['input_file_bytes'] : null;
        $this->container['input_file_url'] = isset($data['input_file_url']) ? $data['input_file_url'] : null;
        $this->container['row_to_insert'] = isset($data['row_to_insert']) ? $data['row_to_insert'] : null;
        $this->container['insert_placement'] = isset($data['insert_placement']) ? $data['insert_placement'] : null;
        $this->container['existing_table_path'] = isset($data['existing_table_path']) ? $data['existing_table_path'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if (!is_null($this->container['input_file_bytes']) && !preg_match("/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/", $this->container['input_file_bytes'])) {
            $invalidProperties[] = "invalid value for 'input_file_bytes', must be conform to the pattern /^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/.";
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

        if (!preg_match("/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/", $this->container['input_file_bytes'])) {
            return false;
        }
        return true;
    }


    /**
     * Gets input_file_bytes
     *
     * @return string
     */
    public function getInputFileBytes()
    {
        return $this->container['input_file_bytes'];
    }

    /**
     * Sets input_file_bytes
     *
     * @param string $input_file_bytes Optional: Bytes of the input file to operate on
     *
     * @return $this
     */
    public function setInputFileBytes($input_file_bytes)
    {

        if (!is_null($input_file_bytes) && (!preg_match("/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/", $input_file_bytes))) {
            throw new \InvalidArgumentException("invalid value for $input_file_bytes when calling InsertDocxTableRowRequest., must conform to the pattern /^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/.");
        }

        $this->container['input_file_bytes'] = $input_file_bytes;

        return $this;
    }

    /**
     * Gets input_file_url
     *
     * @return string
     */
    public function getInputFileUrl()
    {
        return $this->container['input_file_url'];
    }

    /**
     * Sets input_file_url
     *
     * @param string $input_file_url Optional: URL of a file to operate on as input.  This can be a public URL, or you can also use the begin-editing API to upload a document and pass in the secure URL result from that operation as the URL here (this URL is not public).
     *
     * @return $this
     */
    public function setInputFileUrl($input_file_url)
    {
        $this->container['input_file_url'] = $input_file_url;

        return $this;
    }

    /**
     * Gets row_to_insert
     *
     * @return \Swagger\Client\Model\DocxTableRow
     */
    public function getRowToInsert()
    {
        return $this->container['row_to_insert'];
    }

    /**
     * Sets row_to_insert
     *
     * @param \Swagger\Client\Model\DocxTableRow $row_to_insert Table you would like to insert
     *
     * @return $this
     */
    public function setRowToInsert($row_to_insert)
    {
        $this->container['row_to_insert'] = $row_to_insert;

        return $this;
    }

    /**
     * Gets insert_placement
     *
     * @return string
     */
    public function getInsertPlacement()
    {
        return $this->container['insert_placement'];
    }

    /**
     * Sets insert_placement
     *
     * @param string $insert_placement Optional; default is TableEnd.  Placement Type of the insert; possible values are: TableStart (very beginning of the table), TableEnd (very end of the document), or a 0-based row index number as an integer, 0 being the first row in the table, 1 being the second row in the table, 2 being the third row in the table, etc. to insert this row after
     *
     * @return $this
     */
    public function setInsertPlacement($insert_placement)
    {
        $this->container['insert_placement'] = $insert_placement;

        return $this;
    }

    /**
     * Gets existing_table_path
     *
     * @return string
     */
    public function getExistingTablePath()
    {
        return $this->container['existing_table_path'];
    }

    /**
     * Sets existing_table_path
     *
     * @param string $existing_table_path Required; the path to the existing table to modify
     *
     * @return $this
     */
    public function setExistingTablePath($existing_table_path)
    {
        $this->container['existing_table_path'] = $existing_table_path;

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


