<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://paysprint.in/service-api/api/v1/service/bill-payment/bill/getoperator",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "authorisedkey: MzNkYzllOGJmZGVhNWRkZTc1YTgzM2Y5ZDFlY2EyZTQ=",
    "cache-control: no-cache",
    "postman-token: 6211c535-faee-f65a-b671-d7a8e913bcb0",
    "token:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0aW1lc3RhbXAiOjE2MjgwODYxNTUsInBhcnRuZXJJZCI6IlBTMDAxIiwicmVxaWQiOiIxMjIzNzg2ODc4Njc2ODMzIn0.Yxjy6bxJKDRne_6oqG3MlFHgO01tt-jfu7cCq2gzNQM"
  ),
));

echo json_encode(json_decode(curl_exec($curl),true)['data']);
$err = curl_error($curl);
curl_close($curl);

