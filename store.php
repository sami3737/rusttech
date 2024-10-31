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

// Example
if ( is_session_started() === FALSE ) session_start();
require __DIR__ . '../api/SourceQuery/bootstrap.php';

use xPaw\SourceQuery\SourceQuery;

define( 'SQ_SERVER_ADDR', '127.0.0.1' );
define( 'SQ_SERVER_PORT', 28015 ); // udp
define( 'SQ_RCON_PORT', 28016 ); // tcp - only for SourceQuery::SOURCE
define( 'SQ_TIMEOUT',     2 );
define( 'SQ_ENGINE',      SourceQuery::SOURCE );

$Query = new SourceQuery( );
$items = '{}';

try
{
	$Query->Connect( SQ_SERVER_ADDR, SQ_SERVER_PORT, SQ_RCON_PORT, SQ_TIMEOUT, SQ_ENGINE );
	require_once __DIR__ . './api/RustIO/apikey.php';
	$items = $Query->GetItem($Rustapikey);
}
catch( Exception $e )
{
	echo $e->getMessage( );
}
?>
<!DOCTYPE HTML>
<!--
	Stellar by Pixelarity
	pixelarity.com | hello@pixelarity.com
	License: pixelarity.com/license
-->
<html>
<head>
	<title>RustTech - Store</title>
	<link rel="icon" type="image/png" href="images/favicon-white.png"></link>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!--[if lte IE 8]><script src="js/ie/html5shiv.js"></script><![endif]-->
	<link rel="stylesheet" href="css/main.css" />
	<!--[if lte IE 9]><link rel="stylesheet" href="css/ie9.css" /><![endif]-->
	<!--[if lte IE 8]><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
	<style type="text/css">
		#PPMiniCart{
			margin-top:500px;}
	</style>
	<link rel="stylesheet" href="css/shop.css" type="text/css">
	<script src="js/jquery.min.js"></script>
	<script src="js/underscore-min.js"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$( "#login" ).click(function(){
				$('#overlay').fadeIn("slow");
				$('.login-popup').show();
				$('.menu').attr('id', 'blur');
				$('.wrap').attr('id', 'blur');
			});

			$( "#overlay" ).click(function(){
				$('.wrap').removeAttr('id', 'blur');
				$('.login-popup').slideUp("slow");
				$('.close_popup').slideUp("slow");
				$('.msg-popup').slideUp("slow");
				$('.msg-popup-mod').slideUp("slow");
				$('.msg-popup_1').slideUp("slow");
				$('.vote-popup').slideUp("slow");
				$('#overlay').fadeOut("slow");
				$(".show").slideUp("slow");
				$(".show").removeClass('show');
				$("#serv_1_popup").slideUp("slow");
				$(".menu").removeAttr("id", "blur");
				$(".buy-popup").slideUp("slow");
				$('.cart-show').slideUp("slow");
			});

			$('.cart_view').click(function()
			{
				display_cart();
				$('.cart-show').show();
				$('#overlay').fadeIn("slow");
			});
		});
	</script>
