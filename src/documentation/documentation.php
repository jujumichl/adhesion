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

    $content = preg_replace("/\r\n|\r|\n/", '<br/>', $content);

return $content;
}