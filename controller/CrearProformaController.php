<?php


class CrearProformaController
{
    private $render;
    private $loginSession;
    private $crearProformaModel;
    private $costeoModel;
    private $qrChoferModel;

    public function __construct($render, $loginSession, $crearProformaModel, $costeoModel, $qrChoferModel)
    {
        $this->render = $render;
        $this->loginSession = $loginSession;
        $this->crearProformaModel = $crearProformaModel;
        $this->costeoModel = $costeoModel;
        $this->qrChoferModel = $qrChoferModel;
    }

    public function ejecutar()
    {
        $logeado = $this->loginSession->verificarQueUsuarioEsteLogeado();
        $data["titulo"] = "Proforma";
        if ($logeado) {
            $data["login"] = true;

            $tablaChoferes = $this->crearProformaModel->obtenerUsuariosChoferes();
            $data["tablaChoferes"] = $tablaChoferes;

            $tablaVehiculos = $this->crearProformaModel->obtenerEquiposVehiculos();
            $data["tablaVehiculos"] = $tablaVehiculos;

            $tablaAcoplados = $this->crearProformaModel->obtenerEquiposAcoplados();
            $data["tablaAcoplados"] = $tablaAcoplados;

            $tablaTiposDeCarga = $this->crearProformaModel->obtenerTiposDeCarga();
            $data["tablaTiposDeCarga"] = $tablaTiposDeCarga;

            $data2 = $this->loginSession->verificarQueUsuarioRol();
            $dataMerge = array_merge($data, $data2);
            echo $this->render->render("view/CrearProformaView.php", $dataMerge);
            exit();
        }
        echo $this->render->render("view/CrearProformaView.php");
    }

