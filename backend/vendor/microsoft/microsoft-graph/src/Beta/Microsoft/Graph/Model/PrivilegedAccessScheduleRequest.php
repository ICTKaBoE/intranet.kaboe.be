<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* PrivilegedAccessScheduleRequest File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
namespace Beta\Microsoft\Graph\Model;

/**
* PrivilegedAccessScheduleRequest class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class PrivilegedAccessScheduleRequest extends Request
{
    /**
    * Gets the action
    *
    * @return ScheduleRequestActions|null The action
    */
    public function getAction()
    {
        if (array_key_exists("action", $this->_propDict)) {
            if (is_a($this->_propDict["action"], "\Beta\Microsoft\Graph\Model\ScheduleRequestActions") || is_null($this->_propDict["action"])) {
                return $this->_propDict["action"];
            } else {
                $this->_propDict["action"] = new ScheduleRequestActions($this->_propDict["action"]);
                return $this->_propDict["action"];
            }
        }
        return null;
    }

    /**
    * Sets the action
    *
    * @param ScheduleRequestActions $val The action
    *
    * @return PrivilegedAccessScheduleRequest
    */
    public function setAction($val)
    {
        $this->_propDict["action"] = $val;
        return $this;
    }

    /**
    * Gets the isValidationOnly
    *
    * @return bool|null The isValidationOnly
    */
    public function getIsValidationOnly()
    {
        if (array_key_exists("isValidationOnly", $this->_propDict)) {
            return $this->_propDict["isValidationOnly"];
        } else {
            return null;
        }
    }

    /**
    * Sets the isValidationOnly
    *
    * @param bool $val The isValidationOnly
    *
    * @return PrivilegedAccessScheduleRequest
    */
    public function setIsValidationOnly($val)
    {
        $this->_propDict["isValidationOnly"] = boolval($val);
        return $this;
    }

    /**
    * Gets the justification
    *
    * @return string|null The justification
    */
    public function getJustification()
    {
        if (array_key_exists("justification", $this->_propDict)) {
            return $this->_propDict["justification"];
        } else {
            return null;
        }
    }

    /**
    * Sets the justification
    *
    * @param string $val The justification
    *
    * @return PrivilegedAccessScheduleRequest
    */
    public function setJustification($val)
    {
        $this->_propDict["justification"] = $val;
        return $this;
    }

    /**
    * Gets the scheduleInfo
    *
    * @return RequestSchedule|null The scheduleInfo
    */
    public function getScheduleInfo()
    {
        if (array_key_exists("scheduleInfo", $this->_propDict)) {
            if (is_a($this->_propDict["scheduleInfo"], "\Beta\Microsoft\Graph\Model\RequestSchedule") || is_null($this->_propDict["scheduleInfo"])) {
                return $this->_propDict["scheduleInfo"];
            } else {
                $this->_propDict["scheduleInfo"] = new RequestSchedule($this->_propDict["scheduleInfo"]);
                return $this->_propDict["scheduleInfo"];
            }
        }
        return null;
    }

    /**
    * Sets the scheduleInfo
    *
    * @param RequestSchedule $val The scheduleInfo
    *
    * @return PrivilegedAccessScheduleRequest
    */
    public function setScheduleInfo($val)
    {
        $this->_propDict["scheduleInfo"] = $val;
        return $this;
    }

    /**
    * Gets the ticketInfo
    *
    * @return TicketInfo|null The ticketInfo
    */
    public function getTicketInfo()
    {
        if (array_key_exists("ticketInfo", $this->_propDict)) {
            if (is_a($this->_propDict["ticketInfo"], "\Beta\Microsoft\Graph\Model\TicketInfo") || is_null($this->_propDict["ticketInfo"])) {
                return $this->_propDict["ticketInfo"];
            } else {
                $this->_propDict["ticketInfo"] = new TicketInfo($this->_propDict["ticketInfo"]);
                return $this->_propDict["ticketInfo"];
            }
        }
        return null;
    }

    /**
    * Sets the ticketInfo
    *
    * @param TicketInfo $val The ticketInfo
    *
    * @return PrivilegedAccessScheduleRequest
    */
    public function setTicketInfo($val)
    {
        $this->_propDict["ticketInfo"] = $val;
        return $this;
    }

}
