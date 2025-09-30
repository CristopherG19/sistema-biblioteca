<?php
require_once '../app/models/Categoria.php';

class CategoriasController {
    public function index() {
        $categorias = Categoria::getAll();
        include __DIR__ . '/../views/categorias/index.php';
    }

    public function agregar() {
        include __DIR__ . '/../views/categorias/agregar.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (Categoria::insertar($_POST)) {
                    header('Location: index.php?page=categorias&mensaje=' . urlencode('Categoría agregada exitosamente'));
                } else {
                    header('Location: index.php?page=categorias&error=' . urlencode('Error al agregar la categoría'));
                }
            } catch (Exception $e) {
                header('Location: index.php?page=categorias&error=' . urlencode('Error al agregar la categoría: ' . $e->getMessage()));
            }
        } else {
            header('Location: index.php?page=categorias');
        }
        exit;
    }

    public function editar() {
        $id = $_GET['id'];
        $categoria = Categoria::getById($id);
        include __DIR__ . '/../views/categorias/editar.php';
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                if (Categoria::actualizar($id, $_POST)) {
                    header('Location: index.php?page=categorias&mensaje=' . urlencode('Categoría actualizada exitosamente'));
                } else {
                    header('Location: index.php?page=categorias&error=' . urlencode('Error al actualizar la categoría'));
                }
            } catch (Exception $e) {
                header('Location: index.php?page=categorias&error=' . urlencode('Error al actualizar la categoría: ' . $e->getMessage()));
            }
        } else {
            header('Location: index.php?page=categorias');
        }
        exit;
    }

    public function eliminar() {
        try {
            $id = $_GET['id'];
            if (Categoria::eliminar($id)) {
                header('Location: index.php?page=categorias&mensaje=' . urlencode('Categoría eliminada exitosamente'));
            } else {
                header('Location: index.php?page=categorias&error=' . urlencode('Error al eliminar la categoría'));
            }
        } catch (Exception $e) {
            header('Location: index.php?page=categorias&error=' . urlencode('Error al eliminar la categoría: ' . $e->getMessage()));
        }
        exit;
    }
}