    public function crearProforma()
    {
        $logeado = $this->loginSession->verificarQueUsuarioEsteLogeado();
        if ($logeado) {
            $data["login"] = true;

            $clienteCuit = isset($_POST["clienteRegistradoCuit"]) ? $_POST["clienteRegistradoCuit"] : false;
            $clienteCuitExistente = $this->crearProformaModel->verificarCuitClienteExistente($clienteCuit);

            $cargaTipo = isset($_POST["cargaTipo"]) ? $_POST["cargaTipo"] : false;
            $cargaPeso = isset($_POST["cargaPeso"]) ? $_POST["cargaPeso"] : false;

            $tieneHazard = isset($_POST["hazardRadios"]) ? $_POST["hazardRadios"] : "no";
            $imoSubClass = null;
            $hazardId = NULL;
            if ($tieneHazard == "si") {
                $imoSubClass = isset($_POST["cargaTipo"]) ? $_POST["cargaTipo"] : false;
            }

            $tieneReefer = isset($_POST["reeferRadios"]) ? $_POST["reeferRadios"] : "no";
            $reeferId = NULL;
            $reeferTemperatura = null;
            if ($tieneReefer == "si") {
                $reeferTemperatura = isset($_POST["reeferTemperatura"]) ? $_POST["reeferTemperatura"] : false;
            }

            $origenLocalidad = isset($_POST["origenLocalidad"]) ? $_POST["origenLocalidad"] : false;
            $origenCalle = isset($_POST["origenCalle"]) ? $_POST["origenCalle"] : false;
            $origenAltura = isset($_POST["origenAltura"]) ? $_POST["origenAltura"] : false;


            $destinoLocalidad = isset($_POST["destinoLocalidad"]) ? $_POST["destinoLocalidad"] : false;
            $destinoCalle = isset($_POST["destinoCalle"]) ? $_POST["destinoCalle"] : false;
            $destinoAltura = isset($_POST["destinoAltura"]) ? $_POST["destinoAltura"] : false;


            $fechaSalida = isset($_POST["fechaSalida"]) ? $_POST["fechaSalida"] : false;
            $fechaLlegada = isset($_POST["fechaLlegada"]) ? $_POST["fechaLlegada"] : false;

            $vehiculoPatente = isset($_POST["vehiculoRadios"]) ? $_POST["vehiculoRadios"] : false;
            $acopladoPatente = isset($_POST["acopladoRadios"]) ? $_POST["acopladoRadios"] : false;

            $choferID = isset($_POST["choferRadios"]) ? $_POST["choferRadios"] : false;

            $total = null;
            $cantidadKilometros = null;
            $direccionDestino = null;
            $direccionPartida = null;
            $camposVacios = false;
            $proformaId = null;
            if ($clienteCuit != false and $cargaTipo != false and $cargaPeso != false and $origenLocalidad != false and $origenCalle != false and
                $origenAltura != false and $destinoLocalidad != false and $destinoCalle != false and $destinoAltura != false and
                $fechaSalida != false and $fechaLlegada != false and $vehiculoPatente != false and $acopladoPatente != false and
                $choferID != false and $imoSubClass !== false and $reeferTemperatura !== false) {
                if ($clienteCuitExistente == true) {
                    if ($imoSubClass != null) {
                        $hazardId = $this->crearProformaModel->registrarHazard($imoSubClass);
                    }

                    if ($reeferTemperatura != null) {
                        $reeferId = $this->crearProformaModel->registrarReefer($reeferTemperatura);
                    }

                    $idCarga = $this->crearProformaModel->registrarCarga($cargaTipo, $cargaPeso, $hazardId, $reeferId);

                    $idDireccionOrigen = $this->crearProformaModel->registrarDireccion($origenCalle, $origenAltura, $origenLocalidad);

                    $idDireccionDestino = $this->crearProformaModel->registrarDireccion($destinoCalle, $destinoAltura, $destinoLocalidad);

                    $idViaje = $this->crearProformaModel->registrarViaje($idCarga, $acopladoPatente, $vehiculoPatente, $choferID, $fechaSalida, $fechaLlegada, $idDireccionDestino, $idDireccionOrigen);

                    ini_set("date.timezone", "America/Argentina/Buenos_Aires");
                    $fecha = date("Y-m-d", time());

                    $proformaId = $this->crearProformaModel->registrarProforma($clienteCuit, $idViaje, $fecha);
                    $this->qrChoferModel->generarQr($proformaId, $choferID);
                    $nombreOrigenLocalidad = $this->crearProformaModel->devolverNombreLocalidadPorIdLocalidad($origenLocalidad);
                    $nombreOrigenProvincia = $this->crearProformaModel->devolverNombreProvinciaPorIdLocalidad($origenLocalidad);
                    $direccionDestino = $origenCalle . " " . $origenAltura . ", " . $nombreOrigenLocalidad . ", " . $nombreOrigenProvincia;

                    $nombreDestinoLocalidad = $this->crearProformaModel->devolverNombreLocalidadPorIdLocalidad($destinoLocalidad);
                    $nombreDestinoProvincia = $this->crearProformaModel->devolverNombreProvinciaPorIdLocalidad($destinoLocalidad);
                    $direccionPartida = $destinoCalle . " " . $destinoAltura . ", " . $nombreDestinoLocalidad . ", " . $nombreDestinoProvincia;

                    $tipoAcoplado = $this->crearProformaModel->obtenerTipoAcopladoPorPatente($acopladoPatente);
                    $total = $this->calcularCosteo($direccionDestino, $direccionPartida, $hazardId, $idCarga, $tipoAcoplado, $reeferId);
                    $cantidadKilometros = $this->costeoModel->calcularDistanciaEnKilometros($direccionDestino, $direccionPartida);
                }
            } else {
                $camposVacios = true;
            }

            $nombreTipoCarga = null;
            if ($cargaTipo != false) {
                $nombreTipoCarga = $this->crearProformaModel->devolverNombreTipoCargaPorIdCarga($cargaTipo);
            }

            $datosHazard = null;
            if ($imoSubClass != null) {
                $datosHazard = $this->crearProformaModel->devolverHazardPorHazardId($imoSubClass);
            }
            $datosReefer = null;
            if ($reeferId != null) {
                $datosReefer = $this->crearProformaModel->devolverReeferPorReeferId($reeferId);
            }

            $datos = array('camposVacios' => $camposVacios, 'clienteCuitExistente' => $clienteCuitExistente, 'total' => $total,
                'cantidadKilometros' => $cantidadKilometros, 'clienteCuit' => $clienteCuit, 'hazardId' => $hazardId, 'reeferId' => $reeferId,
                'direccionDestino' => $direccionDestino, 'direccionPartida' => $direccionPartida, 'fechaSalida' => $fechaSalida, 'fechaLlegada' => $fechaLlegada,
                'vehiculoPatente' => $vehiculoPatente, 'acopladoPatente' => $acopladoPatente, 'cargaPeso' => $cargaPeso, 'nombreTipoCarga' => $nombreTipoCarga,
                'datosHazard' => $datosHazard, 'datosReefer' => $datosReefer, 'choferID' => $choferID, 'proformaId' => $proformaId);
            echo json_encode($datos);
            exit();
        }
        echo $this->render->render("view/loginView.php");
    }

