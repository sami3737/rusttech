<?php

require_once __DIR__ . '/autoload.php';
require './api/mysql/Db.class.php';

use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

define("LOG_FILE", "./api/logging.log");

class Paypal {

    private $apiContext = null;

    public function __construct($clientId, $clientSecret, $sandbox = false) {
        $this->apiContext = new ApiContext(new OAuthTokenCredential(
            $clientId,
            $clientSecret
        ));
        $this->apiContext->setConfig([
            'mode' => $sandbox ? 'sandbox' : 'live'
        ]);
    }

    /**
     * @param $items - array of items
     * @return string - the URL
     */
    public function createPayment($items, $cart = false) {
		$discount = 0;
        $userId = $_SESSION['T2SteamID64'];
        $db = new \DB();
		if($cart){
			$itemarray = array();
			$amountarray = array();
			for($i = 0; $i < count($items); $i++){
				$itemdetail = $items[0];
				$prices = 0;
				$amounts = 0;
				for($ii = 0; $ii < count($itemdetail); $ii++){
					$amounts += $itemdetail[$ii]['amount'];
					$prices += $itemdetail[$ii]['price'];
					array_push($itemarray, $itemdetail[$ii]['itemname']);
					array_push($amountarray, $itemdetail[$ii]['amount']);
				}
				$itemgen = implode(",", $itemarray);
				$amountgen = implode(",", $amountarray);
				$insert = $db->query("INSERT INTO `payment`(`PaymentID`, `Status`, `AccountUniqueNumber`, `Type`, `Price`, `ItemName`,`Amount`, `AmountSerie`) VALUES (uuid(), :Status,:SteamId,:Type,:Price,:ItemName,:Amount, :AmountSerie)", Array('Status' => 'Prepared', 'SteamId' => $userId, 'Type' => 'Paypal', 'Price' => $prices, 'ItemName' => $itemgen, 'Amount' => $amounts, 'AmountSerie' => $amountgen));
				if(isset($insert['ERROR'])){
					error_log(print_r($insert['ERROR']) . PHP_EOL, 3, LOG_FILE);
				}
				$result = $db->row("Select PaymentID FROM Payment ORDER BY Date DESC");
				$paymentId[$i] = $result['PaymentID'];
			}
		}else{
			$itemgen = '';
			$amountgen = 0;
			for($i = 0; $i < count($items); $i++){
				$itemdetail = $items[0];
				for($ii = 0; $ii < count($itemdetail); $ii++){
					if($itemdetail[$ii]['itemname'] != 'Discount'){
						$itemgen = $itemdetail[$ii]['itemname'];
						$amountgen = $itemdetail[$ii]['amount'];
						$amounts = $itemdetail[$ii]['amount'];
						$prices = $itemdetail[$ii]['price'];
					}else{
						$discount = $itemdetail[$ii]['price'];
					}
				}
			}
			$insert = $db->query("INSERT INTO `payment`(`PaymentID`, `Status`, `AccountUniqueNumber`, `Type`, `Price`, `ItemName`,`Amount`) VALUES (uuid(), :Status,:SteamId,:Type,:Price,:ItemName,:Amount)", Array('Status' => 'Prepared', 'SteamId' => $userId, 'Type' => 'Paypal', 'Price' => $prices, 'ItemName' => $itemgen, 'Amount' => $amounts));
			if(isset($insert['ERROR'])){
				error_log(print_r($insert['ERROR']) . PHP_EOL, 3, LOG_FILE);
			}
			$result = $db->row("Select PaymentID FROM Payment ORDER BY Date DESC");
			$paymentId[0] = $result['PaymentID'];
		}

 
        $payer = new Payer();
        $payer->setPaymentMethod("paypal"); // credit_card

        $itemList = new ItemList();
        $totalPrice = 0.0;
		if($cart){
			for($i = 0; $i < count($items); $i++){
				$itemdetail = $items[0];
				for($ii = 0; $ii < count($itemdetail); $ii++){
					$item1 = new Item();
					$item1->setName($itemdetail[$ii]['itemname'])
						->setCurrency("EUR")
						->setQuantity($itemdetail[$ii]['amount'])
						->setSku($itemdetail[$ii]['num']);
					if($itemdetail[$ii]['includedQuantity']){
						$totalPrice += $itemdetail[$ii]['price'];
						$item1->setPrice(($itemdetail[$ii]['price'] / $itemdetail[$ii]['amount']).'');
					}else{
						$totalPrice += $itemdetail[$ii]['price'] * $itemdetail[$ii]['amount'];
						$item1->setPrice($itemdetail[$ii]['price']);
					}
					$itemList->addItem($item1);
				}
			}
		}else{
			for($i = 0; $i < count($items); $i++){
				$itemdetail = $items[0];
				for($ii = 0; $ii < count($itemdetail); $ii++){
					if($itemdetail[$ii]['itemname'] != 'Discount'){
						$item1 = new Item();
						$item1->setName($itemdetail[$ii]['itemname'])
							->setCurrency("EUR")
							->setQuantity($itemdetail[$ii]['amount'])
							->setSku($itemdetail[$ii]['num']);
						if($itemdetail[$ii]['includedQuantity']){
							$totalPrice += $itemdetail[$ii]['price'];
							$item1->setPrice(($itemdetail[$ii]['price'] / $itemdetail[$ii]['amount']).'');
						}else{
							$totalPrice += $itemdetail[$ii]['price'] * $itemdetail[$ii]['amount'];
							$item1->setPrice($itemdetail[$ii]['price']);
						}
						$itemList->addItem($item1);
					}else{
						$price = ($itemdetail[0]['price'])*($discount/100);
						$item1 = new Item();
						$item1->setName($itemdetail[$ii]['itemname'])
							->setCurrency("EUR")
							->setQuantity($itemdetail[$ii]['amount'])
							->setSku($itemdetail[$ii]['num']);
						if($itemdetail[$ii]['includedQuantity']){
							$totalPrice += $itemdetail[$ii]['price'];
							$item1->setPrice(-$price.'');
						}else{
							$totalPrice += -$price;
							$item1->setPrice(-$price);
						}
						$itemList->addItem($item1);
					}
				}
			}
		}
        $amount = new Amount();
        $amount->setCurrency("EUR")
            ->setTotal($totalPrice.'');
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment for ". $_SESSION['steam']['response']['players'][0]['personaname'])
            ->setInvoiceNumber($paymentId[0]);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("http://www.rust-evolution.net/shop.php?success=true")
            ->setCancelUrl("http://www.rust-evolution.net/shop.php?success=false");

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        try {
            $payment->create($this->apiContext);
        }catch(PayPal\Exception\PayPalConnectionException $pce){
            echo $pce->getCode(); // Prints the Error Code
            echo $pce->getData(); // Prints the detailed error message
            die($pce);
        } catch (Exception $ex) {
            die($ex);
            return; //failed
        }

        $approvalUrl = $payment->getApprovalLink();
        $paypalPaymentId = $payment->getId();
        $db->query("UPDATE Payment SET `Price` = :Price, `PaypalPaymentID` = :PaypalPaymentID WHERE `PaymentID` = :PaymentID", Array('Price' => $totalPrice, 'PaypalPaymentID' => $paypalPaymentId,'PaymentID' => $paymentId[0]));
        $_SESSION['PaypalpaymentID'] = $paypalPaymentId;
        $_SESSION['paymentID'] = $paymentId[0];

        return $approvalUrl;
    }

