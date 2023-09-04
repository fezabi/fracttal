<?php
    require_once 'controllers/controller.php';

    $id = $_POST['id'];
    $idActivo = $_POST['idActivo'];
    $woFolio = $_POST['woFolio'];
    $token = $_POST['token'];

    Controller::updateActivo($id, $token, $idActivo, $woFolio);
    