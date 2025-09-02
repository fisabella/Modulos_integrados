<?php
require 'database.php';
$db = new Database();
$conn = $db->getConnection();

$error = "";
$success = "";


// Crear tabla si no existe
$conn->exec("CREATE TABLE IF NOT EXISTS medico (
    IDMedico INTEGER PRIMARY KEY AUTOINCREMENT,
    NombreMed VARCHAR(100) NOT NULL,
    ApellidoMed VARCHAR(100),
    Especialidad VARCHAR(100),
    No_IdentMed VARCHAR(50),
    DireccionMed VARCHAR(150),
    TelefonoMed VARCHAR(50),
    CorreoMed VARCHAR(100),
    RH VARCHAR(10),
    FechaNac DATE
)");

// Manejo de formulario: agregar o editar doctor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $especialidad = $_POST['especialidad'] ?? '';
    $no_ident = $_POST['no_ident'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $rh = $_POST['rh'] ?? '';
    $fecha_nac = $_POST['fecha_nac'] ?? '';

    if ($_POST['action'] === 'edit' && $id) {
        $stmt = $conn->prepare("UPDATE medico SET 
            NombreMed=?, ApellidoMed=?, Especialidad=?, No_IdentMed=?, DireccionMed=?, TelefonoMed=?, CorreoMed=?, RH=?, FechaNac=? 
            WHERE IDMedico=?");
        $stmt->execute([$nombre, $apellido, $especialidad, $no_ident, $direccion, $telefono, $correo, $rh, $fecha_nac, $id]);
        $success = "Doctor actualizado correctamente.";
    } elseif ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO medico 
            (NombreMed, ApellidoMed, Especialidad, No_IdentMed, DireccionMed, TelefonoMed, CorreoMed, RH, FechaNac) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $apellido, $especialidad, $no_ident, $direccion, $telefono, $correo, $rh, $fecha_nac]);
        $success = "Doctor agregado correctamente.";
    }
}

// Eliminar doctor
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM medico WHERE IDMedico=?");
    $stmt->execute([$_GET['delete']]);
    $success = "Doctor eliminado correctamente.";
}

// Obtener todos los doctores
$doctores = $conn->query("SELECT * FROM medico")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="form-section">
    <h2 id="form-title">Agregar Doctor</h2>
    <?php if($error) echo "<div class='message-error'>".htmlspecialchars($error)."</div>"; ?>
    <?php if($success) echo "<div class='message-success'>".htmlspecialchars($success)."</div>"; ?>
    <form method="post" id="doctor-form">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="action" value="add">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido">
        <input type="text" name="especialidad" placeholder="Especialidad">
        <input type="text" name="no_ident" placeholder="No. Identificación">
        <input type="text" name="direccion" placeholder="Dirección">
        <input type="text" name="telefono" placeholder="Teléfono">
        <input type="email" name="correo" placeholder="Correo">
        <input type="text" name="rh" placeholder="RH">
        <input type="date" name="fecha_nac" placeholder="Fecha Nac">
        <button type="submit" id="submit-btn">Guardar</button>
    </form>
</div>

<div class="list-section">
    <h2>Lista de Doctores</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Especialidad</th>
                <th>No. Ident</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>RH</th>
                <th>Fecha Nac</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctores as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['IDMedico']) ?></td>
                <td><?= htmlspecialchars($d['NombreMed']) ?></td>
                <td><?= htmlspecialchars($d['ApellidoMed']) ?></td>
                <td><?= htmlspecialchars($d['Especialidad']) ?></td>
                <td><?= htmlspecialchars($d['No_IdentMed']) ?></td>
                <td><?= htmlspecialchars($d['DireccionMed']) ?></td>
                <td><?= htmlspecialchars($d['TelefonoMed']) ?></td>
                <td><?= htmlspecialchars($d['CorreoMed']) ?></td>
                <td><?= htmlspecialchars($d['RH']) ?></td>
                <td><?= htmlspecialchars($d['FechaNac']) ?></td>
                <td class="action-buttons">
                    <button onclick="editarDoctor(
                        <?= $d['IDMedico'] ?>,
                        '<?= addslashes($d['NombreMed']) ?>',
                        '<?= addslashes($d['ApellidoMed']) ?>',
                        '<?= addslashes($d['Especialidad']) ?>',
                        '<?= addslashes($d['No_IdentMed']) ?>',
                        '<?= addslashes($d['DireccionMed']) ?>',
                        '<?= addslashes($d['TelefonoMed']) ?>',
                        '<?= addslashes($d['CorreoMed']) ?>',
                        '<?= addslashes($d['RH']) ?>',
                        '<?= $d['FechaNac'] ?>'
                    )">Editar</button>
                    <button onclick="if(confirm('Eliminar?')) location.href='?module=medico&delete=<?= $d['IDMedico'] ?>'">Eliminar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function editarDoctor(id, nombre, apellido, especialidad, no_ident, direccion, telefono, correo, rh, fecha_nac) {
    const form = document.getElementById('doctor-form');
    form.id.value = id;
    form.nombre.value = nombre;
    form.apellido.value = apellido;
    form.especialidad.value = especialidad;
    form.no_ident.value = no_ident;
    form.direccion.value = direccion;
    form.telefono.value = telefono;
    form.correo.value = correo;
    form.rh.value = rh;
    form.fecha_nac.value = fecha_nac;
    form.action.value = 'edit';
    document.getElementById('submit-btn').textContent = 'Actualizar';
    document.getElementById('form-title').textContent = 'Editar Doctor';
}
</script>
