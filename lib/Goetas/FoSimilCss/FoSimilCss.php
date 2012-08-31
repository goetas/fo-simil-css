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
	protected function applyRules(DOMXPath $domXpath, DOMDocument $css) {
		$cssXpath = new DOMXPath($css);
		$cssXpath->registerNamespace("css", "http://goetas.com/fo/css");
	
		foreach ($cssXpath->query("/css:css/css:rule") as $rule){
							
			foreach ($this->getAllParentNs($rule) as $uri){
				if($prefix = $rule->lookupPrefix($uri)){
					$cssXpath->registerNamespace($prefix, $uri);
				}
			}	
			
			
			if($rule->hasAttribute("match")){
				$xpath = $rule->getAttribute("match");
			}else{
				$xpath = CssSelector::toXPath($rule->getAttribute("css-match"));
			}
			
			foreach ($domXpath->query("//$xpath") as $nodo){
				foreach ($rule->attributes as $attNode){
					if($attNode->name!=="match" && $attNode->name!=="css-match"){
						$nodo->setAttributeNode ($domXpath->document->importNode($attNode));
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