</head>
<body>
<div id="overlay"></div>
<div class="cart-show" style="display:none;"></div>
<div class="buy-popup" style="display:none;"></div>
<!-- Wrapper -->
<div id="wrapper">
	<!-- Header -->
	<header id="header">
		<h1>Store</h1>
		<p>Resources, Ranks, and Kits</p>
	</header>
	<!-- Nav -->
	<nav id="nav">
		<ul>
			<li><a href="home.html">Home</a></li>
			<li><a href="home.html#contact">Contact</a></li>
			<li><a href="home.html#tc">Terms & Conditions</a></li>
			<li><a href="store.php" class="active">Store</a></li>
			<li><a href="http://playrust.io/map/?rusttech.net:28015">Live Map</a></li>
			<?php
			if(isset($_SESSION['T2SteamAuth'])){
				echo '<li><a href="?logout">Logout</a></li>';
			}
			else
				echo '<li><a id="login">Login</a></li>';
			?>
		</ul>
		<br/>
		<button class="button special fit small cart_view" name="submit">View Cart</button>
	</nav>
	<!-- Main -->
	<div id="main">
		<div class="login-popup" style="display:none;">
			<div class="log-left">
				<h4>Login</h4>
				<form id="login_form" method="POST">
					<?php
					require('api/login.php');
					?>
				</form>

			</div>

			<div class="log-right">
				<h4>Information</h4>
				<p>Login to the site will give you access to webshop.</p>
			</div>
		</div>
		<!-- Content -->
		<section id="content" class="main">
			<br>
			<p style="font-size: 18px">
				<strong>Please fill in where applicable before making a purchase</strong>
				<br>
				<br>
				<?php
				if(isset($_SESSION['T2SteamID64']))
				{
					?>
					<input type="text" required id="na" placeholder="SteamID or Username" style="width: 50%" readonly value='<?php echo $_SESSION['T2SteamID64']; ?>'>
					<?php
				}
				else
				{
					?>
					<input type="text" required id="na" placeholder="You must be connected." style="width: 50%" readonly>
					<?php
				}
				?>
			<div>
				<label>Discount code:</label>
				<input class="discount"></input><button><a href="javascript:CheckDiscount()">Valid code</a></button><br /><br />
				<input type="checkbox" id="terms" name="terms">
				<label for="terms">I agree to the <a href="home.html#tc">terms and conditions</a></label>
			</div>
			</p>
		</section>
		<div class="wrap">
			<div class="magazin-left">
				<ul class="cat">
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Items');">Items</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Resources');">Resources</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Ammunition');">Ammunition</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Weapon');">Weapon</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Attire');">Clothing</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Construction');">Structures</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Misc');">Other</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Food');">Food</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Medical');">Medic</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('Traps');">Traps</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('VIP');">VIP rank</a></li>
					<li><a href="javascript:void(0)" onclick="LoadMagazin('VIP+');">VIP+ rank</a></li>
				</ul>
			</div>
			<div class="magazin-cont">
				<font color="red">
					Completion:<br>
					- You agree that RustTech has nothing to do with your time lost while playing or money spent on RustTech. It's your own responsibility how long you play and where you play. All donate money you make are final, you can request a refund, but is completely up to RustTech to accept your request or deny it.<br>
					- By buying into this shop you accept the rule above!<br>
				</font>
			</div>
			<div id="overlay"></div>
		</div>
	</div>
	<!-- Footer -->
	<footer id="footer">
		<p class="copyright">&copy; RustTech.</p>
	</footer>
