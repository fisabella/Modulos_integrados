<?php
require 'database.php';
$db = new Database();
$conn = $db->getConnection();

$error = "";
$success = "";

// Crear tabla si no existe
$conn->exec("CREATE TABLE IF NOT EXISTS tratamientos (
    CodTratamiento INTEGER PRIMARY KEY AUTOINCREMENT,
    Medicamentos TEXT NOT NULL,
    Dosis TEXT NOT NULL,
    Explicacion TEXT NOT NULL,
    Observaciones TEXT
)");

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM tratamientos WHERE CodTratamiento=?");
    $stmt->execute([$_GET['edit']]);
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $CodTratamiento = $_POST['CodTratamiento'] ?? null;
    $Medicamentos = $_POST['Medicamentos'];
    $Dosis = $_POST['Dosis'];
    $Explicacion = $_POST['Explicacion'];
    $Observaciones = $_POST['Observaciones'];

    try {
        if ($CodTratamiento) {
            $stmt = $conn->prepare("UPDATE tratamientos SET Medicamentos=?, Dosis=?, Explicacion=?, Observaciones=? WHERE CodTratamiento=?");
            $stmt->execute([$Medicamentos, $Dosis, $Explicacion, $Observaciones, $CodTratamiento]);
            $success = "Tratamiento actualizado correctamente.";
        } else {
            $stmt = $conn->prepare("INSERT INTO tratamientos (Medicamentos, Dosis, Explicacion, Observaciones) VALUES (?,?,?,?)");
            $stmt->execute([$Medicamentos, $Dosis, $Explicacion, $Observaciones]);
            $success = "Tratamiento agregado correctamente.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

if (isset($_GET['delete'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM tratamientos WHERE CodTratamiento=?");
        $stmt->execute([$_GET['delete']]);
        $success = "Tratamiento eliminado correctamente.";
    } catch (PDOException $e) {
        $error = "Error al eliminar: " . $e->getMessage();
    }
}

$tratamientos = $conn->query("SELECT * FROM tratamientos")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="form-section">
    <h2><?= $editData ? "Editar Tratamiento" : "Agregar Tratamiento" ?></h2>
    
    <?php if($error) echo "<div class='message-error'>".htmlspecialchars($error)."</div>"; ?>
    <?php if($success) echo "<div class='message-success'>".htmlspecialchars($success)."</div>"; ?>

    <form method="post">
        <input type="hidden" name="CodTratamiento" value="<?= htmlspecialchars($editData['CodTratamiento'] ?? '') ?>">
        <input type="text" name="Medicamentos" placeholder="Medicamentos" required value="<?= htmlspecialchars($editData['Medicamentos'] ?? '') ?>">
        <input type="text" name="Dosis" placeholder="Dosis" required value="<?= htmlspecialchars($editData['Dosis'] ?? '') ?>">
        <input type="text" name="Explicacion" placeholder="Explicación" required value="<?= htmlspecialchars($editData['Explicacion'] ?? '') ?>">
        <input type="text" name="Observaciones" placeholder="Observaciones" value="<?= htmlspecialchars($editData['Observaciones'] ?? '') ?>">
        <button type="submit"><?= $editData ? "Actualizar" : "Guardar" ?></button>
        <?php if($editData): ?>
            <a class="btn-cancel" href="?module=tratamientos">Cancelar</a>
        <?php endif; ?>
    </form>
</div>

<div class="list-section">
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Medicamentos</th>
                <th>Dosis</th>
                <th>Explicación</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tratamientos as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['CodTratamiento']) ?></td>
                    <td><?= htmlspecialchars($t['Medicamentos']) ?></td>
                    <td><?= htmlspecialchars($t['Dosis']) ?></td>
                    <td><?= htmlspecialchars($t['Explicacion']) ?></td>
                    <td><?= htmlspecialchars($t['Observaciones']) ?></td>
                    <td class="action-buttons">
                        <a href="?module=tratamientos&edit=<?= $t['CodTratamiento'] ?>" class="edit-btn">Editar</a>
                        <a href="?module=tratamientos&delete=<?= $t['CodTratamiento'] ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de eliminar este tratamiento?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
