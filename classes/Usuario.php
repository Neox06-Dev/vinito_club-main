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
    private ?string $direccion;

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

    public function getDireccion(): ?string
    {
        return $this->direccion ?? null;
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

    // Método para actualizar la dirección del usuario

    public static function actualizarDireccion(int $id, string $direccion): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "UPDATE usuarios
            SET direccion = ?
            WHERE id_usuario = ?";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([$direccion, $id]);
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

        try {
            $conexion->beginTransaction();

            // 1. Obtener los pedidos del usuario
            $queryPedidos = "SELECT id_pedido FROM pedidos WHERE id_usuario = ?";
            $stmtPedidos = $conexion->prepare($queryPedidos);
            $stmtPedidos->execute([$id]);
            $pedidos = $stmtPedidos->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($pedidos)) {
                $placeholders = implode(',', array_fill(0, count($pedidos), '?'));
                
                // 2. Eliminar detalles de esos pedidos
                $queryDetalles = "DELETE FROM detalle_pedidos WHERE id_pedido IN ($placeholders)";
                $stmtDetalles = $conexion->prepare($queryDetalles);
                $stmtDetalles->execute($pedidos);

                // 3. Eliminar los pedidos
                $queryDelPedidos = "DELETE FROM pedidos WHERE id_usuario = ?";
                $stmtDelPedidos = $conexion->prepare($queryDelPedidos);
                $stmtDelPedidos->execute([$id]);
            }

            // 4. Finalmente eliminar el usuario
            $queryUsuario = "DELETE FROM usuarios WHERE id_usuario = ?";
            $stmtUsuario = $conexion->prepare($queryUsuario);
            $resultado = $stmtUsuario->execute([$id]);

            $conexion->commit();
            return $resultado;

        } catch (Throwable $e) {
            $conexion->rollBack();
            return false;
        }
    }

}