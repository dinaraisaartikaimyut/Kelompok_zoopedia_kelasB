<?php
$currentFile = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));
$isAdmin     = ($currentDir === 'admin');
?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<link rel="stylesheet" href="/zoopedia/public/css/main.css" />
<link rel="stylesheet" href="/zoopedia/public/css/navbar.css" />
<link rel="stylesheet" href="/zoopedia/public/css/componente.css" />

<?php if ($isAdmin): ?>
<link rel="stylesheet" href="/zoopedia/public/css/admin.css" />
<link rel="stylesheet" href="/zoopedia/public/css/detail_hasil.css">
<?php endif; ?>

<?php if ($currentFile === 'kuis.php'): ?>
<link rel="stylesheet" href="/zoopedia/public/css/kuis.css" />
<?php endif; ?>

<?php if (in_array($currentFile, ['kategori.php', 'detail_kategori.php', 'hewan.php'])): ?>
<link rel="stylesheet" href="/zoopedia/public/css/kategori.css" />
<?php endif; ?>

<title><?= isset($title) ? htmlspecialchars($title) : 'Zoopedia' ?></title>
