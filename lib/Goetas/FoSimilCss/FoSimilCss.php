<?php 
namespace Goetas\FoSimilCss;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Symfony\Component\CssSelector\CssSelector;

class FoSimilCss {
	
	public function applyCssDocument(DOMDocument $xml, DOMDocument $css) {
		$xpath = new DOMXPath($xml);
		$xpath->registerNamespace("css", "http://goetas.com/fo/css");
		
		$this->applyRules($xpath, $css);
	}
	public function applyCss(DOMDocument $xml, $css) {
		$cssDoc = new DOMDocument("1.0", "UTF-8");
		$cssDoc->load($css);
		$this->applyCssDocument($xml, $cssDoc);
	}
	public function applyAll(DOMDocument $xml) {
		$xpath = new DOMXPath($xml);
		$xpath->registerNamespace("css", "http://goetas.com/fo/css");
		
		$rulesNodes = iterator_to_array($xpath->query("//css:rules[@location]"));
		
		foreach ( $rulesNodes as $rules){
			$css = new DOMDocument("1.0", "UTF-8");
			if($css->load($rules->getAttribute("location"))){
				$this->applyRules($xpath, $css);
			}
			$rules->parentNode->removeChild($rules);
		}
	}
	/**
	 * @see http://www.w3.org/TR/CSS2/cascade.html#specificity
	 * @param array $rules
	 * @return array
	 */
	protected function sortRules(array $rules) {
		uasort($rules, function ($r1, $r2) {
			
			$s1 = $r1->getAttribute("css-match");
			$s2 = $r2->getAttribute("css-match");
			
			if($s2 && $s2){
				$a = 0*1000;
				$b = substr_count($s1, "#")*100;
				$c = (substr_count($s1, ".")*10)+(substr_count($s1, ":")*10);
				$d = substr_count(preg_replace('/\s+/', " ", trim($s1)), " ")*1;
				$c1 = $a+$b+$c+$d;
				
				
				$a = 0*1000;
				$b = substr_count($s2, "#")*100;
				$c = (substr_count($s2, ".")*10)+(substr_count($s2, ":")*10);
				$d = substr_count(preg_replace('/\s+/', " ", trim($s2)), " ")*1;
				$c2 = $a+$b+$c+$d;
				return $c1-$c2;
			}
			return 0;
		});
		return $rules;
	}
	protected function applyRules(DOMXPath $domXpath, DOMDocument $css) {
		$cssXpath = new DOMXPath($css);
		$cssXpath->registerNamespace("css", "http://goetas.com/fo/css");
	
		
		$rules = iterator_to_array($cssXpath->query("/css:css/css:rule"));
		
		foreach ($this->sortRules($rules) as $rule){
							
			foreach ($this->getAllParentNs($rule) as $uri){
				if($prefix = $rule->lookupPrefix($uri)){
					$cssXpath->registerNamespace($prefix, $uri);
				}
			}	

			if($rule->hasAttribute("match")){
				$xpath = "//".$rule->getAttribute("match");
			}else{
				$xpath = CssSelector::toXPath($rule->getAttribute("css-match"));
				$xpath = str_replace("@class", "@role", $xpath);
			}
			
			foreach ($domXpath->query($xpath) as $nodo){
				foreach ($rule->attributes as $attNode){
					if($attNode->name!=="match" && $attNode->name!=="css-match"){
						if($nodo->hasAttributeNs($attNode->namespaceURI, $attNode->localName) && $attr = $nodo->getAttributeNodens($attNode->namespaceURI, $attNode->localName)){
							$attr->value = $attNode->value;
						}else{
							$nodo->setAttributeNode ($domXpath->document->importNode($attNode));
						}
					}
				}
			}
		}
	}
	protected function getAllParentNs(DOMElement $element) {
		$namespaces=array("fo"=> "http://www.w3.org/1999/XSL/Format");

		$namespaces [$element->namespaceURI] = $element->namespaceURI;
		foreach ($element->attributes as $attribute){
			$namespaces[$element->namespaceURI] = $element->namespaceURI;
		}
		
		if($this->parentNode->nodeType == \XML_ELEMENT_NODE && $this->parentNode instanceof DOMElement ){
			foreach ($this->getAllParentNs($this->parentNode) as $ns){
				$namespaces[$ns]=$ns;
			}
		}
		return $namespaces;
	}
}