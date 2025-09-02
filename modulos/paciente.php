<?php
require 'database.php';
$db = new Database();
$conn = $db->getConnection();

// Crear tabla pacientes si no existe
$conn->exec("CREATE TABLE IF NOT EXISTS pacientes (
    IDPaciente INTEGER PRIMARY KEY AUTOINCREMENT,
    No_Identificacion VARCHAR(20),
    Nombres VARCHAR(100),
    Apellidos VARCHAR(100),
    Direccion VARCHAR(255),
    Telefono VARCHAR(20),
    Correo VARCHAR(45),
    RH VARCHAR(5),
    Fecha_Nac DATE
)");

// Agregar paciente
if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO pacientes (No_Identificacion, Nombres, Apellidos, Direccion, Telefono, Correo, RH, Fecha_Nac) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['No_Identificacion'], 
        $_POST['Nombres'], 
        $_POST['Apellidos'], 
        $_POST['Direccion'], 
        $_POST['Telefono'], 
        $_POST['Correo'], 
        $_POST['RH'], 
        $_POST['Fecha_Nac']
    ]);
}

// Editar paciente
if (isset($_POST['edit'])) {
    $stmt = $conn->prepare("UPDATE pacientes SET No_Identificacion=?, Nombres=?, Apellidos=?, Direccion=?, Telefono=?, Correo=?, RH=?, Fecha_Nac=? WHERE IDPaciente=?");
    $stmt->execute([
        $_POST['No_Identificacion'], 
        $_POST['Nombres'], 
        $_POST['Apellidos'], 
        $_POST['Direccion'], 
        $_POST['Telefono'], 
        $_POST['Correo'], 
        $_POST['RH'], 
        $_POST['Fecha_Nac'],
        $_POST['IDPaciente']
    ]);
}

// Eliminar paciente
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM pacientes WHERE IDPaciente=?");
    $stmt->execute([$_GET['delete']]);
}

// Obtener todos los pacientes
$pacientes = $conn->query("SELECT * FROM pacientes")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="loginPHP-container">
    <div class="form-section">
        <h2 id="form-title">Agregar Paciente</h2>
        <form method="post" id="paciente-form">
            <input type="hidden" name="IDPaciente" value="">
            <input type="text" name="No_Identificacion" placeholder="No. Identificación" required>
            <input type="text" name="Nombres" placeholder="Nombres" required>
            <input type="text" name="Apellidos" placeholder="Apellidos" required>
            <input type="text" name="Direccion" placeholder="Dirección">
            <input type="text" name="Telefono" placeholder="Teléfono">
            <input type="email" name="Correo" placeholder="Correo">
            <input type="text" name="RH" placeholder="RH">
            <input type="date" name="Fecha_Nac" placeholder="Fecha de Nacimiento">
            <button type="submit" name="add">Agregar</button>
        </form>
    </div>

    <div class="list-section">
        <h2>Lista de Pacientes</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>No. Identificación</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>RH</th>
                <th>Fecha Nac</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($pacientes as $p): ?>
            <tr>
                <td><?= $p['IDPaciente'] ?></td>
                <td><?= htmlspecialchars($p['No_Identificacion']) ?></td>
                <td><?= htmlspecialchars($p['Nombres']) ?></td>
                <td><?= htmlspecialchars($p['Apellidos']) ?></td>
                <td><?= htmlspecialchars($p['Direccion']) ?></td>
                <td><?= htmlspecialchars($p['Telefono']) ?></td>
                <td><?= htmlspecialchars($p['Correo']) ?></td>
                <td><?= htmlspecialchars($p['RH']) ?></td>
                <td><?= $p['Fecha_Nac'] ?></td>
                <td>
                    <button onclick="editarPaciente(
                        <?= $p['IDPaciente'] ?>, 
                        '<?= htmlspecialchars($p['No_Identificacion'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($p['Nombres'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($p['Apellidos'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($p['Direccion'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($p['Telefono'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($p['Correo'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($p['RH'], ENT_QUOTES) ?>',
                        '<?= $p['Fecha_Nac'] ?>'
                    )">Editar</button>
                    <button onclick="if(confirm('Eliminar?')) location.href='?delete=<?= $p['IDPaciente'] ?>'">Eliminar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<script>
function editarPaciente(IDPaciente, No_Identificacion, Nombres, Apellidos, Direccion, Telefono, Correo, RH, Fecha_Nac) {
    const form = document.getElementById('paciente-form');
    form.IDPaciente.value = IDPaciente;
    form.No_Identificacion.value = No_Identificacion;
    form.Nombres.value = Nombres;
    form.Apellidos.value = Apellidos;
    form.Direccion.value = Direccion;
    form.Telefono.value = Telefono;
    form.Correo.value = Correo;
    form.RH.value = RH;
    form.Fecha_Nac.value = Fecha_Nac;
    
    form.querySelector('button[name="add"]').name = 'edit';
    form.querySelector('button[name="edit"]').textContent = 'Actualizar';
    document.getElementById('form-title').textContent = 'Editar Paciente';
}
</script>
