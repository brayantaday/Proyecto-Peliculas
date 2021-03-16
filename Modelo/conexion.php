<?php
Class Conexion
{	private $usuario="root";
	private $pass="";
	private $dbcon=null;
	//private $dns="pgsql:host=localhost:5432;dbname=dbproyecto";
	private $dns="mysql:host=localhost:3306;dbname=db_proyecto2";
	private $error=null;

	private function conectar()
	{
		try {
			$this->dbconn = new PDO($this->dns, $this->usuario, $this->pass);
			$this->dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return true;
			} catch (PDOException $e) {
			$this->error= $e->getMessage();
			return false;
		}
		}

		public function consultarUsuario($tabla, $email){	
			try {
				if(!$this->conectar()){
					return "No conecta".$this->error;
					exit;
				}
				$query="Select * from $tabla where email='$email'";
				$result_set = $this->dbconn->prepare($query);
				$result_set->execute();
				$result = $result_set->fetchAll();
				return $result;
			}catch (Exception $e) {
				return $e->getMessage();
			}
		}

	public function consultar($tabla)
	{	try {
		if(!$this->conectar())
		{	return "No conecta".$this->error;
			exit;
		}
		$query="Select * from $tabla";
		$result_set = $this->dbconn->prepare($query);
		$result_set->execute();
		$result = $result_set->fetchAll();
		return $result;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	public function insertar($tabla,$datos)
	{
		try {
			$this->conectar();
			$sql = "INSERT INTO $tabla(";
			foreach($datos as $clave=>$valor)
			{
				$sql .=$clave.",";
			}
			$sql = substr ($sql, 0, strlen($sql) - 1).") VALUES(";
			foreach($datos as $clave=>$valor)
			{
				$sql .=":".$clave.",";
			}
			$sql = substr ($sql, 0, strlen($sql) - 1).")";
			$stmt = $this->dbconn->prepare($sql);
			foreach($datos as $clave=>$valor)
			{$clave=':'.$clave;
			 $stmt->bindValue($clave, $valor);
			}
			// execute the insert statement
			$stmt->execute();
			return "Correcto";
		} catch (Exception $e) {
			$this->error= $e->getMessage();
			return "Error al insertar... ".$this->error;
		}
	}

	public function consultarFiltro($tabla,$filtro)
	{	try {
		if(!$this->conectar())
		{	return "No conecta".$this->error;
			exit;
		}
		$query="Select * from $tabla where ";
		foreach($filtro as $clave=>$valor)
		{
			$query .="$clave = :$clave and ";
		}
		$query = substr ($query, 0, strlen($query) - 4);
		$result_set = $this->dbconn->prepare($query);
		foreach($filtro as $clave=>$valor)
		{$clave=':'.$clave;
			$result_set->bindValue($clave, $valor);
		}
		$result_set->execute();
		$result = $result_set->fetchAll();
		return $result;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function actualizar($tabla,$datos,$filtro)
	{
		try {
			$this->conectar();
			$sql = "Update $tabla set ";
			foreach($datos as $clave=>$valor)
			{
				$sql .="$clave= :$clave,";
			}
			$sql = substr ($sql, 0, strlen($sql) - 1)." where ";
			foreach($filtro as $clave=>$valor)
			{
				$sql .="$clave = :$clave and ";
			}
			$sql = substr ($sql, 0, strlen($sql) - 4);
			$stmt = $this->dbconn->prepare($sql);
			foreach($datos as $clave=>$valor)
			{$clave=':'.$clave;
			 $stmt->bindValue($clave, $valor);
			}
			foreach($filtro as $clave=>$valor)
			{$clave=':'.$clave;
			 $stmt->bindValue($clave, $valor);
			}
			// execute the insert statement
			$stmt->execute();
			return "Correcto";
		} catch (Exception $e) {
			$this->error= $e->getMessage();
			return "Error al actualizar... ".$this->error;
		}
	}
	public function eliminar($tabla,$filtro)
	{
		try {
		if(!$this->conectar())
		{	return "No conecta".$this->error;
			exit;
		}
		$query="delete from $tabla where Id_carrito=$filtro";
		$stmt = $this->dbconn->prepare($query);
		$stmt->execute();
		return "pelicula eliminada";
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	public function consultarInner($tabla,$filtro)
	{
		try {
		if(!$this->conectar())
		{	return "No conecta".$this->error;
			exit;
		}
		$query="select titulo,precio,Id_carrito from persona as p inner join $tabla as c on p.Id=c.id_persona inner join pelicula as pe on c.id_pelicula=pe.Id where p.Id=$filtro";
		$result_set = $this->dbconn->prepare($query);
		$result_set->execute();
		$result = $result_set->fetchAll();
		return $result;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
}
?>