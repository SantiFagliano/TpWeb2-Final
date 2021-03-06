<?php


class AdministrarUsuariosController
{
    private $render;
    private $administrarUsuarioModel;
    private $loginSession;

    public function __construct($render, $loginSession, $administrarUsuarioModel)
    {
        $this->render = $render;
        $this->loginSession = $loginSession;
        $this->administrarUsuarioModel = $administrarUsuarioModel;
    }

    public function ejecutar()
    {
        $logeado = $this->loginSession->verificarQueUsuarioEsteLogeado();
        $data["titulo"] = "Admin usuarios";
        if ($logeado) {
            $data["login"] = true;


            $data["bajaUsuario"] = isset($_GET["bajaUsuario"]) ? $_GET["bajaUsuario"] : false;
            $data["bajaEmpleado"] = isset($_GET["bajaEmpleado"]) ? $_GET["bajaEmpleado"] : false;
            $data["nombreUsuarioExistente"] = isset($_GET["nombreUsuarioExistente"]) ? $_GET["nombreUsuarioExistente"] : false;
            $data["dniExistente"] = isset($_GET["dniExistente"]) ? $_GET["dniExistente"] : false;
            $data["modificarUsuario"] = isset($_GET["modificarUsuario"]) ? $_GET["modificarUsuario"] : false;
            $data["modificarEmpleado"] = isset($_GET["modificarEmpleado"]) ? $_GET["modificarEmpleado"] : false;
            $data["empleadoChoferOcupado"] = isset($_GET["empleadoChoferOcupado"]) ? $_GET["empleadoChoferOcupado"] : false;


            $tablaUsuarios = $this->administrarUsuarioModel->obtenerUsuariosNoEmpleados();
            $tablaUsuariosEmpleados = $this->administrarUsuarioModel->obtenerUsuariosEmpleados();

            $data["tablaUsuarios"] = $tablaUsuarios;
            $data["tablaUsuariosEmpleados"] = $tablaUsuariosEmpleados;

            $data2 = $this->loginSession->verificarQueUsuarioRol();
            $dataMerge = array_merge($data, $data2);
            echo $this->render->render("view/administrarUsuariosView.php", $dataMerge);
            exit();
        }
        echo $this->render->render("view/administrarUsuariosView.php");
    }

    public function darDeBajaEmpleado()
    {
        $idEmpleadoAEliminar = $_POST["botonDarDeBajaEmpleadoModal"];

        $empleadoChofer = $this->administrarUsuarioModel->verificarSiUnEmpleadoEsUnChofer($idEmpleadoAEliminar);
        if($empleadoChofer){
            $choferViaje = $this->administrarUsuarioModel->verificarSiChoferEstaEnViajeActivoOPendiente($idEmpleadoAEliminar);
            if($choferViaje){
                header("Location: /administrarUsuarios?empleadoChoferOcupado=true");
                exit();
            }
        }
        $this->administrarUsuarioModel->eliminarEmpleado($idEmpleadoAEliminar);

        header("Location: /administrarUsuarios?bajaEmpleado=true");
        exit();
    }

    public function darDeBajaUsuario()
    {
        $dniUsuarioAEliminar = $_POST["botonDarDeBajaUsuarioModal"];

        $this->administrarUsuarioModel->eliminarUsuario($dniUsuarioAEliminar);

        header("Location: /administrarUsuarios?bajaUsuario=true");
        exit();
    }

    public function modificarUsuario()
    {
        $nombreUsuarioAModificar = $_POST["nombreUsuario"];
        $nombreModificar = $_POST["nombre"];
        $apellidoAModificar = $_POST["apellido"];
        $dniAModificar = $_POST["dni"];
        $fechaNacimientoAModificar = $_POST["fechaNacimiento"];
        $dniUsuarioQueSeVaAModificar = $_POST["botonModificar"];

        $nombreUsuarioExistente = $this->administrarUsuarioModel->verificarNombreUsuarioExistente($nombreUsuarioAModificar, $dniUsuarioQueSeVaAModificar);
        $dniExistente = $this->administrarUsuarioModel->verificarDNIUsuarioExistente($dniAModificar, $dniUsuarioQueSeVaAModificar);

        if ($nombreUsuarioExistente and $dniExistente) {
            header("Location: /administrarUsuarios?nombreUsuarioExistente=true&dniExistente=true");
            exit();
        } elseif ($nombreUsuarioExistente) {
            header("Location: /administrarUsuarios?nombreUsuarioExistente=true");
            exit();
        } elseif ($dniExistente) {
            header("Location: /administrarUsuarios?dniExistente=true");
            exit();
        }

        $this->administrarUsuarioModel->modificarUsuario($nombreUsuarioAModificar,
            $nombreModificar, $apellidoAModificar,
            $dniAModificar, $fechaNacimientoAModificar, $dniUsuarioQueSeVaAModificar);

        header("Location: /administrarUsuarios?modificarUsuario=true");
        exit();
    }

    public function modificarEmpleado()
    {
        $nombreUsuarioAModificar = $_POST["nombreUsuario"];
        $nombreModificar = $_POST["nombre"];
        $apellidoAModificar = $_POST["apellido"];
        $dniAModificar = $_POST["dni"];
        $fechaNacimientoAModificar = $_POST["fechaNacimiento"];
        $dniUsuarioQueSeVaAModificar = $_POST["botonModificar"];
        $tipoLicenciaAModificar = $_POST["tipoLicencia"];
        $rolAModificar = $_POST["rol"];
        $idEmpleado = $_POST["idEmpleado"];


        $nombreUsuarioExistente = $this->administrarUsuarioModel->verificarNombreUsuarioExistente($nombreUsuarioAModificar, $dniUsuarioQueSeVaAModificar);
        $dniExistente = $this->administrarUsuarioModel->verificarDNIUsuarioExistente($dniAModificar, $dniUsuarioQueSeVaAModificar);

        if ($nombreUsuarioExistente and $dniExistente) {
            header("Location: /administrarUsuarios?nombreUsuarioExistente=true&dniExistente=true");
            exit();
        } elseif ($nombreUsuarioExistente) {
            header("Location: /administrarUsuarios?nombreUsuarioExistente=true");
            exit();
        } elseif ($dniExistente) {
            header("Location: /administrarUsuarios?dniExistente=true");
            exit();
        }

        $this->administrarUsuarioModel->modificarEmpleado($nombreUsuarioAModificar,
            $nombreModificar, $apellidoAModificar, $dniAModificar,
            $fechaNacimientoAModificar, $tipoLicenciaAModificar, $rolAModificar, $idEmpleado, $dniUsuarioQueSeVaAModificar);

        header("Location: /administrarUsuarios?modificarEmpleado=true");
        exit();
    }


}