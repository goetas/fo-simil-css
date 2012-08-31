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
   <!-- elements -->
    <css:rule 
        css-match="fo|flow"
        font-family="sans-serif"  
        font-size="10pt"      
    />
    <!-- id selector -->
    <css:rule 
        css-match="#header"
        font-size="xx-small"
        
    />
    <!-- role selector (like @class) -->
    <css:rule 
        css-match=".header"
        font-size="xx-small"
        
    />    
    <!-- more complicated rule-->
    <css:rule 
        css-match="fo|table#data > fo|table-header.myclass > * > fo|table-cell > fo|block:last-child"
        border-bottom="1pt solid red"
        font-weight="bold"        
    />
</css:css>

```
