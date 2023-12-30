<?php
	namespace DB;
	class DBAccess {
		private const HOST_DB = "localhost";
		private const DATABASE_NAME = "melodia_db";
		private const USERNAME = "root";
		private const PASSWORD = "root";
		
		private $connection;
		
		public function openDBConnection(){
			$this -> connection = mysqli_connect(
				self::HOST_DB,
				self::USERNAME,
				self::PASSWORD,
				self::DATABASE_NAME,
			);
			return mysqli_connect_errno() == 0;
		}
				
        /** Funzione che ritorna l'utente dato lo username e la password. */
		public function getUser($username, $password){
			$query = "SELECT username FROM accounts
			WHERE username = \"$username\" AND password = \"$password\"";
			$queryResult = mysqli_query($this->connection, $query) or die("Errore in DBAccess" .mysqli_error($this -> connection));
			if (mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)){
					$result[] = $row;
				}
				$queryResult->free();
				return $result;
			}else{
				return null;
			}
		}
		
        /** Funzione che ritorna tutte le richieste */
		public function getListaRichieste(){
			$query = "SELECT m.message_id, m.name, m.email, m.message, m.state_id, m.creation_date, ms.name as nome_stato
			FROM messages AS m INNER JOIN message_states AS ms ON (m.state_id = ms.state_id)
			ORDER BY ms.state_id ASC, m.creation_date ASC";
			$queryResult = mysqli_query($this -> connection, $query) or die("Errore in DBAccess" .mysqli_error($this -> connection));
			if (mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)){
					$result[] = $row;
				}
				$queryResult->free();
				return $result;
			}else{
				return null;
			}
		}
		
        /** Funzione che ritorna la richiesta con id uguale a message_id */
		public function getRichiesta($message_id){
			$query = "SELECT m.message_id, m.name, m.email, m.message, m.state_id, m.creation_date
			FROM messages AS m
			WHERE m.message_id = $message_id";
			$queryResult = mysqli_query($this -> connection, $query) or die("Errore in DBAccess" .mysqli_error($this -> connection));
			if (mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)){
					$result[] = $row;
				}
				$queryResult->free();
				return $result;
			}else{
				return null;
			}
		}
		
        /** Funzione che revade la richiesta con message_id = $message_id */
		public function evadiRichiesta($message_id){
			$query = "UPDATE messages
			SET state_id = 2
			WHERE message_id = $message_id";
			mysqli_query ($this->connection, $query) or die(mysqli_error($this->connection));
			return mysqli_affected_rows ($this->connection) > 0;
		}
		
        /** Funzione che ritorna la lista delle categorie. */
		public function getCategories(){
			$query = "SELECT category_id, name FROM categories";
			$queryResult = mysqli_query($this->connection, $query) or die("Errore in DBAccess" .mysqli_error($this -> connection));
			if (mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)){
					$result[] = $row;
				}
				$queryResult->free();
				return $result;
			} else {
				return null;
			}
		}
		
        /** Funzione che ritorna tutti gli prodotti */
		public function getListaArticoli(){
			$query = "SELECT p.product_id, p.name, p.image_url, p.category_id, p.price, p.discounted_price, c.name as nome_cat
			FROM products AS p INNER JOIN categories AS c ON (p.category_id = c.category_id)
			ORDER BY p.name";
			$queryResult = mysqli_query($this -> connection, $query) or die("Errore in DBAccess" .mysqli_error($this -> connection));
			if (mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)){
					$result[] = $row;
				}
				$queryResult->free();
				return $result;
			}else{
				return null;
			}
		}
		
        /** Funzione che ritorna il singolo prodotto dato l'identificativo. */
		public function getProduct($product_id){
			if ($product_id == null) {
				return null;
			}
			$query = "SELECT p.product_id,p.name,p.description,p.image_url,p.category_id,
				p.price,p.discounted_price,p.brand,p.color,p.material,c.name as nome_cat
			FROM products AS p INNER JOIN categories AS c ON (p.category_id = c.category_id)
			WHERE product_id = $product_id";
			$queryResult = mysqli_query($this -> connection, $query) or die("Errore in DBAccess" .mysqli_error($this -> connection));
			if (mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)){
					$result[] = $row;
				}
				$queryResult->free();
				return $result;
			}else{
				return null;
			}
		}
		
		/** Funzione che ritorna l'identificativo massimo dei prodotti inseriti. */
		public function getMaxProductId(){
			$query = "SELECT MAX(product_id) as max FROM products";
			$queryResult = mysqli_query($this -> connection, $query) or die("Errore in DBAccess" .mysqli_error($this -> connection));
			if (mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)){
					$result[] = $row;
				}
				$queryResult->free();
				return $result;
			}else{
				return null;
			}
		}
		
        /** Funzione che inserisce un nuovo prodotto. */
		public function insertNewProduct($nomeArticolo, $descrizioneArticolo, $prezzoArticolo, $marchioArticolo, $coloreArticolo, $materialeArticolo, $idCategoria, $prezzoScontatoArticolo, $imageUrl){
			$queryInsert = "
				INSERT INTO products(name, description, category_id, price, {discounted_price} brand, color, material, image_url)
				VALUES (\"$nomeArticolo\",\"$descrizioneArticolo\",$idCategoria,$prezzoArticolo,{discounted_price_value}\"$marchioArticolo\",\"$coloreArticolo\",\"$materialeArticolo\",\"$imageUrl\")
			";
			if ($prezzoScontatoArticolo != null) {
				$queryInsert = str_replace ("{discounted_price}", "discounted_price,", $queryInsert);
				$queryInsert = str_replace ("{discounted_price_value}", $prezzoScontatoArticolo . ",", $queryInsert);
			} else {
				$queryInsert = str_replace ("{discounted_price}", "", $queryInsert);
				$queryInsert = str_replace ("{discounted_price_value}", "", $queryInsert);
			}
			mysqli_query ($this->connection, $queryInsert) or die(mysqli_error($this->connection));
			return mysqli_affected_rows ($this->connection) > 0;
		}
		
        /** Funzione che aggiorna un prodotto. */
		public function updateProduct($product_id, $nomeArticolo, $descrizioneArticolo, $prezzoArticolo, $marchioArticolo, $coloreArticolo, $materialeArticolo, $idCategoria, $prezzoScontatoArticolo, $imageUrl){
			$queryUpdate = "
				UPDATE products
				SET name = \"$nomeArticolo\",
				description = \"$descrizioneArticolo\",
				category_id = $idCategoria,
				price = $prezzoArticolo,
				{discounted_price}
				brand = \"$marchioArticolo\",
				color = \"$coloreArticolo\",
				material = \"$materialeArticolo\",
				image_url = \"$imageUrl\"
				WHERE product_id = $product_id
			";
			if ($prezzoScontatoArticolo != null) {
				$queryUpdate = str_replace ("{discounted_price}", "discounted_price = $prezzoScontatoArticolo,", $queryUpdate);
			} else {
				$queryUpdate = str_replace ("{discounted_price}", "discounted_price = null,", $queryUpdate);
			}
			mysqli_query ($this->connection, $queryUpdate) or die(mysqli_error($this->connection));
			return mysqli_affected_rows ($this->connection) > 0;
		}
		
        /** Funzione che elimina un prodotto. */
		public function deleteProduct($product_id){
			$queryDelete = "DELETE FROM products WHERE product_id = $product_id";
			mysqli_query ($this->connection, $queryDelete) or die(mysqli_error($this->connection));
			return mysqli_affected_rows ($this->connection) > 0;
		}

		public function closeConnection(){
			mysqli_close($this->connection);
		}
	}
?>