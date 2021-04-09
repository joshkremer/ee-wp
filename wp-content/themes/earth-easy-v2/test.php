<?php 
$url = 'https://store-j602wc6a.mybigcommerce.com/api/v2/products.json?limit=250';
$cURL = curl_init();
$username = 'eartheasy';
$password = 'b97ca01898f4b685ffb5be9b6ba36db8b9461dcc';
curl_setopt($cURL, CURLOPT_URL, $url);
curl_setopt($cURL, CURLOPT_HTTPGET, true);
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($cURL, CURLOPT_USERPWD, "$username:$password");
curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json',
'Accept: application/json'
));
$result = curl_exec($cURL);
curl_close($cURL);
$result_array = json_decode($result, true);

echo "<pre>";
print_r($result_array);

?>