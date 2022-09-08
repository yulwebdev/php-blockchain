<?php

class Transaction {
    public $id;
    public $input;
    public $outputs;

  function __construct() {
    $this->id = guidv4();
    $this->input = null;
    $this->outputs = array();
  }
  
  public static function transactionWithOutputs($senderWallet, $outputs) {
	$transaction = new self();
	
	foreach($outputs as $output) {
	  array_push($transaction->outputs, $output);
	}
	$transaction->signTransaction($senderWallet);

	return $transaction;
  }
  
  function update($senderWallet, $recipient, $amount) {
	  
    //senderOutput = this.outputs.find(output => output.address === senderWallet.publicKey);
	
    foreach($this->outputs as $output) {
		if($output["address"] == $senderWallet->publicKey) {
			$senderOutput = $output;
			break;
		}
	}
	
    if($amount > $senderOutput["amount"]) {
	  echo "Amount $amount exceeds balance.";
      return;
    }
	
    $senderOutput["amount"] = $senderOutput["amount"] - $amount;
	array_push($this->outputs, array("amount"=>$amount, "address"=>$recipient));
	$this->signTransaction($senderWallet);
	
    foreach($this->outputs as $output_id => $output) {
		if($output["address"] == $senderWallet->publicKey) {
			$this->outputs[$output_id]["amount"] = $senderOutput["amount"];
			break;
		}
	}
	
    return $this;
	
  }
  
  public static function newTransaction($senderWallet, $recipient, $amount) {
	  
	  if($amount > $senderWallet->balance) {
		  echo "Amount $amount exceeds balance.";
		  return;
	  }
	  
	  $outputs = array();
	  
	  array_push($outputs, array("amount"=>$senderWallet->balance - $amount, "address"=>$senderWallet->publicKey));
	  array_push($outputs, array("amount"=>$amount, "address"=>$recipient));
	  
	  return self::transactionWithOutputs($senderWallet, $outputs);
	  
  }
  
  public static function rewardTransaction($minerWallet, $bcWallet) {
	  
	  $outputs = array();
	  
	  array_push($outputs, array("amount"=>MINING_REWARD, "address"=>$minerWallet->publicKey));
	  
	  return self::transactionWithOutputs($bcWallet, $outputs);
  }
  
  function signTransaction($senderWallet) {
	  $this->input = array(
		"timestamp"=>floor(microtime(true) * 1000),
		"amount"=>$senderWallet->balance,
		"address"=>$senderWallet->publicKey,
		"signature"=>Wallet::sign($senderWallet, serialize($this->outputs))
	  );
  }
  
  function verifyTransaction($senderWallet) {
	  return (Wallet::sign($senderWallet, serialize($this->outputs)) == $this->input["signature"]) ? true : false;
  }
  
}