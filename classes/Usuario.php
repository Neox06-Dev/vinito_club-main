<?php

class Usuario
{
    private int $id_usuario;
    private string $nombre;
    private string $email;
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

    public function getRol(): string
    {
        return $this->rol;
    }
}