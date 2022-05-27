<?php

session_start();

$s = $_POST["session_id"];

session_destroy();

sendMessage([]);