    public function registrarCliente()
    {
        $logeado = $this->loginSession->verificarQueUsuarioEsteLogeado();
        if ($logeado) {
            $data["login"] = true;
            $clienteDenominacion = isset($_POST["clienteDenominacion"]) ? $_POST["clienteDenominacion"] : false;
            $clienteNombre = isset($_POST["clienteNombre"]) ? $_POST["clienteNombre"] : false;
            $clienteApellido = isset($_POST["clienteApellido"]) ? $_POST["clienteApellido"] : false;
            $clienteCuit = isset($_POST["clienteCuit"]) ? $_POST["clienteCuit"] : false;
            $clienteLocalidad = isset($_POST["clienteLocalidad"]) ? $_POST["clienteLocalidad"] : false;
            $clienteCalle = isset($_POST["clienteCalle"]) ? $_POST["clienteCalle"] : false;
            $clienteAltura = isset($_POST["clienteAltura"]) ? $_POST["clienteAltura"] : false;
            $clienteTelefono = isset($_POST["clienteTelefono"]) ? $_POST["clienteTelefono"] : false;
            $clienteEmail = isset($_POST["clienteEmail"]) ? $_POST["clienteEmail"] : false;
            $contacto1 = isset($_POST["clienteContacto1"]) ? $_POST["clienteContacto1"] : null;
            $contacto2 = isset($_POST["clienteContacto2"]) ? $_POST["clienteContacto2"] : null;

            $clienteCuitExistente = false;
            if (isset($_POST["clienteCuit"])) {
                $clienteCuitExistente = $this->crearProformaModel->verificarCuitClienteExistente($clienteCuit);
            }


            if ($clienteDenominacion != false and $clienteNombre != false and $clienteApellido != false and $clienteCuit != false and
                $clienteLocalidad != false and $clienteCalle != false and $clienteAltura != false and $clienteTelefono != false and
                $clienteEmail != false and $clienteCuitExistente == false) {

                $idDireccionCliente = $this->crearProformaModel->registrarDireccion($clienteCalle, $clienteAltura, $clienteLocalidad);

                $this->crearProformaModel->registrarClienteConDireccion($idDireccionCliente, $clienteDenominacion,
                    $clienteNombre, $clienteApellido, $clienteCuit, $clienteTelefono, $clienteEmail, $contacto1, $contacto2);
            }

            $datos = array('clienteDenominacion' => $clienteDenominacion, 'clienteNombre' => $clienteNombre, 'clienteApellido' => $clienteApellido,
                'clienteCuit' => $clienteCuit, 'clienteLocalidad' => $clienteLocalidad, 'clienteCalle' => $clienteCalle, 'clienteAltura' => $clienteAltura,
                'clienteTelefono' => $clienteTelefono, 'clienteEmail' => $clienteEmail, 'clienteCuitExistente' => $clienteCuitExistente);

            echo json_encode($datos);
            exit();
        }
        echo $this->render->render("view/loginView.php");
    }

    public function cargarListaProvincia()
    {
        $listaProvincia = $this->crearProformaModel->obtenerProvincias();
        echo $listaProvincia;
    }

    public function cargarListaLocalidad()
    {
        $idProvincia = $_POST["idProvincia"];
        $listaLocalidad = $this->crearProformaModel->obtenerLocalidades($idProvincia);
        echo $listaLocalidad;
    }

    public function cargarListaImoClass()
    {
        $listaImoClass = $this->crearProformaModel->obtenerImoClases();
        echo $listaImoClass;
    }

    public function cargarListaImoSubClass()
    {
        $idImoClass = $_POST["idImoClass"];
        $listaImoClass = $this->crearProformaModel->obtenerImoSubClases($idImoClass);
        echo $listaImoClass;
    }

    public function calcularCosteo($direccionDestino, $direccionPartida, $idImoSubClass, $idTipoCarga, $idTipoAcoplado, $idReefer)
    {
        $distancia = $this->costeoModel->calcularDistanciaEnKilometros($direccionDestino,
            $direccionPartida);
        $precio = $this->costeoModel->precioPorKilometro($idImoSubClass, $idTipoCarga, $idTipoAcoplado, $idReefer);
        return $this->costeoModel->precioDeLaDistancia($distancia, $precio);
    }

    public function mostrarClientesPorCuit()
    {
        $cuit = isset($_POST["clienteCuit"]) ? $_POST["clienteCuit"] : null;
        if ($cuit != null) {
            $listaClientes = $this->crearProformaModel->obtenerNombreApellidoClientesPorCuit($cuit);
            echo $listaClientes;
        }
    }

}