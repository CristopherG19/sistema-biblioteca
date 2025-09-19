USE biblioteca_db;

DELIMITER //

DROP PROCEDURE IF EXISTS sp_libro_obtener_por_isbn;

CREATE PROCEDURE sp_libro_obtener_por_isbn(IN p_isbn VARCHAR(20))
BEGIN
    SELECT l.*, c.nombre as categoria 
    FROM Libros l 
    JOIN Categorias c ON l.idCategoria = c.idCategoria 
    WHERE l.isbn = p_isbn;
END//

DELIMITER ;