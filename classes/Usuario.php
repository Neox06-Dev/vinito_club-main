<?php

require_once __DIR__ . '/Conexion.php';

class Usuario
{
    private int $id_usuario;
    private string $nombre;
    private string $email;
    private ?string $telefono;
    private string $password;
    private string $rol;
    private string $fecha_registro;

    public static function buscarPorEmail(string $email): ?Usuario
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT *
            FROM usuarios
            WHERE email = ?";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $stmt->execute([$email]);

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public function verificarPassword(string $password): bool
    {
        return password_verify(
            $password,
            $this->password
        );
    }

    // Getters

    public function getId(): int
    {
        return $this->id_usuario;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public function getFechaRegistro(): string
    {
        return $this->fecha_registro;
    }

    public static function buscarPorLogin(string $login): ?Usuario
    {
        $conexion = (new Conexion())->getConexion();

        $login = trim($login);

        $query = "SELECT *
                FROM usuarios
                WHERE email = ?
                OR nombre = ?
                LIMIT 1";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $stmt->execute([$login, $login]);

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }


    public static function buscarPorId(int $id): ?Usuario
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT *
            FROM usuarios
            WHERE id_usuario = ?";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $stmt->execute([$id]);

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    // Método para registrar un nuevo usuario

    public static function registrar(
        string $nombre,
        string $email,
        string $telefono,
        string $password
    ): bool {

        if (self::emailExiste($email)) {
            return false;
        }

        $conexion = (new Conexion())->getConexion();

        $email = trim(strtolower($email));

        $query = "INSERT INTO usuarios
                (nombre, email, telefono, password, rol)
                VALUES (?, ?, ?, ?, 'cliente')";

        $stmt = $conexion->prepare($query);

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        return $stmt->execute([
            $nombre,
            $email,
            $telefono,
            $passwordHash
        ]);
    }

    public static function emailExiste(string $email): bool
    {
        return self::buscarPorEmail($email) !== null;
    }

    // Verifica si el email ya está en uso por OTRO usuario (para edición de perfil)

    public static function emailExisteParaOtro(string $email, int $idUsuario): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT COUNT(*)
            FROM usuarios
            WHERE email = ?
            AND id_usuario != ?";

        $stmt = $conexion->prepare($query);

        $stmt->execute([$email, $idUsuario]);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Método para actualizar los datos del perfil (nombre, email, teléfono)

    public static function actualizarPerfil(
        int $id,
        string $nombre,
        string $email,
        string $telefono
    ): bool {

        $conexion = (new Conexion())->getConexion();

        $email = trim(strtolower($email));

        $query = "UPDATE usuarios
            SET nombre = ?, email = ?, telefono = ?
            WHERE id_usuario = ?";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            $nombre,
            $email,
            $telefono,
            $id
        ]);
    }

    // Método para actualizar la contraseña del usuario

    public static function actualizarPassword(int $id, string $nuevaPassword): bool
    {
        $conexion = (new Conexion())->getConexion();

        $passwordHash = password_hash(
            $nuevaPassword,
            PASSWORD_DEFAULT
        );

        $query = "UPDATE usuarios
            SET password = ?
            WHERE id_usuario = ?";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            $passwordHash,
            $id
        ]);
    }

    // Método para eliminar la cuenta del usuario

    public static function eliminar(int $id): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "DELETE FROM usuarios
            WHERE id_usuario = ?";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([$id]);
    }

    
}