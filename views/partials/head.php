<?php
$currentFile = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));
$isAdmin     = ($currentDir === 'admin');
?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<link rel="stylesheet" href="/zoopedia/public/css/style.css">

<title><?= isset($title) ? htmlspecialchars($title) : 'Zoopedia' ?></title>
