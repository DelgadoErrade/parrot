/* Consulta para una determinada fecha*/
SELECT '2015-06-04' AS fecha, '00000' AS referencia, 'SALDO ANTERIOR' AS descripcion, SUM(monto) AS monto FROM tbl_mov_bancarios
WHERE fecha < '2015-06-04'
UNION
SELECT fecha, referencia, descripcion, monto FROM tbl_mov_bancarios
WHERE fecha = '2015-06-04' 