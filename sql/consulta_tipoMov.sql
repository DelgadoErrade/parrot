SELECT tbl_bancos.idbanco, 
	banco,
    tbl_mov_bancarios.id_tipo_movimiento, 
    tipo_movimiento,
	date_format(fecha, "%d-%m-%Y") AS _Fecha, 
	referencia, 
	descripcion, 
	monto 
FROM tbl_mov_bancarios
INNER JOIN tbl_bancos ON  tbl_bancos.idbanco =  tbl_mov_bancarios.idbanco
INNER JOIN tbl_tipo_movimientos ON tbl_mov_bancarios.id_tipo_movimiento = tbl_tipo_movimientos.id_tipo_movimiento
WHERE tbl_mov_bancarios.id_tipo_movimiento IN (1,2,6) AND fecha BETWEEN "2015/08/01" AND "2015/08/31" AND tbl_mov_bancarios.idbanco = 1
ORDER BY tbl_mov_bancarios.id_tipo_movimiento, fecha