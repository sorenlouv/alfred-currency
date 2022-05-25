<?php

function getRow($currency_from, $currency_to, $amount){
  // build URL
  $url = "https://v6.exchangerate-api.com/v6/f3a28ea08292d915f7e45eee/pair/" . $currency_from . "/" . $currency_to;

  // get exchange rate
  $options = array('http' => array('ignore_errors' => TRUE));
  $context  = stream_context_create($options);
  $data_json = file_get_contents($url, false, $context);
  $data = json_decode($data_json);



  if($data->result == "success" && getHttpCode($http_response_header) == 200){
    $exchange_rate = $data->conversion_rate;
    $destination_amount = number_format($exchange_rate * $amount, 2);

    echo json_encode([
      "items" => [
          [
              "title" => $destination_amount . " " . $currency_to,
              "subtitle" => $currency_from . ' to ' . $currency_to,
              "arg" => $destination_amount,
          ]
      ]
    ]);

  } else {
    echo json_encode([
      "items" => [
          [
              "title" => "Unsupported currency pair: " . $currency_from ." / " . $currency_to
          ]
      ]
    ]);
  }
}

function getHttpCode($http_response_header)
{
    if(is_array($http_response_header))
    {
        $parts=explode(' ',$http_response_header[0]);
        if(count($parts)>1) //HTTP/1.0 <code> <text>
            return intval($parts[1]); //Get code
    }
    return 0;
}

function getWaitingRow(){
  echo json_encode([
    "items" => [
        [
            "title" => "Waiting for input",
            "subtitle" => "Example: 50 usd to eur",
        ]
    ]
  ]);
}

function getResult($query, $default_currency){



  // Replace symbols
  $query = str_replace(
    array("€", "$", "£"),
    array("EUR", "USD", "GBP"),
    $query
  );

  // uppercase
  $query = strtoupper($query);

  // strip " to "
  $query = str_replace(" TO ", " ", $query);
  $query = str_replace(" IN ", " ", $query);

  // trim
  $query = trim($query);


  // parse query
  preg_match("/^([0-9]+[.]?[0-9]*?)\s?([A-Z]{3})\s?([A-Z]{3})?$/", $query, $matches);

  if(!empty($matches)){

    $amount = $matches[1];
    $currency_from = $matches[2];
    $currency_to = isset($matches[3]) ? $matches[3] : $default_currency;

    // Initial row
    getRow($currency_from, $currency_to, $amount);

  }else{
    getWaitingRow();
  }
}