    public function executePayment() {

        $paymentId[0] = $_SESSION['PaypalpaymentID'];

        $db = new \DB();

        $payerId = $_GET['PayerID'];

        $payment = Payment::get($paymentId[0], $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $payment->execute($execution, $this->apiContext);

            $db->query("UPDATE Payment SET Status = :Status WHERE PaymentID = :PaymentID", Array('Status' => 'Pending', 'PaymentID' => $_SESSION['paymentID']));

            try {
                $payment = Payment::get($paymentId[0], $this->apiContext);
            } catch(Exception $ex) {
                error_log(date('[Y-m-d H:i e] '). "Error code : " . $ex->getCode() . "\r\nError message : " . $ex->getMessage() . PHP_EOL, 3, LOG_FILE);
                $db->query("UPDATE Payment SET Status = :Status WHERE PaymentID = :PaymentID", Array('Status' => 'GetFailed', 'PaymentID' => $_SESSION['paymentID']));
                return false; //failed
            }
        } catch(Exception $ex) {
            $db->query("UPDATE Payment SET Status = :Status WHERE PaymentID = :PaymentID", Array('Status' => 'ExecFailed', 'PaymentID' => $_SESSION['paymentID']));
            error_log(date('[Y-m-d H:i e] '). "Error code : " . $ex->getCode() . "\r\nError message : " . $ex->getMessage() . PHP_EOL, 3, LOG_FILE);
            return false; //failed
        }

        $db->query("UPDATE Payment SET Status = :Status WHERE PaymentID = :PaymentID", Array('Status' => 'Complete', 'PaymentID' => $_SESSION['paymentID']));

        return $payment;
    }

    public function cancelPayment(){
        $db = new \DB();
        $db->query("UPDATE Payment SET Status = :Status WHERE PaymentID = :PaymentID", Array('Status' => 'cancelled', 'PaymentID' => $_SESSION['paymentID']));
    }

    public function getPaymentStatus(){
        $paymentId[0] = $_SESSION['PaypalpaymentID'];        
        try {
            $payment = Payment::get($paymentId[0], $this->apiContext);
        } catch(Exception $ex) {
            error_log(date('[Y-m-d H:i e] '). "Error code : " . $ex->getCode() . "\r\nError message : " . $ex->getMessage() . PHP_EOL, 3, LOG_FILE);
            return false; //failed
        }

        return $payment;
    }
}