<?php
require_once 'Conexion.php';

class Categoria
{
    private int $id_categoria;
    private string $nombre;
    private string $error = '';

    public static function todas(): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT
                id_categoria,
                nombre
            FROM categorias
            ORDER BY id_categoria DESC
        ";

        $PDOStatement = $conexion->prepare($query);

        $PDOStatement->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $PDOStatement->execute();

        return $PDOStatement->fetchAll();
    }

    public static function porId(int $id): ?Categoria
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT
                id_categoria,
                nombre
            FROM categorias
            WHERE id_categoria = :id_categoria
        ";

        $PDOStatement = $conexion->prepare($query);

        $PDOStatement->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $PDOStatement->execute([
            ':id_categoria' => $id
        ]);

        return $PDOStatement->fetch() ?: null;
    }


    public static function existeNombre(string $nombre): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT COUNT(*) 
            FROM categorias
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
            $this->error = 'El nombre de la categoría es obligatorio.';
            return false;
        }

        if (self::existeNombre($this->nombre)) {
            $this->error = 'La categoría ya existe.';
            return false;
        }

        $conexion = (new Conexion())->getConexion();

        $query = "
            INSERT INTO categorias (
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
            FROM categorias
            WHERE LOWER(nombre) = LOWER(:nombre)
            AND id_categoria <> :id_categoria
        ";

        $stmtExiste = $conexion->prepare($queryExiste);

        $stmtExiste->execute([
            ':nombre' => trim($this->nombre),
            ':id_categoria' => $this->id_categoria
        ]);

        if ($stmtExiste->fetchColumn() > 0) {
            $this->error = 'Ya existe una categoría con ese nombre.';
            return false;
        }

        $query = "
            UPDATE categorias
            SET nombre = :nombre
            WHERE id_categoria = :id_categoria
        ";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            ':nombre' => trim($this->nombre),
            ':id_categoria' => $this->id_categoria
        ]);
    }

    public function eliminar(): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT COUNT(*)
            FROM vinos
            WHERE categoria_id = :id_categoria
        ";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            ':id_categoria' => $this->id_categoria
        ]);

        if ($stmt->fetchColumn() > 0) {
            $this->error = 'No se puede eliminar porque existen vinos asociados.';
            return false;
        }

        $query = "
            DELETE FROM categorias
            WHERE id_categoria = :id_categoria
        ";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            ':id_categoria' => $this->id_categoria
        ]);
    }

    // GETTERS

    public function getId(): int
    {
        return $this->id_categoria;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getClaseCss(): string
    {
        return match (strtolower($this->nombre)) {
            'tinto' => 'badge-tinto',
            'blanco' => 'badge-blanco',
            'rosé', 'rose' => 'badge-rose',
            'espumante' => 'badge-espumante',
            'dulce' => 'badge-dulce',
            default => 'badge-especial'
        };
    }

    //SETTERS
    public function setNombre(string $nombre): void
    {
        $this->nombre = trim($nombre);
    }
    
}