</div>
<!-- Scripts -->
<script src="js/jquery.scrollex.min.js"></script>
<script src="js/jquery.scrolly.min.js"></script>
<script src="js/skel.min.js"></script>
<script src="js/util.js"></script>
<!--[if lte IE 8]><script src="js/ie/respond.min.js"></script><![endif]-->
<script>
	var cart = [];
	var discunt = 0;
	opened = function(verb, url, data, target) {
		var form = document.createElement("form");
		form.action = url;
		form.method = verb;
		form.target = target || "_self";
		if (data) {
			for (var key in data) {
				var input = document.createElement("textarea");
				input.name = key;
				input.value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
				form.appendChild(input);
			}
		}
		form.style.display = 'none';
		document.body.appendChild(form);
		form.submit();
	};

	function buy(data){
		var paypalWindow = window.open('shop.php?item=' + encodeURI(data.name)
			+ '&price=' + encodeURI(data.Price)
			+ '&num=' + encodeURI(data.id)
			+ '&amount=' + encodeURI(data.Amount)
			+ (data.Include ? '&included=true' : '')
			+ (discunt != 0 ? '&discount='+discunt : ''), '_blank');
		// listen if the opened window has been closed, if yes just call the status
		var intervalNumber = setInterval(function(){
			if(paypalWindow.parent === null || paypalWindow.parent === undefined){
				clearInterval(intervalNumber);
				$.ajax({url: './shop.php?Status', success:function (result) {
					var json = JSON.parse(result);
					var message = '';
					if(json['transactions'].length != 0 && json['transactions'][0]['related_resources'][0].sale.state == 'completed'){
						message = 'Thanks for your payment, you can now take your reward using the ingame command.';
					}else{
						message = 'A problem appeared with your payment, please contact an administrator.';
					}
					message += '<div class="button" style="left: 51%;" id="check"><a href="javascript:continu()">Continue shopping</a></div>'
						+ '<div class="button" id="check"><a href="javascript:checkout()">Process to checkout</a></div>';

					$('#overlay').fadeIn('slow');
					$('.buy-popup').html(message)
						.slideDown('slow');
					$(".show").slideUp("slow");
					$(".show").removeClass('show');
					//todo adding status window
				}});
			}
		}, 500);
	}

	function addtocart(data){
		cart.push(data);
		$(".show").slideUp("slow");
		$(".show").removeClass('show');
		var message = 'Successfully added to your cart.';
		message += '<div class="button" style="left: 51%;" id="check"><a href="javascript:continu()">Continue shopping</a></div>'
			+ '<div class="button" id="check"><a href="javascript:display_cart()">Show cart & Checkout</a></div>';

		$('#overlay').fadeIn('slow');
		$('.buy-popup').html(message);
		$('.buy-popup').show();
	}

	function continu()
	{
		$(".buy-popup").slideUp("slow");
		$('#overlay').fadeOut("slow");
		$('.cart-show').slideUp("slow");
	}

	function checkout()
	{
		opened('POST', 'shop.php', cart, '_blank');
		while (cart.length > 0) {
			cart.pop();
		}
		//buy(cart);
	}

	function Remove(i)
	{
		var itemtoRemove = cart[i];
		if(itemtoRemove.displayName.toLowerCase() == 'discount')
		{
			$('.discount').css('background-color', 'white');
			$('.discount').css('color', 'black');
			$('.discount').prop('readonly', false);
		}
		cart.splice($.inArray(itemtoRemove, cart),1);
		display_cart();
	}

	function CheckDiscount()
	{
		if ( !$('.discount').prop('readonly')){
			var code = $('.discount').val().toLowerCase();
			var display = false;
			var discount = 0;
			var codelist = $.getJSON( "./js/discount.json", function(data) {
				$.each( data.code, function(i, item) {
					if(code == i.toLowerCase()){
						discount = item;
						discunt = item;
						display = true;
					}
				});
				if(display)
				{
					var disc = {};
					disc['Amount'] = 1;
					disc['Include'] = false;
					disc['IsSold'] = 1;
					disc['displayName'] = 'Discount';
					disc['name'] = 'discount';
					disc['Price'] = discount;
					disc['id'] = 0;
					addtocart(disc);
					$('.discount').attr('readonly', true);
					$('.discount').css('background-color', 'green');
					$('.discount').css('color', 'white');
					alert('Code is valid and give you -'+discount+'%');
				}
				else
				{
					$('.discount').css('background-color', 'red');
					$('.discount').css('color', 'white');
					alert('This code doesn\'t exist');
				}
			});
		}
	}

	function display_cart(){
		if(cart.length > 0)
		{
			var discount = 0;
			var html = [];
			var total = 0;
			html.push('Items : <br />');
			for(var i = 0; i < cart.length; i++)
			{
				if(cart[i].displayName.toLowerCase() == 'discount')
				{
					discount = cart[i].Price;
					html.push('<tr><td><img src="./images/discount.png"/>' + cart[i].displayName + ' x' + cart[i].Amount + '</td><td style="width:30px"></td><td>-' + cart[i].Price + ' %</td><td style="width:30px"></td><td><a href="javascript:Remove('+i+')">Remove</a></td></tr>');
				}
				else{
					html.push('<tr><td><img src="./images/items/' + cart[i].name + '.png"/>' + cart[i].displayName + ' x' + cart[i].Amount + '</td><td style="width:30px"></td><td>' + cart[i].Price + ' EURO</td><td style="width:30px"></td><td><a href="javascript:Remove('+i+')">Remove</a></td></tr>');
					total += cart[i].Price;
				}
			}
			if(cart.length > 0)
			{
				if(cart.length == 1 && cart[0].displayName.toLowerCase() == 'discount'){
					total = 0;
				}
				else{
					total = total - (total*(discount/100));
				}
				html.push('<br />');
				html.push('Total :<a style="width:30px"></a>' + total + 'EURO');
				html.push('<div class="button" style="left: 51%;" id="check"><a href="javascript:continu()">Continue shopping</a></div>'
					+ '<div class="button" id="check"><a href="javascript:checkout()">Checkout</a></div>');
				$('.cart-show').html(html);
				$('.cart-show').show();
				$('.buy-popup').slideUp();
			}
			return;
		}
		var html = [];
		html.push('<div class="button" style="left: 51%;" id="check"><a href="javascript:continu()">Continue shopping</a></div>');
		$('.cart-show').html(html);
	}

	function LoadMagazin(cat) {
		if(cat == 'VIP'){
			var html = [
				'<center>',
				'Paypal',
				'<br /><br /><br /><b>Please enter you character name on Rust-Evolution</b><br /><br />',
				'<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">',
				'<input type="hidden" name="cmd" value="_s-xclick">',
				'<input type="hidden" name="hosted_button_id" value="DLDC52X5YBEF6">',
				'<table>',
				'<tr><td><input type="hidden" name="on0" value="Character Name">Character Name</td></tr><tr><td><input type="text" name="os0" maxlength="200"></td></tr>',
				'</table>',
				'<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">',
				'<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">',
				'</form>',
				'<div style="color:#837630"><br /><br />Content : (kit : /kit vip) (every 3 hours)<br /><br />',
				'-1 500 000 wood<br />',
				'-1 500 000 stone<br />',
				'-300 000 metal ore<br />',
				'-300 000 sulfur ore<br />',
				'-100 000 animal fat<br />',
				'-100 000 High Quality Metal Ore<br />',
				'-200 000 leather<br />',
				'-200 000 cloth<br />',
				'-2 ak47<br />',
				'-600 Ammo 5.56HV<br />',
				'-10 supply signal<br />',
				'-50 small gift<br />',
				'-10 landscape picture frame<br />',
				'Stay for 30 days<br />',
				'</div>',
				'</center>'
			];
			$('.magazin-cont').html(html.join(''));
			return;
		}
		if(cat == 'VIP+'){
			var html = [
				'<center>',
				'Paypal',
				'<br /><br /><br /><b>Please enter you character name on Rust-Evolution</b><br /><br />',
				'<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">',
				'<input type="hidden" name="cmd" value="_s-xclick">',
				'<input type="hidden" name="hosted_button_id" value="RUWA8EFHHS47G">',
				'<table>',
				'<tr><td><input type="hidden" name="on0" value="Character Name">Character Name</td></tr><tr><td><input type="text" name="os0" maxlength="200"></td></tr>',
				'</table>',
				'<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal, le réflexe sécurité pour payer en ligne">',
				'<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">',
				'</form>',
				'<div style="color:#837630"><br /><br />Content : <br /><br />',
				'-Include everything from VIP rank<br />',
				'AND (kit : /kit vip+) 2 hours cooldown<br />',
				'-1 lr-300<br />',
				'-200 HV 5.56 Rifle Ammo<br />',
				'-200 c4<br />',
				'-1 Metal Facemask<br />',
				'-1 Metal Chest Plate<br />',
				'-1 Road Sign Kilt<br />',
				'-Private unraidable bank<br />',
				'-Gather x3000<br />',
				'Stay for 30 days<br />',
				'</div>',
				'</center>'
			];
			$('.magazin-cont').html(html.join(''));
			return;
		}
		function getObjects(obj, key, val, price, amount, inc, sold) {
			var objects = [];
			for (var i in obj) {
				if (!obj.hasOwnProperty(i)) continue;
				if (typeof obj[i] == 'object') {
					objects = objects.concat(getObjects(obj[i], key, val, price, amount, inc, sold));
				} else
				//if key matches and value matches or if key matches and value is not passed (eliminating the case where key matches but passed value does not)
				if (i == key && obj[i] == val || i == key && val == '') { //
					obj['Price'] = price;
					obj['Amount'] = amount;
					obj['IsSold'] = sold;
					if(inc) obj['Include'] = true;
				} else if (obj[i] == val && key == ''){
					//only add if the object is not already in the array
					if (objects.lastIndexOf(obj) == -1){
						obj['Price'] = price;
						obj['Amount'] = amount;
						obj['IsSold'] = sold;
						if(inc) obj['Include'] = true;
					}
				}
			}
			return obj;
		}

		var list = <?php echo json_encode($items);?>;
		var grouped = _.groupBy(list, 'category');
		var json = [{}];
		$.ajax({
			type: "POST",
			url: "./getItemPrice.php",
			data: {
				cat: cat
			},
			success: function (response) {
				json = JSON.parse(response);
				for(var i=0; i < json.length; i++) {
					grouped = getObjects(grouped, 'name', json[i].ItemName, json[i].Price, json[i].Amount, json[i].Include, json[i].IsSold); //adding price key
				}

				var attr = cat;
				var html = [];
				for (var ii = 0; ii < grouped[attr].length; ii++) {
					var data = grouped[attr][ii];
					if(data.hasOwnProperty('IsSold') && data.IsSold == 1){
						html.push('<div class="magazin-box" id="popup_' + (ii + 1) + '"><div id="bx"><div class="magazin-box-img">');
						html.push('<img src="./images/items/' + data.name + '.png"/></div>');
						html.push('<div class="magazin-title">' + data.displayName + '</div></div></div>');
						html.push('<div class="mg_popup" id="pop_' + (ii + 1) + '" style="display:none;">');
						html.push('<div class="mg_pop_left"><img src="./images/items/' + data.name + '.png"/></div>');
						html.push('<div class="mg_pop_right"><h3>' + data.displayName + '</h3>');
						if(data.Include){
							html.push('<div class="mg_cont"></div><h2 class="kol">Included Quantity: <span style="color:#6c8a30">' + data.Amount + ' piece</span></h2><div/>');
						}else{
							html.push('<div class="mg_cont"></div><h2 class="kol">Quantity: <span style="color:#6c8a30">' + data.Amount + ' piece</span></h2><div/>');
						}
						html.push('<h2 class="sum">price: <span style="color:#6c8a30">' + data.Price + ' EURO</span></h2>');
						<?php if (isset($_SESSION['T2SteamID64'])){ ?>
						html.push('<a href="javascript:void(0)" class="buy-anchor" data-itemName="' + data.name + '"><div class="button" style="height: 48.5px;position: absolute;right: 10px;bottom: 10px;" id="price">Buy using paypal</div></a>');
						html.push('<a href="javascript:void(0)" class="add-anchor" data-itemName="' + data.name + '"><div class="button" id="price" style="height: 48.5px;position: absolute;left: 10px;bottom: 10px;"><span>Add to cart</span></div></a>');
						<?php }else {?>
						html.push('<a href="javascript:void(0)" onclick="alert(\'You must be connected to do this.\');"><div class="button" id="price">You must be connected.</div></a>');
						<?php }?>
						html.push('</div></div>');
					}
				}
				$('.magazin-cont').html('When you are done, just do ingame /claim<br />'+html.join(''));
				var height = (html.length+1.5)/6.5;
				$('.wrap').css('height', height+'em');
				$(".magazin-box").click(function () {
					$("#overlay").fadeIn("slow");
					$(".menu").attr("id", "blur");
					var name = $(this).attr('id').replace("popup", "pop");
					$('#' + name).addClass('show')
						.show();
				});

				$('.magazin-cont a.buy-anchor').click(function(){
					var $this = $(this);
					var itemName = $this.attr('data-itemName');

					var data = grouped[attr].filter(function(value){
						return value.name == itemName;
					});

					buy(data[0]);
				});

				$('.magazin-cont a.add-anchor').click(function(evt){
					var hasAgreed = !!document.getElementById('terms').checked;
					if (!hasAgreed) {
						alert('You must agree to the terms and conditions!');
						evt.preventDefault();
						return;
					}
					var $this = $(this);
					var itemName = $this.attr('data-itemName');

					var data = grouped[attr].filter(function(value){
						return value.name == itemName;
					});

					addtocart(data[0]);
				});
			},
			error: function(textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});
	}
</script>
</body>
</html>