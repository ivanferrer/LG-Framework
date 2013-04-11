<?php
namespace core;
class Globals {

    private static $tipo;
    private static $classe;
    private static $metodo;
    private static $urlChamada;
    private static $anterior;
    private static $alertMensagem;
    private static $alertTipo;

    public static function getTipo() {
        return Globals::$tipo;
    }
    
    public static function setTipo($tipo) {
        Globals::$tipo = $tipo;
    }
    
    public static function getClasse() {
        return Globals::$classe;
    }
    
    public static function setClasse($classe) {
        Globals::$classe = $classe;
    }
    
    public static function getMetodo() {
        return Globals::$metodo;
    }
    
    public static function setMetodo($metodo) {
        Globals::$metodo = $metodo;
    }
    
    public static function getUrlChamada() {
        return Globals::$urlChamada;
    }
    
    public static function setUrlChamada($urlChamada) {
        Globals::$urlChamada = $urlChamada;
    }
    
    public static function getAnterior() {
        return Globals::$anterior;
    }
    
    public static function setAnterior($anterior) {
        Globals::$anterior = $anterior;
    }
    
    public static function getAlertMensagem() {
        return Globals::$alertMensagem;
    }
    
    public static function setAlertMensagem($alertMensagem) {
        Globals::$alertMensagem = $alertMensagem;
    }
    
    public static function getAlertTipo() {
        return Globals::$alertTipo;
    }
    
    public static function setAlertTipo($alertTipo) {
        Globals::$alertTipo = $alertTipo;
    }

}
