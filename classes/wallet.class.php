<?php

class Wallet {
    public $balance;
    public $publicKey;
    public $privateKey;

  function __construct() {
    $this->balance = INITIAL_BALANCE;
    $this->publicKey = bin2hex(random_bytes(20));
    $this->privateKey = bin2hex(random_bytes(20));
  }
  
  function show() {
	  echo "Wallet <b>" . substr($this->publicKey, 0, 10) . "...</b> balance = " . $this->balance . "<br>";
  }
  
  public static function sign($senderWallet, $dataHash) {
	  
	  return md5($senderWallet->privateKey . $senderWallet->publicKey . $dataHash);
	  
  }
  
  function createTransaction($recipient, $amount) {
	  $this->balance = $this->calculateBalance();
	  
      if ($amount > $this->balance) {
		echo "Amount: $amount exceceds current balance: $this->balance.";
        return;
      }
	  
      $transaction = TransactionPool::existingTransaction($this->publicKey);

      if ($transaction) {
        $transaction->update($this, $recipient, $amount);
      } else {
        $transaction = Transaction::newTransaction($this, $recipient, $amount);
      }
	  
	  // add to pool
	  TransactionPool::updateOrAddTransaction($transaction);
	   
	  //echo $this->publicKey . " has " . $this->balance . " coins.";
	  return $transaction;
	  
  }
  
  function calculateBalance() {
	  
	  // set balance to current wallet balance
	  $balance = $this->balance;
	  
	  $bc = Blockchain::get();
	  
	  // get all the transactions from the bc
	  $ts = array();
	  
	  foreach($bc->chain as $block) {
		if(!is_array($block->data)) continue; //skip genesis block
		  foreach($block->data as $t) {
			  if (!$t instanceof Transaction) continue; // not a transaction
			  array_push($ts, $t);
		  }
	  }
	  
	  // get all the sent transation by this wallet
	  $walletInputTs = array();
	  
	  foreach($ts as $t) {
		  if($t->input["address"] == $this->publicKey) {
			  array_push($walletInputTs, $t);
		  }
	  }
	  
	  $startTime = 0;
	  
	  if(count($walletInputTs)) {
		  $recentInputT;
		  
		  // get the most recent
		  foreach($walletInputTs as $t) {
			if($t->input["timestamp"] > $startTime) {
				$recentInputT = $t;
				$startTime = $t->input["timestamp"];
			}
		  }
		  
		  // update the balance
		  foreach($recentInputT->outputs as $output) {
			if($output["address"] == $this->publicKey) {
				$balance = $output["amount"];
			}
		  }
		 
	  }
	  
	  // maybe smth was sent to this wallet
	  foreach($ts as $t) {
		if($t->input["address"] != $this->publicKey) {
			foreach($t->outputs as $output) {
				if($output["address"] == $this->publicKey) {
					$balance += $output["amount"];
				}
			}
		}
	  }
	  
	  return $balance;
	  
  }
  
  public static function blockchainWallet() {
	  $blockchainWallet = new self();
	  $blockchainWallet->address = "blockhain-wallet";
	  return $blockchainWallet;
  }
  
}