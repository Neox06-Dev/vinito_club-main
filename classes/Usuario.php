<?php

class Usuario
{
    private int $id_usuario;
    private string $nombre;
    private string $email;
    private string $telefono;
    private string $password;
    private string $rol;

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

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public static function buscarPorLogin(string $login): ?Usuario
    {
        $conexion = (new Conexion())->getConexion();

        $login = trim($login);

        $query = "SELECT *
                FROM usuarios
                WHERE email = '$login'
                OR nombre = '$login'
                LIMIT 1";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $stmt->execute();

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
}