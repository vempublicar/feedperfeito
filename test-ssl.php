<?php
// Test SSL connection to Supabase
$url = 'https://phbnwlplkdawjgfyscxe.supabase.co/rest/v1/users?limit=1';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

// Try to use the certificate bundle
curl_setopt($ch, CURLOPT_CAINFO, 'C:\xampp\apache\bin\curl-ca-bundle.crt');

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "cURL Error: " . $error . "\n";
echo "Response: " . $response . "\n";
?>