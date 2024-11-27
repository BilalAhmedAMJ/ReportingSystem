<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This code is released under the GNU LGPL Go read it over here:       |
// | http://www.gnu.org/copyleft/lesser.html                              |
// +----------------------------------------------------------------------+
// | Authors: Mathias Sulser <suls@suls.org>                              |
// +----------------------------------------------------------------------+
//
// $Id: class.xslt.php,v 1.6 2002/03/20 19:26:40 eeshq Exp $
//

/**
* Extensible Stylesheet Language Transformations - XSLTansformer
*
* With this class you can easy transform XML files to a HTML page.
* The only requirements are a a XML parser (i.e Expat) and a XSLT
* processor (Sablotron).
*
* @author   Mathias Sulser <suls@suls.org>
* @access   public
* @version  $Id: class.xslt.php,v 1.6 2002/03/20 19:26:40 eeshq Exp $
* @package  XSLT
*/

class XSLTransformer {
    /**
    * XSLT processor resource
    * @var    mixed      $_processor
    */
    var $_processor;

    /**
    * Contains the XSL-data from a file or variable
    * @var    string     $_xsl
    */
    var $_xsl;
    
    /**
    * Contains the XML-data from a file or variable
    * @var    string     $_xml
    */
    var $_xml;

    /**
    * The parameters can be accessed in the XSL file
    * @var    array      $_params
    */
    var $_params    = array();
    
    /**
    * Arguments for the XSLT process
    * @var    array      $_arguments
    */
    var $_arguments = array();
    
    /**
    * Contains all Errors of the parsing process
    * @var    array      $_error
    */
    var $_error     = array();
    
    /**
    * Output of the parsed XML and XSL files
    * @var    string     $_output
    */
    var $_output;

    /**
    * That's the constructor of this class.
    *
    * @param  string    $xml      XML data (file or variable)
    * @param  string    $xsl      XSL data (file or variable)
    * @param  array     $params   Parameters for the XSL file
    * @access public
    */
    function XSLTransformer($xml, $xsl, $params = array()) {
        $this->_processor = xslt_create();
        
        $this->_setVars($xml, $xsl, $params);
        $this->_setArguments();
        
        $this->_transform();
    }

    /**
    * Destructor who frees the used memory
    *
    * @access public
    */
    function destructXSLTansform() {
        xslt_free($this->_processor);
        
        unset($this->_xsl);
        unset($this->_xml);
        unset($this->_params);
        unset($this->_arguments);
        unset($this->_output);
        unset($this->_processor);
    }

    /**
    * Sets the output
    *
    * @access private
    * @param  mixed
    */
    function _setOutput($string) {
        $this->_output = $string;
    }

    /**
    * Returns the result of the transform process
    *
    * @access public
    * @return mixed   $_output
    */
    function getOutput() {
        return $this->_output;
    }

    /**
    * Stores the XML and XSL data
    *
    * @param  string  $xmlData     XML data (File or Variable)
    * @param  string  $xslData     XSL data (File or Variable)
    * @param  array   $parameters  Parameters for the XSL file
    * @access private
    */
    function _setVars($xmlData, $xslData, $parameters = array()) {
        if(file_exists($xmlData)) {
            $this->_xml = join('', file($xmlData));
        } else {
            $this->_xml = $xmlData;
        }
        
        if(file_exists($xslData)) {
            $this->_xsl = join('', file($xslData));
        } else {
            $this->_xsl = $xslData;
        }
        
        $this->_params = $parameters;
    }
    

    /**
    * Sets the value of $this->_arguments
    *
    * @access private
    */
    function _setArguments() {
        $this->_arguments = array (
            "/_xml" => $this->_xml,
            "/_xsl" => $this->_xsl
        );
    }

    /**
    * Returns the value of $this->_arguments
    *
    * @access private
    * @return array $_arguments
    */
    function _getArguments() {
        return $this->_arguments;
    }

    /**
    * Returns the value of $this->_params
    *
    * @access private
    * @return array $_params
    */
    function _getParameters() {
        return $this->_params;
    }

    /**
    * Starts the transform process
    *
    * @access private
    */
    function _transform() {
        if(empty($this->_params)) {
            $result = @xslt_process($this->_processor, 'arg:/_xml', 'arg:/_xsl', NULL, $this->_getArguments());
        } else {
            $result = @xslt_process($this->_processor, 'arg:/_xml', 'arg:/_xsl', NULL, $this->_getArguments(), $this->_getParameters());
        }
        
        if($result) {
            $this->_setOutput($result);
        } else {
            $this->_setError("Cannot process XSLT document ".xslt_errno($this->_processor).": ".xslt_error($this->_processor));
            $this->_setOutput($this->_getError());
        }
    }

    /**
    * Creates a new error and stores the older ones
    *
    * @access private
    * @param  string $string Set the error description
    */
    function _setError($string) {
        if(sizeof($this->_error)) {
            $newError = sizeof($this->_error);
            $this->_error[$newError] = $string;
        } else {
            $this->_error[0] = $string;
        }
    }

    /**
    * Makes the errors ready for print
    *
    * @return string        Set's the errors ready for output
    * @access private
    */
    function _getError() {
        foreach ($this->_error as $value) {
            $help[] = "<b>Error </b>: $value\n";
        }
        return implode("<br>", $help);
    }

} // end of class XSLTransformer
?>