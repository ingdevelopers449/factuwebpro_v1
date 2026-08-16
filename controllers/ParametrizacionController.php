<?php
session_start();
require_once __DIR__ . '/../models/empresa.php';
require_once __DIR__ . '/../models/ResolucionDian.php';

class ParametrizacionController
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
        // Obtener los datos de la empresa actual (para mostrar el nombre)
        $empresaModel = new Empresa();
        $empresas = $empresaModel->obtenerTodos();
        $empresaRegistrada = count($empresas) > 0;
        $nombreEmpresa = $empresaRegistrada ? $empresas[0]['razon_social'] : 'Sin empresa registrada';
        $id_empresa = $empresaRegistrada ? $empresas[0]['id_empresa'] : null;

        // Obtener los datos de parametrización si ya existen
        $resolucionModel = new ResolucionDian();
        $resoluciones = $resolucionModel->obtenerTodos();
        $configRegistrada = count($resoluciones) > 0;

        $configDatos = $configRegistrada ? $resoluciones[0] : [
            'numero_resolucion' => '',
            'fecha_vigencia' => '',
            'prefijo' => '',
            'rango_inicial' => '',
            'rango_final' => '',
            'contador_actual' => '',
            'estado' => 'Activa'
        ];

        // Retornar las variables a la vista
        return [
            'empresaRegistrada' => $empresaRegistrada,
            'nombreEmpresa' => $nombreEmpresa,
            'id_empresa' => $id_empresa,
            'configRegistrada' => $configRegistrada,
            'configDatos' => $configDatos
        ];
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ParametrizacionController.php?action=ver');
            exit;
        }

        // Recuperar ID de la empresa (único registro por ahora)
        $empresaModel = new Empresa();
        $empresas = $empresaModel->obtenerTodos();
        if (count($empresas) == 0) {
            $this->setAlert('error', 'Error', 'Debe configurar una empresa primero.');
            header('Location: ../views/admin/parametrizacion.php');
            exit;
        }
        $id_empresa = $empresas[0]['id_empresa'];

        $numero_resolucion = $_POST['numero_resolucion'] ?? '';
        $fecha_vigencia = $_POST['fecha_vigencia'] ?? '';
        $prefijo = $_POST['prefijo'] ?? '';
        $rango_inicial = $_POST['rango_inicial'] ?? '';
        $rango_final = $_POST['rango_final'] ?? '';
        $contador_actual = $_POST['contador_actual'] ?? '';
        $estado = $_POST['estado'] ?? 'Activa';

        if (empty($numero_resolucion) || empty($fecha_vigencia) || empty($prefijo) || empty($rango_inicial) || empty($rango_final)) {
            $this->setAlert('error', 'Campos Obligatorios', 'Por favor complete todos los campos requeridos.');
            header('Location: ../views/admin/parametrizacion.php');
            exit;
        }

        $resolucionModel = new ResolucionDian();
        $resoluciones = $resolucionModel->obtenerTodos();

        if (count($resoluciones) > 0) {
            // Actualizar
            $resolucionModel->actualizar($id_empresa, $numero_resolucion, $fecha_vigencia, $prefijo, $rango_inicial, $rango_final, $contador_actual, $estado);
            $this->setAlert('success', '¡Actualizado!', 'La parametrización se ha actualizado correctamente.');
        } else {
            // Insertar
            $resolucionModel->insertar($id_empresa, $numero_resolucion, $fecha_vigencia, $prefijo, $rango_inicial, $rango_final, $contador_actual, $estado);
            $this->setAlert('success', '¡Guardado!', 'La parametrización se ha configurado correctamente.');
        }

        header('Location: ../views/admin/parametrizacion.php');
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
$controller = new ParametrizacionController();
$controller->run();
