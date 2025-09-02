<?php
// Ruta de la base de datos
$db_file = __DIR__ . '/db/clinica.db';

try {
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tabla PACIENTE
    $db->exec("CREATE TABLE IF NOT EXISTS PACIENTE (
        IDPaciente INTEGER PRIMARY KEY AUTOINCREMENT,
        No_Identificacion VARCHAR(45),
        Nombres VARCHAR(100),
        Apellidos VARCHAR(100),
        Direccion VARCHAR(200),
        Telefono VARCHAR(45),
        Correo VARCHAR(45),
        RH VARCHAR(45),
        Fecha_Nac DATE
    )");

    // Tabla MEDICO
    $db->exec("CREATE TABLE IF NOT EXISTS MEDICO (
        IDMedico INTEGER PRIMARY KEY AUTOINCREMENT,
        NombreMed VARCHAR(100),
        ApellidoMed VARCHAR(100),
        Especialidad VARCHAR(100),
        No_IdentMed VARCHAR(45),
        DireccionMed VARCHAR(200),
        TelefonoMed VARCHAR(45),
        CorreoMed VARCHAR(45)
    )");

    // Tabla CONSULTORIO
    $db->exec("CREATE TABLE IF NOT EXISTS CONSULTORIO (
        IDConsultorio INTEGER PRIMARY KEY AUTOINCREMENT,
        Descripcion VARCHAR(100),
        Especialidad VARCHAR(100)
    )");

    // Tabla TRATAMIENTO
    $db->exec("CREATE TABLE IF NOT EXISTS TRATAMIENTO (
        CodTratamiento INTEGER PRIMARY KEY AUTOINCREMENT,
        Medicamentos VARCHAR(45),
        Dosis VARCHAR(45),
        Explicacion VARCHAR(45),
        Observaciones VARCHAR(45)
    )");

    // Tabla CITA
    $db->exec("CREATE TABLE IF NOT EXISTS CITA (
        IDCita INTEGER PRIMARY KEY AUTOINCREMENT,
        Fecha_Cita VARCHAR(45),
        Hora_Cita VARCHAR(45),
        MotivoConsulta VARCHAR(200),
        ID_Paciente INTEGER,
        ID_Medico INTEGER,
        ID_Consultorio INTEGER,
        FOREIGN KEY(ID_Paciente) REFERENCES PACIENTE(IDPaciente),
        FOREIGN KEY(ID_Medico) REFERENCES MEDICO(IDMedico),
        FOREIGN KEY(ID_Consultorio) REFERENCES CONSULTORIO(IDConsultorio)
    )");

    // Tabla HISTORIAL_CLINICO
    $db->exec("CREATE TABLE IF NOT EXISTS HISTORIAL_CLINICO (
        CodHistoClinic INTEGER PRIMARY KEY AUTOINCREMENT,
        FechaInicio VARCHAR(45),
        IDPaciente INTEGER,
        IDMedico INTEGER,
        Antecedentes VARCHAR(200),
        Diagnostico VARCHAR(200),
        Observaciones VARCHAR(200),
        CITA_IDCita INTEGER,
        TRATAMIENTO_CodTratamiento INTEGER,
        FOREIGN KEY(IDPaciente) REFERENCES PACIENTE(IDPaciente),
        FOREIGN KEY(IDMedico) REFERENCES MEDICO(IDMedico),
        FOREIGN KEY(CITA_IDCita) REFERENCES CITA(IDCita),
        FOREIGN KEY(TRATAMIENTO_CodTratamiento) REFERENCES TRATAMIENTO(CodTratamiento)
    )");

    

} catch (PDOException $e) {
    echo "Error al crear la base de datos: " . $e->getMessage() . "\n";
}
