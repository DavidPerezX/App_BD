<?php

namespace Model;

class Usuario extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es Obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        if(strlen($this->password) < 9) {
            self::$alertas['error'][] = 'El password debe contener al menos 9 caracteres';
        }
        if (!preg_match('~[A-Z]~', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos una letra mayúscula';
        }
        if (!preg_match('~[a-z]~', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos una letra minúscula';
        }
        if (!preg_match('~[0-9]~', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos un número';
        }
        if (!preg_match('~[\W]~', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos un símbolo';
        }



        return self::$alertas;
    }

    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es Obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }

        return self::$alertas;
    }
    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es Obligatorio';
        }
        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if(strlen($this->password) < 9) {
            self::$alertas['error'][] = 'El Password debe tener al menos 9 caracteres';
        }
        if (!preg_match('~[A-Z]~', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos una letra mayúscula';
        }
        if (!preg_match('~[a-z]~', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos una letra minúscula';
        }
        if (!preg_match('~[0-9]~', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos un número';
        }
        if (!preg_match('~[\W]~', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos un símbolo';
        }

        return self::$alertas;
    }

    // Revisa si el usuario ya existe
    public function existeUsuario() {
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);

        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El Usuario ya esta registrado';
        }

        return $resultado;
    }

    public function hashPassword() {
        $salt = random_bytes(16);
        $this->password = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12, 'salt' => $salt]);
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password, $this->password);
        
        if(!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Password Incorrecto o tu cuenta no ha sido confirmada';
        } else {
            return true;
        }
    }

}