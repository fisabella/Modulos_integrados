<?php
require 'database.php';
$db = new Database();
$conn = $db->getConnection();




// Manejo de formulario: agregar o editar cita
if (isset($_POST['action'])) {
    $id_paciente = $_POST['paciente'];
    $id_medico = $_POST['doctor'];
    $id_consultorio = $_POST['consultorio'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $motivo = $_POST['motivo'];

    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("
            INSERT INTO cita (Fecha_cita, Hora_Cita, MotivoConsulta, ID_paciente, ID_medico, ID_consultorio)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$fecha, $hora, $motivo, $id_paciente, $id_medico, $id_consultorio]);
    } elseif ($_POST['action'] === 'edit') {
        $stmt = $conn->prepare("
            UPDATE cita SET Fecha_cita=?, Hora_Cita=?, MotivoConsulta=?, ID_paciente=?, ID_medico=?, ID_consultorio=?
            WHERE IDCita=?
        ");
        $stmt->execute([$fecha, $hora, $motivo, $id_paciente, $id_medico, $id_consultorio, $_POST['id']]);
    }
}

// Eliminar cita
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM cita WHERE IDCita=?");
    $stmt->execute([$_GET['delete']]);
}

// Obtener listas de pacientes, médicos y consultorios
$pacientes = $conn->query("SELECT IDPaciente, Nombres, Apellidos FROM paciente")->fetchAll(PDO::FETCH_ASSOC);
$medicos = $conn->query("SELECT IDMedico, NombreMed, ApellidoMed FROM medico")->fetchAll(PDO::FETCH_ASSOC);
$consultorios = $conn->query("SELECT IDConsultorio, Descripcion FROM consultorio")->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las citas con JOIN
$citas = $conn->query("
    SELECT c.IDCita, p.Nombres || ' ' || p.Apellidos as paciente,
           m.NombreMed || ' ' || m.ApellidoMed as doctor,
           co.Descripcion as consultorio,
           c.Fecha_cita, c.Hora_Cita, c.MotivoConsulta
    FROM cita c
    JOIN paciente p ON c.ID_paciente = p.IDPaciente
    JOIN medico m ON c.ID_medico = m.IDMedico
    JOIN consultorio co ON c.ID_consultorio = co.IDConsultorio
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="loginPHP-container">
    <div class="form-section">
        <h2 id="form-title">Agregar Cita</h2>
        <form method="post" id="cita-form">
            <input type="hidden" name="id" value="">
            <input type="hidden" name="action" value="add">

            <label>Paciente</label>
            <select name="paciente" required>
                <?php foreach($pacientes as $p): ?>
                    <option value="<?= $p['IDPaciente'] ?>"><?= $p['Nombres'] . ' ' . $p['Apellidos'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Médico</label>
            <select name="doctor" required>
                <?php foreach($medicos as $m): ?>
                    <option value="<?= $m['IDMedico'] ?>"><?= $m['NombreMed'] . ' ' . $m['ApellidoMed'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Consultorio</label>
            <select name="consultorio" required>
                <?php foreach($consultorios as $c): ?>
                    <option value="<?= $c['IDConsultorio'] ?>"><?= $c['Descripcion'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Fecha</label>
            <input type="date" name="fecha" required>

            <label>Hora</label>
            <input type="time" name="hora" required>

            <label>Motivo</label>
            <input type="text" name="motivo">

            <button type="submit" id="submit-btn">Agregar</button>
        </form>
    </div>

    <div class="list-section">
        <h2>Lista de Citas</h2>
        <table>
            <tr>
                <th>Paciente</th>
                <th>Doctor</th>
                <th>Consultorio</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Motivo</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($citas as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['paciente']) ?></td>
                <td><?= htmlspecialchars($c['doctor']) ?></td>
                <td><?= htmlspecialchars($c['consultorio']) ?></td>
                <td><?= htmlspecialchars($c['Fecha_cita']) ?></td>
                <td><?= htmlspecialchars($c['Hora_Cita']) ?></td>
                <td><?= htmlspecialchars($c['MotivoConsulta']) ?></td>
                <td>
                    <button onclick="editarCita(
                        <?= $c['IDCita'] ?>,
                        <?= $c['ID_paciente'] ?? 'null' ?>,
                        <?= $c['ID_medico'] ?? 'null' ?>,
                        <?= $c['ID_consultorio'] ?? 'null' ?>,
                        '<?= $c['Fecha_cita'] ?>',
                        '<?= $c['Hora_Cita'] ?>',
                        '<?= addslashes($c['MotivoConsulta']) ?>'
                    )">Editar</button>
                    <button onclick="if(confirm('Eliminar?')) location.href='?module=cita&delete=<?= $c['IDCita'] ?>'">Eliminar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<script>
function editarCita(id, paciente, doctor, consultorio, fecha, hora, motivo) {
    const form = document.getElementById('cita-form');
    form.id.value = id;
    form.paciente.value = paciente;
    form.doctor.value = doctor;
    form.consultorio.value = consultorio;
    form.fecha.value = fecha;
    form.hora.value = hora;
    form.motivo.value = motivo;
    form.action.value = 'edit';
    document.getElementById('submit-btn').textContent = 'Actualizar';
    document.getElementById('form-title').textContent = 'Editar Cita';
}
</script>
