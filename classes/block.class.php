<?php

class Block {
    public $timestamp;
    public $lastHash;
    public $hash;
    public $nonce;
    public $data;

  function __construct($timestamp, $lastHash, $hash, $nonce, $data) {
    $this->timestamp = $timestamp;
    $this->lastHash = $lastHash;
    $this->hash = $hash;
    $this->nonce = $nonce;
    $this->data = $data;
  }
  
  public static function genesis() {
	  return new self('Genesis time', "-----", "f1r57-h45h", 0, "foo");
  }
  
  public static function mineBlock($lastBlock, $data) {

	  $nonce = 0;

	  do {
		$nonce++;
	    $timestamp = floor(microtime(true) * 1000);
	    $hash = hash("sha256", $timestamp . $lastBlock->hash . serialize($data) . $nonce);
	  } while(substr($hash, 0, DIFFICULTY) !== str_repeat('0', DIFFICULTY));
	  
	  return new self($timestamp, $lastBlock->hash, $hash, $nonce, $data);
  }
  
  /*
  public static function hash($timestamp, $lastHash, $data, $nonce) {
	  return hash("sha256", $timestamp . $lastHash . $data . $nonce);
  }
  
  public static function blockHash($block) {
	  return hash("sha256", $block->timestamp . $block->lastHash . $block->data . $block->nonce);
  }
  
  public static function adjustDifficulty($lastBlock, $currentTime) {
	$difficulty = ($lastBlock->timestamp + MINE_RATE) > $currentTime ? $lastBlock->difficulty + 1 : $lastBlock->difficulty - 1;
	return $difficulty;
  }
  */
}