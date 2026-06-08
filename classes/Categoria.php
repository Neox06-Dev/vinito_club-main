<?php
require_once 'Conexion.php';

class Categoria
{
    private int $id_categoria;
    private string $nombre;

    public static function todas(): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT * FROM categorias";

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

        $query = "SELECT * FROM categorias
            WHERE id_categoria = ?";

        $PDOStatement = $conexion->prepare($query);

        $PDOStatement->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $PDOStatement->execute([$id]);

        $resultado = $PDOStatement->fetch();

        return $resultado ?: null;
    }

    // Getters

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getId(): int
    {
        return $this->id_categoria;
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
}