<?php
require 'database.php';
$db = new Database();
$conn = $db->getConnection();

$error = "";
$success = "";

// Crear tabla si no existe (SQLite)
$conn->exec("CREATE TABLE IF NOT EXISTS historial_medico (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente TEXT NOT NULL,
    descripcion TEXT,
    fecha TEXT NOT NULL
)");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paciente = $_POST['paciente'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $id = $_POST['id'] ?? null;

    if ($_POST['action'] === 'edit' && $id) {
        $stmt = $conn->prepare("UPDATE historial_medico SET paciente=?, descripcion=?, fecha=? WHERE id=?");
        $stmt->execute([$paciente, $descripcion, $fecha, $id]);
        $success = "Registro actualizado correctamente.";
    } elseif ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO historial_medico (paciente, descripcion, fecha) VALUES (?,?,?)");
        $stmt->execute([$paciente, $descripcion, $fecha]);
        $success = "Registro agregado correctamente.";
    }
}

// Eliminar registro
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM historial_medico WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    $success = "Registro eliminado correctamente.";
}

// Obtener todos los registros
$historiales = $conn->query("SELECT * FROM historial_medico")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="form-section">
    <h2 id="form-title">Agregar Registro</h2>
    <?php if($error) echo "<div class='message-error'>".htmlspecialchars($error)."</div>"; ?>
    <?php if($success) echo "<div class='message-success'>".htmlspecialchars($success)."</div>"; ?>
    <form method="post" id="historial-form">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="action" value="add">
        <input type="text" name="paciente" placeholder="Paciente" required>
        <input type="text" name="descripcion" placeholder="Descripción">
        <input type="date" name="fecha" required>
        <button type="submit" id="submit-btn">Guardar</button>
    </form>
</div>

<div class="list-section">
    <table>
        <thead>
            <tr><th>ID</th><th>Paciente</th><th>Descripción</th><th>Fecha</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            <?php foreach($historiales as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($h['id']) ?></td>
                    <td><?= htmlspecialchars($h['paciente']) ?></td>
                    <td><?= htmlspecialchars($h['descripcion']) ?></td>
                    <td><?= htmlspecialchars($h['fecha']) ?></td>
                    <td class="action-buttons">
                        <button onclick="editarHistorial(<?= $h['id'] ?>, '<?= addslashes($h['paciente']) ?>', '<?= addslashes($h['descripcion']) ?>', '<?= $h['fecha'] ?>')">Editar</button>
                        <button onclick="if(confirm('Eliminar?')) location.href='?delete=<?= $h['id'] ?>'">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function editarHistorial(id, paciente, descripcion, fecha) {
    const form = document.getElementById('historial-form');
    form.id.value = id;
    form.paciente.value = paciente;
    form.descripcion.value = descripcion;
    form.fecha.value = fecha;
    form.action.value = 'edit';
    document.getElementById('submit-btn').textContent = 'Actualizar';
    document.getElementById('form-title').textContent = 'Editar Registro';
}
</script>
