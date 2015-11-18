<?php
/**
 * File containing SQLIImportHandlerOptions
 * @copyright Copyright (C) 2010 - SQLi Agency. All rights reserved
 * @licence http://www.gnu.org/licenses/gpl-2.0.txt GNU GPLv2
 * @author Jerome Vieilledent
 * @version @@@VERSION@@@
 * @package sqliimport
 * @subpackage sourcehandlers
 */

/**
 * Options class for import handlers.
 * An object of this class will be provided to each import handler when running import
 */
class SQLIImportHandlerOptions extends SQLIImportOptions
{
    const OPTION_HANDLER_DELIMITER = '|',
          OPTION_VALUE_DELIMITER = ',',
          OPTION_HANDLERNAME_DELIMITER = '::';

    public function __construct( array $options = array() )
    {
        parent::__construct( $options );
    }

    /**
     * (non-PHPdoc)
     * @see extension/sqliimport/classes/options/SQLIImportOptions::__set()
     */
    public function __set( $name, $value )
    {
        $this->properties[$name] = $value;
    }
    
    /**
     * Generic toString method
     */
    public function __toString()
    {
        $aReturn = array();
        if( is_array( $this->properties ) )
        {
            foreach( $this->properties as $name => $value )
            {
                $aReturn[] = $name.' : '.(string)$value;
            }
        }
        
        return implode( "\n", $aReturn );
    }
    
    /**
     * Initializes an SQLIImportHandlerOptions object from text.
     * One option per line (optionName=optionValue)
     * @param $textOptions
     * @return SQLIImportHandlerOptions
     */
    public static function fromText( $textOptions )
    {
        $aOptionsLines = explode( "\n", $textOptions );
        $aOptions = array();
        foreach( $aOptionsLines as $optionLine )
        {
            $optionLine = trim( $optionLine );
            list( $optionName, $optionValue ) = explode( '=', $optionLine, 2 );
            $aOptions[$optionName] = $optionValue;
        }
        
        return new self( $aOptions );
    }
    
    /**
     * Returns a string representation of options, as used in forms
     * @return string
     */
    public function toText()
    {
        $text = '';
        foreach( $this->properties as $optionName => $optionValue )
        {
            $text .= $optionName.'='.$optionValue."\n";
        }
        
        $text = trim( $text );
        return $text;
    }
    
    /**
     * Decodes handlers options provided from the command line.
     * Returns an associative array with handler name as the key and an instance of SQLIImportHandlerOptions as value
     * @param string $optionLine Raw options for import handlers.
     *                           Should be something like --options="handler1::foo=bar,foo2=baz|handler2::someoption=biz"
     * @return SQLIImportHandlerOptions[]
     */
    public static function decodeHandlerOptionLine( $optionLine )
    {
        $aFinalOptions = array();
        
        if( $optionLine )
        {
            $aHandlersOption = explode( self::OPTION_HANDLER_DELIMITER, $optionLine );
            
            foreach( $aHandlersOption as $handlerOption )
            {
                list( $handlerName, $options ) = explode( self::OPTION_HANDLERNAME_DELIMITER, $handlerOption );
                $aParams = array();
                $aOptions = explode( self::OPTION_VALUE_DELIMITER, $options );
                foreach( $aOptions as $option )
                {
                    list( $paramName, $paramValue ) = explode( '=', $option );
                    $aParams[$paramName] = $paramValue;
                }
                
                $aFinalOptions[$handlerName] = new self( $aParams );
            }
        }
        
        return $aFinalOptions;
    }
}
