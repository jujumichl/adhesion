<?php
function getDocumentation() {
    $output="";
    $output.="Page de documentation";

    $filename = "README.md";
    $lines = array();
    $fp = fopen($filename, "r");

    if(filesize($filename) > 0){
        $content = fread($fp, filesize($filename));
        // $lines = explode("\n", $content);
        fclose($fp);
    }

    include_once 'Parsedown.php';
    $parsedown = new Parsedown();
    $parsedown->setSafeMode(true);		// This will escape HTML link <a href=""> into html entities but markdown links are ok
    
    // Because HTML will be HTML entity encoded, we replace tag we want to keep
    $content = preg_replace('/<span style="([^"]+)">/', '<!-- SPAN_STYLE_\1 -->', $content);
    $content = preg_replace('/<\/span>/', '<!-- SPAN_END -->', $content);
    
    $content = $parsedown->text($content);
    
    $content = preg_replace('/&lt;!-- SPAN_STYLE_([^-]+) --&gt;/', '<span style="\1">', $content);
    $content = preg_replace('/&lt;!-- SPAN_END --&gt;/', '</span>', $content);
    

    $content = '<div class="mddoc">'.$content. '</div>'; 
   // $content = preg_replace("/\r\n|\r|\n/", '<br/>', $content);

return $content;
}




