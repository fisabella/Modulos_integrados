<?php
class Database {
    private $db;

    public function __construct($file = "clinica.db") {
        try {
            $this->db = new PDO("sqlite:" . $file);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->crearTablas();
        } catch (PDOException $e) {
            die("Error al conectar a la base de datos: " . $e->getMessage());
        }
    }

    private function crearTablas() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS paciente (
                IDPaciente INTEGER PRIMARY KEY AUTOINCREMENT,
                No_Identificacion TEXT,
                Nombres TEXT,
                Apellidos TEXT,
                Direccion TEXT,
                Telefono TEXT,
                Correo TEXT,
                RH TEXT,
                Fecha_Nac DATE
            )
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS medico (
                IDMedico INTEGER PRIMARY KEY AUTOINCREMENT,
                NombreMed TEXT,
                ApellidoMed TEXT,
                Especialidad TEXT,
                No_IdentMed TEXT,
                DireccionMed TEXT,
                TelefonoMed TEXT,
                CorreoMed TEXT
            )
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS consultorio (
                IDConsultorio INTEGER PRIMARY KEY AUTOINCREMENT,
                Descripcion TEXT,
                Especialidad TEXT
            )
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS cita (
                IDCita INTEGER PRIMARY KEY AUTOINCREMENT,
                Fecha_cita TEXT,
                Hora_Cita TEXT,
                MotivoConsulta TEXT,
                ID_paciente INTEGER,
                ID_medico INTEGER,
                ID_consultorio INTEGER,
                FOREIGN KEY(ID_paciente) REFERENCES paciente(IDPaciente),
                FOREIGN KEY(ID_medico) REFERENCES medico(IDMedico),
                FOREIGN KEY(ID_consultorio) REFERENCES consultorio(IDConsultorio)
            )
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS tratamiento (
                CodTratamiento INTEGER PRIMARY KEY AUTOINCREMENT,
                Medicamentos TEXT,
                Dosis TEXT,
                Explicacion TEXT,
                Observaciones TEXT
            )
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS historial_clinico (
                CodHistoClinic INTEGER PRIMARY KEY AUTOINCREMENT,
                FechaInicio TEXT,
                IDpaciente INTEGER,
                IDmedico INTEGER,
                Antecedentes TEXT,
                Diagnostico TEXT,
                Observaciones TEXT,
                CITA_IDCita INTEGER,
                TRATAMIENTO_CodTratamiento INTEGER,
                FOREIGN KEY(IDpaciente) REFERENCES paciente(IDPaciente),
                FOREIGN KEY(IDmedico) REFERENCES medico(IDMedico),
                FOREIGN KEY(CITA_IDCita) REFERENCES cita(IDCita),
                FOREIGN KEY(TRATAMIENTO_CodTratamiento) REFERENCES tratamiento(CodTratamiento)
            )
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario VARCHAR(50) UNIQUE,
                password VARCHAR(255)
            )
        ");
    }

    public function getConnection() {
        return $this->db;
    }
}
?>
