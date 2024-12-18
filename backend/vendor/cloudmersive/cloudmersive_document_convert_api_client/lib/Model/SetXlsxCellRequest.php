<?php
/**
 * SetXlsxCellRequest
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
 * SetXlsxCellRequest Class Doc Comment
 *
 * @category Class
 * @description Input to a Set Cell in XLSX Worksheets request
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class SetXlsxCellRequest implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'SetXlsxCellRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'input_file_bytes' => 'string',
        'input_file_url' => 'string',
        'worksheet_to_update' => '\Swagger\Client\Model\XlsxWorksheet',
        'row_index' => 'int',
        'cell_index' => 'int',
        'cell_value' => '\Swagger\Client\Model\XlsxSpreadsheetCell'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'input_file_bytes' => 'byte',
        'input_file_url' => null,
        'worksheet_to_update' => null,
        'row_index' => 'int32',
        'cell_index' => 'int32',
        'cell_value' => null
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
        'worksheet_to_update' => 'WorksheetToUpdate',
        'row_index' => 'RowIndex',
        'cell_index' => 'CellIndex',
        'cell_value' => 'CellValue'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'input_file_bytes' => 'setInputFileBytes',
        'input_file_url' => 'setInputFileUrl',
        'worksheet_to_update' => 'setWorksheetToUpdate',
        'row_index' => 'setRowIndex',
        'cell_index' => 'setCellIndex',
        'cell_value' => 'setCellValue'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'input_file_bytes' => 'getInputFileBytes',
        'input_file_url' => 'getInputFileUrl',
        'worksheet_to_update' => 'getWorksheetToUpdate',
        'row_index' => 'getRowIndex',
        'cell_index' => 'getCellIndex',
        'cell_value' => 'getCellValue'
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
        $this->container['worksheet_to_update'] = isset($data['worksheet_to_update']) ? $data['worksheet_to_update'] : null;
        $this->container['row_index'] = isset($data['row_index']) ? $data['row_index'] : null;
        $this->container['cell_index'] = isset($data['cell_index']) ? $data['cell_index'] : null;
        $this->container['cell_value'] = isset($data['cell_value']) ? $data['cell_value'] : null;
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
        return count($this->listInvalidProperties()) === 0;
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
            throw new \InvalidArgumentException("invalid value for $input_file_bytes when calling SetXlsxCellRequest., must conform to the pattern /^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/.");
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
     * Gets worksheet_to_update
     *
     * @return \Swagger\Client\Model\XlsxWorksheet
     */
    public function getWorksheetToUpdate()
    {
        return $this->container['worksheet_to_update'];
    }

    /**
     * Sets worksheet_to_update
     *
     * @param \Swagger\Client\Model\XlsxWorksheet $worksheet_to_update Optional; Worksheet (tab) within the spreadsheet to update; leave blank to default to the first worksheet
     *
     * @return $this
     */
    public function setWorksheetToUpdate($worksheet_to_update)
    {
        $this->container['worksheet_to_update'] = $worksheet_to_update;

        return $this;
    }

    /**
     * Gets row_index
     *
     * @return int
     */
    public function getRowIndex()
    {
        return $this->container['row_index'];
    }

    /**
     * Sets row_index
     *
     * @param int $row_index 0-based index of the row, 0, 1, 2, ... to set
     *
     * @return $this
     */
    public function setRowIndex($row_index)
    {
        $this->container['row_index'] = $row_index;

        return $this;
    }

    /**
     * Gets cell_index
     *
     * @return int
     */
    public function getCellIndex()
    {
        return $this->container['cell_index'];
    }

    /**
     * Sets cell_index
     *
     * @param int $cell_index 0-based index of the cell, 0, 1, 2, ... in the row to set
     *
     * @return $this
     */
    public function setCellIndex($cell_index)
    {
        $this->container['cell_index'] = $cell_index;

        return $this;
    }

    /**
     * Gets cell_value
     *
     * @return \Swagger\Client\Model\XlsxSpreadsheetCell
     */
    public function getCellValue()
    {
        return $this->container['cell_value'];
    }

    /**
     * Sets cell_value
     *
     * @param \Swagger\Client\Model\XlsxSpreadsheetCell $cell_value New Cell value to update/overwrite into the Excel XLSX spreadsheet
     *
     * @return $this
     */
    public function setCellValue($cell_value)
    {
        $this->container['cell_value'] = $cell_value;

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


