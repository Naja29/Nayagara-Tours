<?php
session_start();
session_destroy();
header('Location: /nayagara-tours/admin/login.php');
exit;
