<?php
  error_reporting(E_ALL & ~E_NOTICE);
  session_start();
  if (isset($_SESSION['auth'])) {
    include 'member.php';
  } else {
    include 'index1.html';
  }