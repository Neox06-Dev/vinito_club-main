<?php
require_once 'Categoria.php';
require_once 'Conexion.php';
require_once 'Varietal.php';

class Vino
{
    private int $id_vino;
    private string $nombre;
    private string $descripcion;
    private float $precio;
    private int $stock;
    private string $imagen;
    private string $anio_cosecha;
    private int $volumen_ml;
    private string $temperatura_servicio;
    private string $bodega;
    private int $categoria_id;
    private string $maridaje;
    private int $destacado;
    private string $region;


    // CONSTRUCTOR
    public function __construct(
        ?int $id_vino = null,
        string $nombre = '',
        string $descripcion = '',
        float $precio = 0,
        int $stock = 0,
        string $imagen = '',
        string $anio_cosecha = '',
        int $volumen_ml = 0,
        string $temperatura_servicio = '',
        string $bodega = '',
        int $categoria_id = 0,
        string $maridaje = '',
        int $destacado = 0,
        string $region = ''
    )
    {
        $this->id_vino = $id_vino ?? 0;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->imagen = $imagen;
        $this->anio_cosecha = $anio_cosecha;
        $this->volumen_ml = $volumen_ml;
        $this->temperatura_servicio = $temperatura_servicio;
        $this->bodega = $bodega;
        $this->categoria_id = $categoria_id;
        $this->maridaje = $maridaje;
        $this->destacado = $destacado;
        $this->region = $region;
    }

    // MÉTODOS DE CONSULTA
    public static function catalogo_completo(): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT * FROM vinos";

        $PDOStatement = $conexion->prepare($query);

        $PDOStatement->setFetchMode(
            PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
            self::class
        );

        $PDOStatement->execute();

        return $PDOStatement->fetchAll();
    }

    public static function vino_por_id(int $id): ?Vino
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT * FROM vinos
            WHERE id_vino = ?";

        $PDOStatement = $conexion->prepare($query);

        $PDOStatement->setFetchMode(
            PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
            self::class
        );

        $PDOStatement->execute([$id]);

        $resultado = $PDOStatement->fetch();

        return $resultado ?: null;
    }

    public static function ultimos(int $limite = 4): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT *
            FROM vinos
            ORDER BY id_vino DESC
            LIMIT ?
        ";

        $stmt = $conexion->prepare($query);

        $stmt->bindValue(
            1,
            $limite,
            PDO::PARAM_INT
        );

        $stmt->setFetchMode(
            PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
            self::class
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    // GETTERS
    public function getIdVino(): int
    {
        return $this->id_vino;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getPrecioFormateado(): string
    {
        return '$' . number_format($this->precio, 2, ',', '.');
    }

    public function estaEnStock(): bool
    {
        return $this->stock > 0;
    }

    public function getImagenSrc(): string
    {
        return 'assets/img/productos/' . $this->imagen;
    }

    public function getAnio(): string
    {
        return date(
            'Y',
            strtotime($this->anio_cosecha)
        );
    }

    public function getVolumenMl(): int
    {
        return $this->volumen_ml;
    }

    public function getTemperaturaServicio(): string
    {
        return $this->temperatura_servicio;
    }

    public function getBodega(): string
    {
        return $this->bodega;
    }
    
    public function getCategoria(): ?Categoria
    {
        return Categoria::porId($this->categoria_id);
    }
    
    public function getCategoriaId(): int
    {
        return $this->categoria_id;
    }

    public function getCategoriaLabel(): string
    {
        $categoria = $this->getCategoria();

        return $categoria
            ? $categoria->getNombre()
            : 'Sin categoría';
    }
    
    
    public function getMaridaje(): string
    {
        return $this->maridaje;
    }

    public function getDestacado(): int
    {
        return $this->destacado;
    }

    public function getRegion(): string
    {
        return $this->region;
    }


    public function getStock(): int
    {
        return $this->stock;
    }

    public function getVarietales(): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT v.*
            FROM varietales v
            JOIN vino_varietal vv
            ON v.id_varietal = vv.varietal_id
            WHERE vv.vino_id = ?
        ";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            Varietal::class
        );

        $stmt->execute([$this->id_vino]);

        return $stmt->fetchAll();
    }

    public function getAnioCosecha(): string
    {
        return $this->anio_cosecha;
    }

    public function getImagen(): string
    {
        return $this->imagen;
    }

    // MÉTODOS DE GESTIÓN
    public function crear(): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            INSERT INTO vinos
            (
                nombre,
                descripcion,
                precio,
                stock,
                imagen,
                anio_cosecha,
                volumen_ml,
                temperatura_servicio,
                bodega,
                categoria_id,
                maridaje,
                destacado,
                region
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            $this->nombre,
            $this->descripcion,
            $this->precio,
            $this->stock,
            $this->imagen,
            $this->anio_cosecha,
            $this->volumen_ml,
            $this->temperatura_servicio,
            $this->bodega,
            $this->categoria_id,
            $this->maridaje,
            $this->destacado,
            $this->region
        ]);
    }

    public function editar(): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            UPDATE vinos
            SET
                nombre = ?,
                descripcion = ?,
                precio = ?,
                stock = ?,
                imagen = ?,
                anio_cosecha = ?,
                volumen_ml = ?,
                temperatura_servicio = ?,
                bodega = ?,
                categoria_id = ?,
                maridaje = ?,
                destacado = ?,
                region = ?
            WHERE id_vino = ?
        ";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            $this->nombre,
            $this->descripcion,
            $this->precio,
            $this->stock,
            $this->imagen,
            $this->anio_cosecha,
            $this->volumen_ml,
            $this->temperatura_servicio,
            $this->bodega,
            $this->categoria_id,
            $this->maridaje,
            $this->destacado,
            $this->region,
            $this->id_vino
        ]);
    }

    public function eliminar(): bool
    {
        $conexion = (new Conexion())->getConexion();

        // eliminar relaciones vino-varietal
        $queryRelacion = "
            DELETE FROM vino_varietal
            WHERE vino_id = ?
        ";

        $stmtRelacion = $conexion->prepare($queryRelacion);
        $stmtRelacion->execute([$this->id_vino]);

        // eliminar vino
        $queryVino = "
            DELETE FROM vinos
            WHERE id_vino = ?
        ";

        $stmtVino = $conexion->prepare($queryVino);

        return $stmtVino->execute([
            $this->id_vino
        ]);
    }


    //SETTERS
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setPrecio(float $precio): void
    {
        $this->precio = $precio;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function setImagen(string $imagen): void
    {
        $this->imagen = $imagen;
    }

    public function setAnioCosecha(string $anio_cosecha): void
    {
        $this->anio_cosecha = $anio_cosecha;
    }

    public function setVolumenMl(int $volumen_ml): void
    {
        $this->volumen_ml = $volumen_ml;
    }

    public function setTemperaturaServicio(string $temperatura_servicio): void
    {
        $this->temperatura_servicio = $temperatura_servicio;
    }

    public function setBodega(string $bodega): void
    {
        $this->bodega = $bodega;
    }

    public function setCategoriaId(int $categoria_id): void
    {
        $this->categoria_id = $categoria_id;
    }

    public function setMaridaje(string $maridaje): void
    {
        $this->maridaje = $maridaje;
    }

    public function setDestacado(int $destacado): void
    {
        $this->destacado = $destacado;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }
}