CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_nuevoUsuario`(
	in _id int, 
    in _username varchar(20),
	in _email varchar(120), 
    in _password varchar(20))
begin
	if not exists(select 1 from user where username = _username) then
		if not exists(select 1 from user where email = _email) then
			insert into user (id, username, email, password) values (_id, _username, _email, MD5(_password));
			select 0 as errno;
		else
			select 2 as errno;
		end if;	
	else
		select 1 as errno;
	end if;
end