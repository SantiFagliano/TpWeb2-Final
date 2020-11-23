<?php


class LoginSession
{
    public function verificarQueUsuarioEsteLogeado(){
        $logeado = isset( $_SESSION["logeado"]) ?  $_SESSION["logeado"] : null;
        if($logeado == 1){
            return true;
        }
        return false;
    }

    public function verificarQueUsuarioEsAdmin(){
        $usuarioAdmin = false;
        if($_SESSION["rol"] == "admin"){
            $usuarioAdmin  = true;
        }
        return $usuarioAdmin;
    }
}