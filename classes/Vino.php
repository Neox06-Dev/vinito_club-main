<?php
require_once 'Categoria.php';
require_once 'Conexion.php';
require_once 'Varietal.php';
require_once 'Region.php';

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
    private int $region_id;
    private ?int $varietal_id = null;


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
        int $region_id = 0
    ) {
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
        $this->region_id = $region_id;
    }

    // MÉTODOS DE CONSULTA
    public static function catalogo_completo(): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT
                id_vino,
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
                region_id,
                maridaje,
                destacado
            FROM vinos
            ORDER BY id_vino DESC
        ";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
            self::class
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function vino_por_id(int $id): ?Vino
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT
                id_vino,
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
                region_id,
                maridaje,
                destacado
            FROM vinos
            WHERE id_vino = :id_vino
        ";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
            self::class
        );

        $stmt->execute([
            ':id_vino' => $id
        ]);

        return $stmt->fetch() ?: null;
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

    public function getRegionId(): int
    {
        return $this->region_id;
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

    public function getRegion(): ?Region
    {
        return Region::porId($this->region_id);
    }

    public function getRegionNombre(): string
    {
        $region = $this->getRegion();

        return $region
            ? $region->getNombre()
            : 'Sin región';
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

        if (Region::porId($this->region_id) === null) {
            return false;
        }

        $query = "
        INSERT INTO vinos (
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
            region_id,
            maridaje,
            destacado
        )
        VALUES (
            :nombre,
            :descripcion,
            :precio,
            :stock,
            :imagen,
            :anio_cosecha,
            :volumen_ml,
            :temperatura_servicio,
            :bodega,
            :categoria_id,
            :region_id,
            :maridaje,
            :destacado
        )
    ";

        $stmt = $conexion->prepare($query);

        $resultado = $stmt->execute([

            // Información principal
            ':nombre' => $this->nombre,
            ':descripcion' => $this->descripcion,

            // Precio y stock
            ':precio' => $this->precio,
            ':stock' => $this->stock,

            // Imagen
            ':imagen' => $this->imagen,

            // Características
            ':anio_cosecha' => $this->anio_cosecha,
            ':volumen_ml' => $this->volumen_ml,
            ':temperatura_servicio' => $this->temperatura_servicio,

            // Relaciones
            ':bodega' => $this->bodega,
            ':categoria_id' => $this->categoria_id,
            ':region_id' => $this->region_id,

            // Extras
            ':maridaje' => $this->maridaje,
            ':destacado' => $this->destacado
        ]);

        if (!$resultado) {
            return false;
        }

        $idVino = $conexion->lastInsertId();

        $queryVarietal = "
            INSERT INTO vino_varietal (
                vino_id,
                varietal_id
            )
            VALUES (
                :vino_id,
                :varietal_id
            )
        ";

        $stmtVarietal = $conexion->prepare($queryVarietal);

        $stmtVarietal->execute([
            ':vino_id' => $idVino,
            ':varietal_id' => $this->varietal_id
        ]);

        return true;
    }

    public function editar(): bool
    {
        $conexion = (new Conexion())->getConexion();

        if (Region::porId($this->region_id) === null) {
            return false;
        }

        $query = "
            UPDATE vinos
            SET
                nombre = :nombre,
                descripcion = :descripcion,
                precio = :precio,
                stock = :stock,
                imagen = :imagen,
                anio_cosecha = :anio_cosecha,
                volumen_ml = :volumen_ml,
                temperatura_servicio = :temperatura_servicio,
                bodega = :bodega,
                categoria_id = :categoria_id,
                region_id = :region_id,
                maridaje = :maridaje,
                destacado = :destacado
            WHERE id_vino = :id_vino
        ";

        $stmt = $conexion->prepare($query);

        $resultado = $stmt->execute([

            // Información principal
            ':nombre' => $this->nombre,
            ':descripcion' => $this->descripcion,

            // Precio y stock
            ':precio' => $this->precio,
            ':stock' => $this->stock,

            // Imagen
            ':imagen' => $this->imagen,

            // Características
            ':anio_cosecha' => $this->anio_cosecha,
            ':volumen_ml' => $this->volumen_ml,
            ':temperatura_servicio' => $this->temperatura_servicio,

            // Relaciones
            ':bodega' => $this->bodega,
            ':categoria_id' => $this->categoria_id,
            ':region_id' => $this->region_id,

            // Extras
            ':maridaje' => $this->maridaje,
            ':destacado' => $this->destacado,

            // Id
            ':id_vino' => $this->id_vino
        ]);

        if (!$resultado) {
            return false;
        }

        $queryVarietal = "
            UPDATE vino_varietal
            SET
                varietal_id = :varietal_id
            WHERE vino_id = :vino_id
        ";

        $stmtVarietal = $conexion->prepare($queryVarietal);

        $stmtVarietal->execute([
            ':varietal_id' => $this->varietal_id,
            ':vino_id' => $this->id_vino
        ]);

        return true;
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

    public static function descontarStock(
        PDO $conexion,
        int $idVino,
        int $cantidad
    ): bool
    {

        $query = "
            UPDATE vinos
            SET stock = stock - ?
            WHERE id_vino = ?
            AND stock >= ?
        ";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            $cantidad,
            $idVino,
            $cantidad
        ]);

        return $stmt->rowCount() > 0;
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

    public function setRegionId(int $region_id): void
    {
        $this->region_id = $region_id;
    }

    public function setVarietalId(int $varietal_id): void
    {
        $this->varietal_id = $varietal_id;
    }
}
