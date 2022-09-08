<?php

class Miner {
    public $wallet;

	function __construct($wallet) {
		$this->wallet = $wallet;
	}
	
	function mine() {
		$tp = TransactionPool::get();
		
		if(empty($tp->transactions)) return false; // theres nothing to mine
		
		array_push($tp->transactions, Transaction::rewardTransaction($this->wallet, Wallet::blockchainWallet()));
		// get bc
		$bc = Blockchain::get();
		// mine block
		Blockchain::addBlock($tp->transactions);
		//p2p.syncChains()
		//clear pool (broadcast to every participant to clear their pools)
		TransactionPool::clear();
		return;
	}
	
}