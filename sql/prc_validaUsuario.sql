CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_validaUsuario`(
	in _usuario varchar(20),
    in _clave varchar(32)
)
BEGIN
	if not exists(select 1 from user where username = _usuario and password = md5(_clave)) then
		select 1 as numError;
     else
		select 0 as numError;
     end if;       
END