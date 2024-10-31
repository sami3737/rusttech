<?php
require __DIR__ . './api/SourceQuery/bootstrap.php';
use xPaw\SourceQuery\SourceQuery;
define( 'SQ_SERVER_ADDR', '127.0.0.1' );
define( 'SQ_SERVER_PORT', 28015 ); // udp
define( 'SQ_RCON_PORT', 28016 ); // tcp - only for SourceQuery::SOURCE
define( 'SQ_TIMEOUT',     2 );
define( 'SQ_ENGINE',      SourceQuery::SOURCE );

if(isset($_POST['cat']) && !empty($_POST['cat'])){
    $cat = $_POST['cat'];
    $Query = new SourceQuery( );
    $ItemPrice = NULL;
    try
    {
        $Query->Connect( SQ_SERVER_ADDR, SQ_SERVER_PORT, SQ_RCON_PORT, SQ_TIMEOUT, SQ_ENGINE );
        $ItemPrice = $Query->GetPrice($cat);
    }
    catch( Exception $e )
    {
        echo $e->getMessage( );
    }
    $json = '';
    $Query->Disconnect( );

    $json = array();

    if(!isset($ItemPrice['ERROR'])){
        for($i=0;$i<count($ItemPrice); $i++){

            $result = array(
                'UniqueNumber' => $ItemPrice[$i]['UniqueNumber'],
                'ItemName' => $ItemPrice[$i]['ItemName'],
                'Price' => $ItemPrice[$i]['Price'],
                'Amount' => $ItemPrice[$i]['Amount'],
                'Include' => $ItemPrice[$i]['Include'],
                'IsSold' => $ItemPrice[$i]['IsSold']
            );
            array_push($json, $result);
        }
    }
    print_r(json_encode($json));
}
