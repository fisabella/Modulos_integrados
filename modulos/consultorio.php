<?php
require 'database.php';
$db = new Database();
$conn = $db->getConnection();

$error = "";
$success = "";

// Crear tabla si no existe
$conn->exec("CREATE TABLE IF NOT EXISTS consultorio (
    IDConsultorio INTEGER PRIMARY KEY AUTOINCREMENT,
    Descripcion TEXT NOT NULL
)");

// Manejo de formulario: agregar o editar consultorio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $id = $_POST['id'] ?? null;

    if ($_POST['action'] === 'edit' && $id) {
        $stmt = $conn->prepare("UPDATE consultorio SET Descripcion=? WHERE IDConsultorio=?");
        $stmt->execute([$nombre, $id]);
        $success = "Consultorio actualizado correctamente.";
    } elseif ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO consultorio (Descripcion) VALUES (?)");
        $stmt->execute([$nombre]);
        $success = "Consultorio agregado correctamente.";
    }
}

// Eliminar consultorio
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM consultorio WHERE IDConsultorio=?");
    $stmt->execute([$_GET['delete']]);
    $success = "Consultorio eliminado correctamente.";
}

// Obtener todos los consultorios
$consultorios = $conn->query("SELECT * FROM consultorio")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="form-section">
    <h2 id="form-title">Agregar Consultorio</h2>
    <?php if($error) echo "<div class='message-error'>".htmlspecialchars($error)."</div>"; ?>
    <?php if($success) echo "<div class='message-success'>".htmlspecialchars($success)."</div>"; ?>
    <form method="post" id="consultorio-form">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="action" value="add">
        <input type="text" name="nombre" placeholder="Nombre del consultorio" required>
        <button type="submit" id="submit-btn">Guardar</button>
    </form>
</div>

<div class="list-section">
    <h2>Lista de Consultorios</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            <?php foreach($consultorios as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['IDConsultorio']) ?></td>
                    <td><?= htmlspecialchars($c['Descripcion']) ?></td>
                    <td class="action-buttons">
                        <button onclick="editarConsultorio(<?= $c['IDConsultorio'] ?>, '<?= addslashes($c['Descripcion']) ?>')">Editar</button>
                        <button onclick="if(confirm('Eliminar?')) location.href='?module=consultorio&delete=<?= $c['IDConsultorio'] ?>'">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function editarConsultorio(id, nombre) {
    const form = document.getElementById('consultorio-form');
    form.id.value = id;
    form.nombre.value = nombre;
    form.action.value = 'edit';
    document.getElementById('submit-btn').textContent = 'Actualizar';
    document.getElementById('form-title').textContent = 'Editar Consultorio';
}
</script>
