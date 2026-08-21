<?php
session_start();
require_once __DIR__ . '/../models/Empresa.php';

class EmpresaController
{
    private function setAlert(string $icon, string $title, string $text)
    {
        $_SESSION['alert'] = [
            'icon' => $icon,
            'title' => $title,
            'text' => $text
        ];
    }

    public function index()
    {
        // Obtener los datos de la base de datos
        $empresaModel = new Empresa();
        $empresas = $empresaModel->obtenerTodos();

        $empresaRegistrada = count($empresas) > 0; 
        $empresaDatos = $empresaRegistrada ? $empresas[0] : [
            'nit' => '',
            'razon_social' => '',
            'direccion' => '',
            'telefono' => '',
            'correo' => '',
            'logo' => ''
        ];

        // Retornar las variables a la vista
        return [
            'empresaRegistrada' => $empresaRegistrada,
            'empresaDatos' => $empresaDatos
        ];
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/admin/empresa.php');
            exit;
        }

        $nit = $_POST['nit'] ?? '';
        $razon_social = $_POST['razon_social'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';

        if (empty($nit) || empty($razon_social) || empty($direccion)) {
            $this->setAlert('error', 'Campos Incompletos', 'Debe completar los datos legales obligatorios');
            header('Location: ../views/admin/empresa.php');
            exit;
        }

        $empresaModel = new Empresa();
        $empresas = $empresaModel->obtenerTodos();

        // 1. Obtener el logo actual (si existe)
        $logo = '';
        if (count($empresas) > 0) {
            $logo = $empresas[0]['logo'];
        }

        // 2. Procesar la subida del nuevo logo (si el usuario subió uno)
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['logo']['tmp_name'];
            $name = time() . '_' . basename($_FILES['logo']['name']);
            $upload_dir = __DIR__ . '/../public/uploads/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($tmp_name, $upload_dir . $name)) {
                // Eliminar el logo viejo si se reemplaza
                if (!empty($logo) && file_exists($upload_dir . $logo)) {
                    unlink($upload_dir . $logo);
                }
                $logo = $name;
            }
        }

        // 3. Guardar en Base de Datos
        if (count($empresas) > 0) {
            // Ya existe, actualizar
            $empresaModel->actualizar($nit, $razon_social, $direccion, $telefono, $correo, $logo);
            $this->setAlert('success', '¡Actualizado!', 'Los datos de la empresa se han actualizado correctamente.');
        } else {
            // No existe, insertar
            $empresaModel->insertar($nit, $razon_social, $direccion, $telefono, $correo, $logo);
            $this->setAlert('success', '¡Guardado!', 'Los datos de la empresa se han configurado correctamente.');
        }

        header('Location: ../views/admin/empresa.php');
        exit;
    }

    public function run()
    {
        $action = $_GET['action'] ?? '';
        if ($action === 'guardar') {
            $this->guardar();
        }
    }
}

// Ejecutar controlador si hay acción por URL
$controller = new EmpresaController();
$controller->run();