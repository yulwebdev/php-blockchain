<?php

class Blockchain {
  public $chain;

  function __construct($bc) {
	if(!$bc) {
		$this->chain = array(Block::genesis());
	} else {
		$this->chain = $bc->chain;
	}
  }
  
  function show() {
	echo "<pre>";
	print_r($this); // var_dump / print_r
	echo "</pre>";
  }
  
  public static function get() {
	return new self(unserialize(file_get_contents("bc")));
  }
  
  public static function addBlock($data) {
	  
	  $bc = self::get();
	  
	  $block = Block::mineBlock($bc->chain[count($bc->chain)-1], $data);
	  array_push($bc->chain, $block);
	  
	  // save bc
	  file_put_contents("bc", serialize($bc));
	  
  }
  
  /*
  public static function isValidChain($chain) {
	  if($chain[0] != Block::genesis()) return false;
	  
	  for($i=1; $i < count($chain); $i++) {
		$block = $chain[$i];
		$lastBlock = $chain[$i-1];
		  
		if($block->lastHash !== $lastBlock->hash ||
		  $block->hash !== Block::blockHash($block)) {
		  return false;
		}
	  }
	  
	  return true;
  }
  
  function replaceChain($newChain) {
	  if(count($newChain->chain) <= count($this->chain)) {
		echo "Received chain is not longer than the current chain.";
		return;
	  } elseif(!self::isValidChain($newChain->chain)) {
		echo "The received chain is not valid.";
		return;
	  }
	  
	  echo "Replacing blockchain with the new chain.";
	  $this->chain = $newChain;
  }
  */
  
}