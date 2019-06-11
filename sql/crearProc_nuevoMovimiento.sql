USE parrot;
DROP PROCEDURE IF EXISTS prc_nuevoMovimiento;
DELIMITER $$
USE parrot$$
CREATE PROCEDURE prc_nuevoMovimiento(
	IN p_id_comprobantes int, 
    IN p_n_comprobante int,
	IN p_factura varchar(8), 
    IN p_fecha_factura date,
    IN p_beneficiario varchar(45),
    IN p_cancela varchar(20),
    IN p_fecha_cancela date)
BEGIN
	IF NOT EXISTS
		(SELECT 1 FROM comprobantes WHERE factura=p_factura AND fecha_factura = p_fecha_factura AND beneficiario = p_beneficiario)
    THEN 
		INSERT INTO comprobantes (id_comprobantes, n_comprobante, factura, fecha_factura, beneficiario, cancela, fecha_cancela) VALUES (p_id_comprobantes, p_n_comprobante, p_factura, p_fecha_factura, p_beneficiario, p_cancela, p_fecha_cancela);
		SELECT 0 as errno;
	ELSE
		SELECT 1 AS errno;
    END IF;
END $$
DELIMITER ;    