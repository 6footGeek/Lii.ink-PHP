<?php
function getTheFuckingTitle($website) {

$page = '$website';

    // tags
    $start = '<title>';
    $end = '<\/title>';

    // open the file
    $fp = fopen( $page, 'r' );

    $cont = "";

    // read the contents
    while( !feof( $fp ) ) {
        $buf = trim( fgets( $fp, 4096 ) );
        $cont .= $buf;
    }
    
    // get tag contents
    preg_match( "/$start(.*)$end/s", $cont, $match );

    // tag contents
    $contents = $match[ 1 ]; 
return $contents
}

getTheFuckingTitle("http://6footgeek.com");

?>

<?php

echo $contents;
?>