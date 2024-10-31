<?php
function is_session_started()
{
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}
if ( is_session_started() === FALSE ) session_start();
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);
if(!isset($_SESSION['T2SteamID64']))
{
	echo 'You must be logged-in to access this page.';
	echo /** @lang text */
	'<script type="text/javascript">
		function closeWindow() {
			setTimeout(function() {
				window.close();
			}, 5000);
		}
		window.onload = closeWindow();
	</script>';
	exit();
}
require './api/paypal/paypal.php';
$id = 'put here your id from https://developer.paypal.com/developer/applications REST API apps';
$secret = 'put here your secret from https://developer.paypal.com/developer/applications REST API apps';

$paypal = new Paypal($id, $secret, false);
if(isset($_GET['success'])){
    if($_GET['success'] == 'true'){
        $result = $paypal->executePayment();
        if($result == false){
            echo 'There is a problem with you payment, please contact an Administrator using the form on home page.';
        }else{
            echo /** @lang text */
            'Thanks you for your payment, you can now take your items using the ingame command. (/claim)<br /><br />';
        }
		echo /** @lang text */
		'<script type="text/javascript">
			function closeWindow() {
				setTimeout(function() {
					window.close();
				}, 5000);
			}
			window.onload = closeWindow();
		</script>';

    }elseif($_GET['success'] == 'false'){
        $paypal->cancelPayment();
        echo 'Your payment has been cancelled.';
        echo /** @lang text */
        '<script type="text/javascript">
 function closeWindow() {
     setTimeout(function() {
         window.close();
     }, 5000);
 }

    window.onload = closeWindow();
    </script>';
    }
    exit();
}elseif(isset($_GET['Status'])){
    $status = $paypal->getPaymentStatus();

    print($status);
    exit();
}
if(isset($_POST) && !empty($_POST)){
	$discount = 0;
	$total = 0;
	$disc = false;
	for($i =0; $i < count($_POST); $i++){
		$array = json_decode($_POST[$i]);
		if(strtolower($array->name) == 'discount'){
			$discount = $array->Price;
			$disc = true;
		}
		else{
			$total += $array->Price;
			$item[$i] = [
			'itemname' => $array->name,
			'num' => $array->id,
			'amount' => $array->Amount,
			'price' => $array->Price
			];
			if(isset($array->Include) && $array->Include == true){
				$item[$i]['includedQuantity'] = true;
			}else{
				$item[$i]['includedQuantity'] = false;
			}
		}
	}
	if($disc)
	{
		$item[count($item)] = [
		'itemname' => 'Discount',
		'num' => 0,
		'amount' => 1,
		'price' => $total * ($discount/100)
		];
	}

    $items = [$item];
    try{
        $url = $paypal->createPayment($items, true);
    }catch(PayPal\Exception\PayPalConnectionException $pce){
        echo $ex->getCode(); // Prints the Error Code
        echo $ex->getData(); // Prints the detailed error message
        die($ex);
    } catch (Exception $ex) {
        die($ex);
    }
    
	header('Location: ' . $url);
}
if((!isset($_GET['item']) || !isset($_GET['num']) || !isset($_GET['amount']) || !isset($_GET['price'])) && !isset($_GET['Status'])){
}else{
	if(isset($_GET['discount']))
		$price = $_GET['discount'];
    $item[0] = [
        'itemname' => $_GET['item'],
        'num' => $_GET['num'],
        'amount' => $_GET['amount'],
        'price' => $_GET['price']
    ];
	if(isset($_GET['discount']))
	{
		$item[1] = [
			'itemname' => 'Discount',
			'num' => 0,
			'amount' => 1,
			'price' => $price
		];
	}

    if(isset($_GET['included']) && $_GET['included'] == true){
        $item[0]['includedQuantity'] = true;
    }else{
        $item[0]['includedQuantity'] = false;
    }
    $items = [$item];
    try{
        $url = $paypal->createPayment($items, false);
    }catch(PayPal\Exception\PayPalConnectionException $pce){
        echo $ex->getCode(); // Prints the Error Code
        echo $ex->getData(); // Prints the detailed error message
        die($ex);
    } catch (Exception $ex) {
        die($ex);
    }
	
    header('Location: ' . $url);
}