<?php
	header("Service-Worker-Allowed: /");
	header("Content-Type: application/javascript");
	echo "importScripts('https://api.nativealerts.com/worker.js');";
?>



