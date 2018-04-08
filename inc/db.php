<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=admin_basic;charset=utf8', 'root', 'root', array (
                  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                                      )
              );
  }
  catch (Exception $e)
  {
      die('Erreur : ' . $e->getMessage());
  }