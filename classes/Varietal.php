<?php

require_once 'Conexion.php';

class Varietal
{
    private int $id_varietal;
    private string $nombre;

    public static function porId(int $id): ?Varietal
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT * FROM varietales
                WHERE id_varietal = ?";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $stmt->execute([$id]);

        $resultado = $stmt->fetch();

        return $resultado ?: null;
    }

    public static function todos(): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT * FROM varietales";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }
    
    public function getIdVarietal(): int
    {
        return $this->id_varietal;
    }
}