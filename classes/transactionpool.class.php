<?php

class TransactionPool {
  public $transactions;

  function __construct($tp) {
	if(!$tp) {
	  $this->transactions = array();
	} else {
	  $this->transactions = $tp->transactions;
	}
  }
  
  function show() {
	echo "<h5>TransactionPool:</h5>";

	echo "<pre>";
	print_r($this); // var_dump / print_r
	echo "</pre>";
  }
  
  public static function existingTransaction($address) {
	$tp = self::get();
	foreach($tp->transactions as $t) {
		if($t->input["address"] == $address) {
			return $t;
		}
	}
	
	return false;
	
  }
  
  public static function get() {
	return new self(unserialize(file_get_contents("tp")));  
  }
  
  function save() {
	return file_put_contents("tp", serialize($this));;  
  }
  
  function clear() {
	return file_put_contents("tp", "");  
  }
  
  public static function addTransaction($transaction) {
	  $tp = self::get();
	  
	  array_push($tp->transactions, $transaction);
	  
	  return $tp->save();
  }
  
  public static function updateOrAddTransaction($transaction) {
	  
	$tp = self::get();
	
	$transactionWithId = false;
	
	foreach($tp->transactions as $tid => $t) {
		if($t->id == $transaction->id) {
			$transactionWithId = $tid;
		}
	}
	
    if ($transactionWithId !== false) {
      $tp->transactions[$transactionWithId] = $transaction;
    } else {
	  array_push($tp->transactions, $transaction);
    }
	
	return $tp->save();
	
  }
  
  /*
  function validTransactions() {
	  return $this->transactions;
  }
  */
  
}