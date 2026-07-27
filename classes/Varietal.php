<?php
require_once 'Conexion.php';

class Varietal
{
    private int $id_varietal;
    private string $nombre;
    private string $error = '';

    public static function todas(): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT
                id_varietal,
                nombre
            FROM varietales
            ORDER BY id_varietal DESC
        ";

        $PDOStatement = $conexion->prepare($query);

        $PDOStatement->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $PDOStatement->execute();

        return $PDOStatement->fetchAll();
    }

    public static function porId(int $id): ?Varietal
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT
                id_varietal,
                nombre
            FROM varietales
            WHERE id_varietal = :id_varietal
        ";

        $PDOStatement = $conexion->prepare($query);

        $PDOStatement->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $PDOStatement->execute([
            ':id_varietal' => $id
        ]);

        return $PDOStatement->fetch() ?: null;
    }


    public static function existeNombre(string $nombre): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT COUNT(*) 
            FROM varietales
            WHERE LOWER(nombre) = LOWER(:nombre)
        ";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            ':nombre' => trim($nombre)
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function crear(): bool
    {
        if (trim($this->nombre) === '') {
            $this->error = 'El nombre del varietal es obligatorio.';
            return false;
        }

        if (self::existeNombre($this->nombre)) {
            $this->error = 'El varietal ya existe.';
            return false;
        }

        $conexion = (new Conexion())->getConexion();

        $query = "
            INSERT INTO varietales (
                nombre
            )
            VALUES (
                :nombre
            )
        ";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            ':nombre' => $this->nombre
        ]);
    }

    public function editar(): bool
    {
        $conexion = (new Conexion())->getConexion();

        $queryExiste = "
            SELECT COUNT(*)
            FROM varietales
            WHERE LOWER(nombre) = LOWER(:nombre)
            AND id_varietal <> :id_varietal
        ";

        $stmtExiste = $conexion->prepare($queryExiste);

        $stmtExiste->execute([
            ':nombre' => trim($this->nombre),
            ':id_varietal' => $this->id_varietal
        ]);

        if ($stmtExiste->fetchColumn() > 0) {
            $this->error = 'Ya existe un varietal con ese nombre.';
            return false;
        }

        $query = "
            UPDATE varietales
            SET nombre = :nombre
            WHERE id_varietal = :id_varietal
        ";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            ':nombre' => trim($this->nombre),
            ':id_varietal' => $this->id_varietal
        ]);
    }

    public function eliminar(): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT COUNT(*)
            FROM vino_varietal
            WHERE varietal_id = :id_varietal
        ";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            ':id_varietal' => $this->id_varietal
        ]);

        if ($stmt->fetchColumn() > 0) {
            $this->error = 'No se puede eliminar porque existen vinos asociados.';
            return false;
        }

        $query = "
            DELETE FROM varietales
            WHERE id_varietal = :id_varietal
        ";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            ':id_varietal' => $this->id_varietal
        ]);
    }

    // GETTERS

    public function getId(): int
    {
        return $this->id_varietal;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getError(): string
    {
        return $this->error;
    }

    //SETTERS
    public function setNombre(string $nombre): void
    {
        $this->nombre = trim($nombre);
    }
}