<?php

class Google_XmlParserUtil {
  /**
   * convert xml string to php array - useful to get a serializable value
   *
   * @param string $xmlstr 
   * @return array
   * @author Adrien aka Gaarf
   */
  public static function XmlToArray($xmlstr) {
    $doc = new DOMDocument();
    $doc->loadXML($xmlstr);
    return self::XmlDOMToArray($doc->documentElement);
  }
  
  public static function XmlDOMToArray($node) {
    $output = array();
    switch ($node->nodeType) {
      case XML_CDATA_SECTION_NODE:
      case XML_TEXT_NODE:
        $output = trim($node->textContent);
        break;
      case XML_ELEMENT_NODE:
        for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
          $child = $node->childNodes->item($i);
          $v = self::XmlDOMToArray($child);
          if(isset($child->tagName)) {
            $t = $child->tagName;
            if(!isset($output[$t])) {
              $output[$t] = array();
            }
            $output[$t][] = $v;
          }
          elseif($v) {
            $output = (string) $v;
          }
        }
        if(is_array($output)) {
          if($node->attributes->length) {
            $a = array();
            foreach($node->attributes as $attrName => $attrNode) {
              $a[$attrName] = (string) $attrNode->value;
            }
            $output['@attributes'] = $a;
          }
          foreach ($output as $t => $v) {
            if(is_array($v) && count($v)==1 && $t!='@attributes') {
              $output[$t] = $v[0];
            }
          }
        }
        break;
    }
	
    return $output;
  }
}
