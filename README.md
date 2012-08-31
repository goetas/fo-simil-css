fo-simil-css
============

Simil CSS for XSL FO. Uses XPath instead of CSS selectors.

To use CSS selectors install also https://github.com/symfony/CssSelector


Example:

```php

$xml = new DOMDocument("1.0", "UTF-8");
$xml->load("file.fo");

$css = new FoSimilCss();
$css->applyCss($xml, "style.xml");

```


Stylesheet example

```xml
<css:css
 xmlns:css="http://goetas.com/fo/css"
 xmlns:fo="http://www.w3.org/1999/XSL/Format"
 >
    <!-- similar to CSS element selector (h1, div...) -->
    <css:rule 
        match="fo:flow"
        font-family="sans-serif"  
        font-size="10pt"      
    />
   
    <!-- similar to CSS id selector (#header) -->
    <css:rule 
        match="*[@id='header']"
        font-size="xx-small"
        
    />  
    <!-- more complex XPath -->
    <css:rule 
        match="fo:table[@id='data']/fo:table-header/fo:table-row/fo:table-cell/fo:block"
        border-bottom="1pt solid red"
        font-weight="bold"        
    />
    
    <!-- Same examples but using  **Symfony\Component\CssSelector\CssSelector** CSS selector engine -->
    
    <css:rule 
        css-match="fo|flow"
        font-family="sans-serif"  
        font-size="10pt"      
    />
    <css:rule 
        css-match="#header"
        font-size="xx-small"
        
    />  
    <css:rule 
        css-match="fo|table#data > fo|table-header > fo|table-row > fo|table-cell > fo|block"
        border-bottom="1pt solid red"
        font-weight="bold"        
    />
</css:css>

```
