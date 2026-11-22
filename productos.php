<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "tienda_tuning_db");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consultar únicamente los productos activos
$sql = "SELECT * FROM productos WHERE estado = 'activo' OR estado IS NULL";
$result = $conexion->query($sql);

$cant_carrito = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - Tienda Tuning</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { background-color: #0a0a0a; color: #fff; }
        
        .top-bar { background-color: #111; color: #ccc; font-size: 13px; padding: 10px 50px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; }
        .top-bar .auth-btns a { color: #fff; text-decoration: none; padding: 6px 12px; margin-left: 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase; display: inline-block; }
        .btn-login { background-color: #e50914; }
        .btn-login:hover { background-color: #b20710; }
        .btn-perfil { background-color: #222; border: 1px solid #444; }
        .btn-perfil:hover { background-color: #333; }
        .btn-admin { background-color: #0277bd; }
        .btn-admin:hover { background-color: #01579b; }
        .btn-logout { background-color: #333; }
        .btn-logout:hover { background-color: #555; }

        header { background-color: #000; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e50914; }
        .logo h1 { font-size: 26px; text-transform: uppercase; color: #fff; letter-spacing: 1px; }
        .logo span { color: #e50914; }
        nav a { color: #fff; text-decoration: none; margin-left: 20px; font-weight: bold; font-size: 14px; text-transform: uppercase; }
        nav a:hover { color: #e50914; }

        .hero { background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/img/hero-bg.jpg') center/cover no-repeat; padding: 60px 20px; text-align: center; border-bottom: 1px solid #222; }
        .hero h2 { font-size: 32px; text-transform: uppercase; margin-bottom: 10px; }
        .hero p { color: #aaa; font-size: 16px; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .section-title { font-size: 22px; text-transform: uppercase; margin-bottom: 25px; border-left: 4px solid #e50914; padding-left: 10px; }

        .grid-productos { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; }
        .card-producto { background-color: #141414; border: 1px solid #262626; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s ease, border-color 0.2s ease; }
        .card-producto:hover { transform: translateY(-5px); border-color: #e50914; }
        .img-box { width: 100%; height: 200px; background-color: #1f1f1f; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .img-box img { width: 100%; height: 100%; object-fit: cover; }
        .no-img { color: #666; font-size: 12px; text-transform: uppercase; }

        .info-box { padding: 15px; display: flex; flex-direction: column; flex-grow: 1; }
        .info-box h3 { font-size: 16px; margin-bottom: 8px; color: #fff; }
        .info-box p { font-size: 13px; color: #aaa; margin-bottom: 15px; flex-grow: 1; line-height: 1.4; }
        .precio { font-size: 18px; font-weight: bold; color: #e50914; margin-bottom: 12px; }
        .btn-comprar { width: 100%; background-color: #e50914; color: #fff; border: none; padding: 10px; font-weight: bold; border-radius: 4px; cursor: pointer; text-transform: uppercase; text-align: center; text-decoration: none; display: block; font-size: 13px; }
        .btn-comprar:hover { background-color: #b20710; }
    </style>
</head>
<body>

    <!-- Barra superior de autenticación -->
    <div class="top-bar">
        <div>
            <span>Bienvenido a <strong>Tienda Tuning</strong></span>
        </div>
        <div class="auth-btns">
            <?php if (isset($_SESSION['usuario'])): ?>
                <span style="margin-right: 10px;">Hola, <?php echo htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario']); ?></span>
                <a href="api/perfil.php" class="btn-perfil">Mi Perfil</a>
                <?php if (isset($_SESSION['rol']) && (strtolower(trim($_SESSION['rol'])) === 'admin' || strtolower(trim($_SESSION['rol'])) === 'administrador')): ?>
                    <a href="api/admin_productos.php" class="btn-admin">Panel Admin</a>
                <?php endif; ?>
                <a href="api/logout.php" class="btn-logout">Cerrar Sesión</a>
            <?php else: ?>
                <a href="api/login.php" class="btn-login">Iniciar Sesión</a>
                <a href="api/registro.php" class="btn-perfil">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Menú Principal con Inicio, Catálogo, Mis Pedidos y Carrito -->
    <header>
        <div class="logo">
            <h1>Tienda <span>Tuning</span></h1>
        </div>
        <nav>
            <a href="index.html">Inicio</a>
            <a href="productos.php">Catálogo</a>
            <a href="api/pedidos.php">Mis Pedidos</a>
            <a href="api/carrito.php" style="background: #e50914; padding: 6px 12px; border-radius: 4px;">Carrito (<?php echo $cant_carrito; ?>)</a>
        </nav>
    </header>

    <div class="hero">
        <h2>Personaliza tu Vehículo</h2>
        <p>Encuentra los mejores accesorios y repuestos tuning para tu auto</p>
    </div>

    <div class="container">
        <h2 class="section-title">Productos Disponibles</h2>

        <div class="grid-productos">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card-producto">
                        <div class="img-box">
                            <?php if (!empty($row['imagen']) && file_exists($row['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($row['imagen']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                            <?php else: ?>
                                <span class="no-img">Sin imagen</span>
                            <?php endif; ?>
                        </div>
                        <div class="info-box">
                            <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                            <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                            <div class="precio">$<?php echo number_format($row['precio'], 0, ',', '.'); ?></div>
                            <a href="api/carrito.php?accion=agregar&id=<?php echo $row['id_producto'] ?? $row['id']; ?>" class="btn-comprar">Agregar al Carrito</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #888; grid-column: 1 / -1; text-align: center;">No hay productos registrados en este momento.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php
$conexion->close();
?>