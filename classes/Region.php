<?php
require_once 'Conexion.php';

class Region
{
    private int $id_region;
    private string $nombre;
    private string $error = '';

    public static function todas(): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT
                id_region,
                nombre
            FROM regiones
            ORDER BY id_region DESC
        ";

        $PDOStatement = $conexion->prepare($query);

        $PDOStatement->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $PDOStatement->execute();

        return $PDOStatement->fetchAll();
    }

    public static function porId(int $id): ?Region
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT
                id_region,
                nombre
            FROM regiones
            WHERE id_region = :id_region
        ";

        $PDOStatement = $conexion->prepare($query);

        $PDOStatement->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $PDOStatement->execute([
            ':id_region' => $id
        ]);

        return $PDOStatement->fetch() ?: null;
    }


    public static function existeNombre(string $nombre): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT COUNT(*) 
            FROM regiones
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
            $this->error = 'El nombre de la región es obligatorio.';
            return false;
        }

        if (self::existeNombre($this->nombre)) {
            $this->error = 'La región ya existe.';
            return false;
        }

        $conexion = (new Conexion())->getConexion();

        $query = "
            INSERT INTO regiones (
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
            FROM regiones
            WHERE LOWER(nombre) = LOWER(:nombre)
            AND id_region <> :id_region
        ";

        $stmtExiste = $conexion->prepare($queryExiste);

        $stmtExiste->execute([
            ':nombre' => trim($this->nombre),
            ':id_region' => $this->id_region
        ]);

        if ($stmtExiste->fetchColumn() > 0) {
            $this->error = 'Ya existe una región con ese nombre.';
            return false;
        }

        $query = "
            UPDATE regiones
            SET nombre = :nombre
            WHERE id_region = :id_region
        ";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            ':nombre' => trim($this->nombre),
            ':id_region' => $this->id_region
        ]);
    }

    public function eliminar(): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT COUNT(*)
            FROM vinos
            WHERE region_id = :id_region
        ";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            ':id_region' => $this->id_region
        ]);

        if ($stmt->fetchColumn() > 0) {
            $this->error = 'No se puede eliminar porque existen vinos asociados.';
            return false;
        }

        $query = "
            DELETE FROM regiones
            WHERE id_region = :id_region
        ";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            ':id_region' => $this->id_region
        ]);
    }

    // GETTERS

    public function getId(): int
    {
        return $this->id_region;
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