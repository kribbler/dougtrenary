<?php

/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * PHP version 5
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Serialization
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Internal\Serialization;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;

/**
 * Short description
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Serialization
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: @package_version@
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class XmlSerializer implements ISerializer
{
    const STANDALONE  = 'standalone';
    const ROOT_NAME   = 'rootName';
    const DEFAULT_TAG = 'defaultTag';
    
    /**
     * Converts a SimpleXML object to an Array recursively
     * ensuring all sub-elements are arrays as well.
     *
     * @param string $sxml The SimpleXML object.
     * @param array  $arr  The array into which to store results.
     * 
     * @return array
     */
    private function _sxml2arr($sxml, $arr = null)
    {
        foreach ((array) $sxml as $key => $value) {
            if (is_object($value) || (is_array($value))) {
                $arr[$key] = $this->_sxml2arr($value);
            } else {
                $arr[$key] = $value;
            }
        }

        return $arr; 
    }
    
    /**
     * Takes an array and produces XML based on it.
     *
     * @param XMLWriter $xmlw       XMLWriter object that was previously instanted
     * and is used for creating the XML.
     * @param array     $data       Array to be converted to XML.
     * @param string    $defaultTag Default XML tag to be used if none specified.
     * 
     * @return void
     */
    private function _arr2xml(\XMLWriter $xmlw, $data, $defaultTag = null)
    {
        foreach ($data as $key => $value) {
            if ($key === Resources::XTAG_ATTRIBUTES) {
                foreach ($value as $attributeName => $attributeValue) {
                    $xmlw->writeAttribute($attributeName, $attributeValue);
                }
            } else if (is_array($value)) {
                if (!is_int($key)) {
                    if ($key != Resources::EMPTY_STRING) {
                        $xmlw->startElement($key);
                    } else {
                        $xmlw->startElement($defaultTag);
                    }
                }
                
                $this->_arr2xml($xmlw, $value);
                
                if (!is_int($key)) {
                    $xmlw->endElement();
                }
            } else {
                $xmlw->writeElement($key, $value);
            }
        }
    }

    /**
     * Gets the attributes of a specified object if get attributes 
     * method is exposed. 
     *
     * @param object $targetObject The target object. 
     * @param array  $methodArray  The array of method of the target object.
     * 
     * @return mixed
     */
    private static function _getInstanceAttributes($targetObject, $methodArray)
    {
        foreach ($methodArray as $method) {
            if ($method->name == 'getAttributes') {
                $classProperty = $method->invoke($targetObject);
                return $classProperty;
            }
        }
        return null;
    }

    /** 
     * Serialize an object with specified root element name. 
     * 
     * @param object $targetObject The target object. 
     * @param string $rootName     The name of the root element. 
     * 
     * @return string
     */
    public static function objectSerialize($targetObject, $rootName)
    {
        Validate::notNull($targetObject, 'targetObject');
        Validate::isString($rootName, 'rootName');
        $xmlWriter = new \XmlWriter();
        $xmlWriter->openMemory(); 
        $xmlWriter->setIndent(true);
        $reflectionClass = new \ReflectionClass($targetObject);
        $methodArray     = $reflectionClass->getMethods();
        $attributes      = self::_getInstanceAttributes(
            $targetObject, 
            $methodArray
        );
         
        $xmlWriter->startElement($rootName);
        if (!is_null($attributes)) {
            foreach (array_keys($attributes) as $attributeKey) {
                $xmlWriter->writeAttribute(
                    $attributeKey, 
                    $attributes[$attributeKey]
                );
            }
        }

        foreach ($methodArray as $method) {
            if ((strpos($method->name, 'get') === 0) 
                && $method->isPublic() 
                && ($method->name != 'getAttributes')
            ) {
                $variableName  = substr($method->name, 3);
                $variableValue = $method->invoke($targetObject);
                if (!empty($variableValue)) {
                    if (gettype($variableValue) === 'object') {
                        $xmlWriter->writeRaw(
                            XmlSerializer::objectSerialize(
                                $variableValue, $variableName
                            )
                        );
                    } else {
                        $xmlWriter->writeElement($variableName, $variableValue);
                    } 
                }
            }
        } 
        $xmlWriter->endElement();
        return $xmlWriter->outputMemory(true);
    }

    /**
     * Serializes given array. The array indices must be string to use them as
     * as element name.
     * 
     * @param array $array      The object to serialize represented in array.
     * @param array $properties The used properties in the serialization process.
     * 
     * @return string
     */
    public function serialize($array, $properties = null)
    {
        $xmlVersion   = '1.0';
        $xmlEncoding  = 'UTF-8';
        $standalone   = Utilities::tryGetValue($properties, self::STANDALONE);
        $defaultTag   = Utilities::tryGetValue($properties, self::DEFAULT_TAG);
        $rootName     = Utilities::tryGetValue($properties, self::ROOT_NAME);
        $docNamespace = Utilities::tryGetValue(
            $array,
            Resources::XTAG_NAMESPACE,
            null
        );

        if (!is_array($array)) {
            return false;
        }

        $xmlw = new \XmlWriter();
        $xmlw->openMemory();
        $xmlw->setIndent(true);
        $xmlw->startDocument($xmlVersion, $xmlEncoding, $standalone);
        
        if (is_null($docNamespace)) {
            $xmlw->startElement($rootName);
        } else {
            foreach ($docNamespace as $uri => $prefix) {
                $xmlw->startElementNS($prefix, $rootName, $uri);
                break;
            }
        }
        
        unset($array[Resources::XTAG_NAMESPACE]);
        self::_arr2xml($xmlw, $array, $defaultTag);

        $xmlw->endElement();

        return $xmlw->outputMemory(true);
    }
    
    /**
     * Unserializes given serialized string.
     * 
     * @param string $serialized The serialized object in string representation.
     * 
     * @return array
     */
    public function unserialize($serialized)
    {
        $sxml = new \SimpleXMLElement($serialized);

        return $this->_sxml2arr($sxml);
    }
}


