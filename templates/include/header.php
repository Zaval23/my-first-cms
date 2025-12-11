<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo htmlspecialchars($results['pageTitle'])?></title>
    <?php
    // Определяем базовый путь для CSS и других ресурсов
    // Если index.php в корне, путь будет просто "style.css"
    // Если в поддиректории, добавим путь к корню
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $cssPath = ($scriptDir == '/' || $scriptDir == '\\') ? 'style.css' : $scriptDir . '/style.css';
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($cssPath); ?>" />
    <?php
    $jsPath = ($scriptDir == '/' || $scriptDir == '\\') ? 'JS' : $scriptDir . '/JS';
    $imagesPath = ($scriptDir == '/' || $scriptDir == '\\') ? 'images' : $scriptDir . '/images';
    ?>
    <script src="<?php echo htmlspecialchars($jsPath); ?>/jquery-3.2.1.js"></script>
    <script src="<?php echo htmlspecialchars($jsPath); ?>/loaderIdentity.js"></script>
    <script src="<?php echo htmlspecialchars($jsPath); ?>/showContent.js"></script>
  </head>
  <body>
    <div id="container">

      <a href="."><img id="logo" src="<?php echo htmlspecialchars($imagesPath); ?>/logo.jpg" alt="Widget News" /></a>