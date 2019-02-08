<?php
require_once('workflows.php');
$wf = new Workflows();

function getRow($currency_from, $currency_to, $amount){
  global $wf;

  // build URL
  $url = "https://api.exchangeratesapi.io/latest?base=" . $currency_from . "&symbols=" . $currency_to;

  // get exchange rate
  $options = array('http' => array('ignore_errors' => TRUE));
  $context  = stream_context_create($options);
  $data_json = file_get_contents($url, false, $context);
  $data = json_decode($data_json);

  // print_r($data);

  if(!isset($data->error) && getHttpCode($http_response_header) == 200){
    $exchange_rate = $data->rates->$currency_to;
    $clipboard = number_format($exchange_rate * $amount, 2);
    $text = $clipboard . " " . $currency_to;
    $sub_text = $currency_from . ' to ' . $currency_to;
    $wf->result(time(), $clipboard, $text, $sub_text, 'icon.png');
  } else {
    $subtext = $data->error . " Please check if currencies are valid.";
    $wf->result(time(), $data->err, "Could not fetch data", trim($subtext), 'icon.png');
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
  global $wf;
  $wf->result(time(), "Waiting for input", "Waiting for input...", "Type in like: 50 USD to EUR", 'icon.png');
}

function getResult($query, $default_currency){
  global $wf;

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

  // print_r('>>> query');
  // print_r($query);

  // parse query
  preg_match("/^(\d+)\s?([A-Z]{3})\s?([A-Z]{3})?$/", $query, $matches);

  // print_r('>>> matches');
  // print_r($matches);
  if(!empty($matches)){

    $amount = $matches[1];
    $currency_from = $matches[2];
    $currency_to = isset($matches[3]) ? $matches[3] : $default_currency;

    // Initial row
    getRow($currency_from, $currency_to, $amount);

  }else{
    getWaitingRow();
  }

  return $wf->toxml();
}

// echo getResult("41 GBP", "EUR");
