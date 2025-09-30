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
            Categoria::insertar($_POST);
        }
        header('Location: index.php?page=categorias');
        exit;
    }

    public function editar() {
        $id = $_GET['id'];
        $categoria = Categoria::getById($id);
        include __DIR__ . '/../views/categorias/editar.php';
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            Categoria::actualizar($id, $_POST);
        }
        header('Location: index.php?page=categorias');
        exit;
    }

    public function eliminar() {
        $id = $_GET['id'];
        Categoria::eliminar($id);
        header('Location: index.php?page=categorias');
        exit;
    }
}
