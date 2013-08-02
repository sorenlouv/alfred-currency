<?php
require_once('workflows.php');
$wf = new Workflows();

function getRow($currency_from, $currency_to, $amount){
  global $wf;

  // build URL
  $url = "http://rate-exchange.appspot.com/currency?from=" . $currency_from . "&to=" . $currency_to;

  // get exchange rate
  $data_json = file_get_contents($url);
  $data = json_decode($data_json);

  // Error occurec. Probabl invalid currency
  if($data->err){
    $subtext = "Supported currencies are: DKK, EUR, USD, SEK, GBP, CHF, AUD, NOK";
    $wf->result(time(), $data->err, "Invalid currency", $subtext, 'icon.png');

  // No problems
  }else{
    $exchange_rate = $data->rate;

    $clipboard = round($exchange_rate * $amount, 1);
    $text = round($exchange_rate * $amount, 1) . " " . $currency_to;
    $sub_text = strtoupper($currency_from) . ' to ' . strtoupper($currency_to);

    $wf->result(time(), $clipboard, $text, $sub_text, 'icon.png');
  }
}

function getWaitingRow(){
  global $wf;
  $wf->result(time(), "Waiting for input", "Waiting for input...", "Type in like: 50USD to EUR", 'icon.png');
}

function getResult($query){
  global $wf;

  file_put_contents("getResult.log", $query, FILE_APPEND);

  $default_currency = "DKK";
  //$currencies = array("DKK", "EUR", "USD", "SEK", "GBP", "CHF", "AUD", "NOK");

  $query = trim($query);
  // $query = trim("40USD to SEK");

  // parse query
  preg_match("/^(\d+)([a-zA-Z]{3})(\sto\s([a-zA-Z]{3}))?$/", $query, $matches);
  if(!empty($matches)){

    $amount = $matches[1];
    $currency_from = $matches[2];
    $currency_to = isset($matches[4]) ? $matches[4] : $default_currency;

    // Initial row
    getRow($currency_from, $currency_to, $amount);

  }else{
    getWaitingRow();
  }

  return $wf->toxml();
}

// echo getResult("40usd to asa");