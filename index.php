<?php

error_reporting(0); //E_ALL
ini_set('display_errors', 0);

define("DIFFICULTY", 4);
//define("MINE_RATE", 2500);
define("INITIAL_BALANCE", 500);
define("MINING_REWARD", 50);

include "classes/block.class.php";
include "classes/blockchain.class.php";
include "classes/wallet.class.php";
include "classes/transaction.class.php";
include "classes/transactionpool.class.php";
include "classes/miner.class.php";

function guidv4()
{
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
}

// scenario:
// create a few user wallets and a miner wallet
// make a few transactions between each other (check if transactions are added to the pool)
// mining

// create a few user wallets
$wallet1 = new Wallet();
$wallet2 = new Wallet();
$minerWallet = new Wallet();

$wallet1->show();
$wallet2->show();
$minerWallet->show();

// make a few transactions between each other

$wallet1->createTransaction($wallet2->publicKey, 50);
$wallet2->createTransaction($wallet1->publicKey, 100);

// (check if transactions are added to the pool)

$tp = TransactionPool::get();

$tp->show();

// mining
$miner = new Miner($minerWallet);

$bc = Blockchain::get();

echo "<h5>Blockchain before mining:</h5>";

$bc->show();

$miner->mine();

$bc = Blockchain::get();

echo "<h5>Blockchain after mining:</h5>";

$bc->show();

echo "wallet1 balance = " . $wallet1->calculateBalance() . "<br>";
echo "wallet2 balance = " . $wallet2->calculateBalance() . "<br>";
echo "minerWallet balance = " . $minerWallet->calculateBalance() . "<br>";