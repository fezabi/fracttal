<?php

    require_once 'controllers/controller.php';

    // Obtener la URL actual desde $_SERVER
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Parsear la URL para obtener sus componentes
    $urlComponents = parse_url($currentUrl);

    // Obtener el path (ruta) de la URL
    $path = $urlComponents['path'];

    // Separar el path en segmentos
    $pathSegments = explode('/', trim($path, '/'));

    $error = false;

    // Obtener los elementos de interés
    if(isset($pathSegments[1])) {
        $token = $pathSegments[1]; // "Bhsgl5498dKgGg23g5jmMl4k0"
        $solicitud = Controller::validaToken($token);
        if($solicitud != null) {
            $activo = $solicitud->id_activo;
            $id = $solicitud->id;
            $woFolio = $solicitud->wo_folio;
            if(empty($activo)) {
                $responsable = $solicitud->responsable;
                $sucursal = $solicitud->sucursal;
                $activos = Controller::getActivos($sucursal);
            }else {
                $error = true;
                $tipo = 'info';
                $mensajeError = "El activo ya fue actualizado!";
            }
        }else{
            $error = true;
            $tipo = 'danger';
            $mensajeError = "Error, token no válido!";
        }
    }else{
        $error = true;
        $tipo = 'danger';
        $mensajeError = "No se encontro token!";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar activo</title>
    
    <script src="views/assets/bootstrap-5.3.1-dist/js/popper.min.js"></script>
    <script src="views/assets/bootstrap-5.3.1-dist/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="views/assets/bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="views/assets/bootstrap-5.3.1-dist/css/bootstrap.min.css">
</head>
<body style="background-color: rgb(238, 238, 238);">
    <div class="container-fluid">
        <div class="row">
            <div class="col" style="background-color: rgb(70, 135, 241); padding:30px;"></div>
        </div>
        <div class="row p-3">
            <div class="col-12" style="background-color: #fff; border-radius: 8px; padding: 30px;">
                <?php if(!$error):?>
                    <h6><span style="color: rgb(70, 135, 241);"><?=$responsable?></span>, ACTUALIZA EL ACTIVO</h6>
                    <hr>
                    <!-- <form action="actualizar-activo.php" method="POST"> -->
                    <form>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn" style="background-color: rgb(70, 135, 241); color: #fff;" id="btnEscanear">
                                    Escanear QR
                                    <svg class="colored-svg" style="fill: #fff; width: 25px; height: 25px; padding-bottom: 3px;">
                                        <use xlink:href="views/assets/img/qr-icon.svg#qr-icon"></use>
                                    </svg>
                                </button>
                                <button type="button" class="btn btn-danger" id="btnCerrar" style="display:none;">
                                    Cerrar 
                                    <svg class="colored-svg" style="fill: #fff; width: 25px; height: 25px; padding-bottom: 3px;">
                                        <use xlink:href="views/assets/img/close-icon.svg#close-icon"></use>
                                    </svg>
                                </button>
                                <!-- Elemento de video para mostrar la cámara -->
                                <video id="camara" autoplay width="100%" style="display: none; margin-top: 25px;"></video>
                                <audio id="audioScan" src="views/assets/mp3/bip.mp3"></audio>
                            </div>
                        </div>
                        <br>
                        <select class="form-control" name="idActivo" id="idActivo">
                            <?php foreach($activos as $activo): ?>
                                <option value="<?=$activo['value']?>"><?=$activo['label']?></option>
                            <?php endforeach;?>
                        </select><br>
                        <div class="alert alert-info" role="alert" id="infoEscaner" style="display: none;"></div>
                        <input type="hidden" value="<?=$id?>" name="id">
                        <input type="hidden" value="<?=$woFolio?>" name="woFolio">
                        <input type="hidden" value="<?=$token?>" name="token">
                        <button class="btn" style="background-color: rgb(70, 135, 241); color: #fff;" type="submit">
                            Guardar 
                            <svg class="colored-svg" style="fill: #fff; width: 25px; height: 25px; padding-bottom: 3px;">
                                <use xlink:href="views/assets/img/save-icon.svg#save-icon"></use>
                            </svg>
                        </button>
                    </form>
                <?php else:?>
                    <div class="alert alert-<?=$tipo?>" role="alert"><?=$mensajeError?></div>
                <?php endif;?>
            </div>
        </div>
    </div>
</body>
<script>    
    const btnEscanear = document.getElementById("btnEscanear");
    const btnCerrar = document.getElementById("btnCerrar");
    const camara = document.getElementById("camara");
    const selectElement = document.getElementById('idActivo');
    const infoEscaner = document.getElementById("infoEscaner");
    let escaneoActivo = false; // Bandera para controlar el escaneo

    // Agrega un evento de clic al botón
    btnCerrar.addEventListener("click", function() {
        btnCerrar.style.display = "none";
        btnEscanear.style.display = "block";
        camara.style.display = "none";
    });

    // Agrega un evento de clic al botón
    btnEscanear.addEventListener("click", function() {
        btnCerrar.style.display = "block";
        btnEscanear.style.display = "none";
        camara.style.display = "block";

        // Restablecer el estado del select y del mensaje de información
        selectElement.selectedIndex = 0;
        infoEscaner.style.display = "none";

        // Acceder a la cámara y escanear código QR
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                var camara = document.getElementById('camara');
                camara.srcObject = stream;

                // Configurar el escaneo del código QR
                const codeReader = new ZXing.BrowserQRCodeReader();
                codeReader.decodeFromVideoDevice(null, 'camara', (result, err) => {
                    if (result) {
                        // Se escaneó un código QR, actualiza el select
                        // const valueToSelect = result.text; // Debería coincidir con el valor de una opción en el select

                        // Extraer el número de la URL
                        const urlParts = result.text.split('/');
                        const valueToSelect = urlParts[urlParts.length - 1]; // Último segmento

                        const btnCerrar = document.getElementById("btnCerrar");
                        const infoEscaner = document.getElementById("infoEscaner");
                        const audioScan = document.getElementById("audioScan");

                        // Encuentra la opción en el select y selecciónala
                        for (let i = 0; i < selectElement.options.length; i++) {
                            if (selectElement.options[i].value === valueToSelect) {
                                selectElement.selectedIndex = i;
                                break;
                            }
                        }
                        // Reproducir el sonido de escaneo
                        audioScan.play();

                        setTimeout(function() {
                            btnCerrar.click();
                            infoEscaner.style.display = "block";
                            infoEscaner.innerHTML = 'Código escaneado correctamente';
                        }, 1000);

                    } else if (err) {
                        console.error('Error al escanear código QR:', err);
                    }
                });
            })
            .catch(function(error) {
                console.error('Error al acceder a la cámara:', error);
            });
    });
</script>

<!-- Agrega la biblioteca para escanear códigos QR -->
<!-- <script src="https://unpkg.com/@zxing/library@latest"></script> -->
<script src="views/assets/js/zxing_library@0.20.0_umd_index.min.js"></script>
</html>