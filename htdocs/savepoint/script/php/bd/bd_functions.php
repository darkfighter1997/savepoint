<?php
	/*
		Este ficheiro é para funções ou variáveis relacionadas com a base de dados.
		Professor eu decidi começar a usar OOP para base de dados. Nunca tinha usado, sempre me habituei
		a procedural mas quis ir por uma rota diferente embora que com o mesmo resultado para não focar-me só num lado. - Pedro
	*/
	$hostname = "localhost";    // Vamos considerar localhost ou máquina local
    $utilizador = "root";    // Username é root
    $password = "";     // Password é root
    $bd = "sp_bd";
 
    //Criar uma nova conexão
    $conn = new mysqli($hostname, $utilizador, $password, $bd);

    //Verificar se a ligação foi feita
    if($conn->connect_error) {
    	die("Erro de conexão: " . $conn->connect_error);
    } 

    /*
    	Esta função serve para inserir dados na base de dados. Criei-a de forma a evitar repetir código. Foi inicialmente desenhada para o projeto web e copiada para usar neste. - Pedro
   		Exemplo:
			$teste = array("user_teste", "password", "0", "profile_test_link", "12345@gmail.com");
			inserir_dados($teste, "user_info");
    */
    function inserir_dados($array = array(), $table, $optional = array()){ // Inicializar a função e as variáveis
			global $conn; //Recuperar a variável global de ligação à base de dados fora da função
			$array_size = count($array); //Contar o tamanho do array para acrescentar os campos necessários.

			$query = "INSERT INTO $table VALUES ("; //Inicio da query

			for ($i=0; $i < $array_size; $i++) { //Inicio do loop para preencher a query
				if($i < ($array_size - 1) ){ //Se não estiver no fim.......
					$query = $query . "'$array[$i]', "; //Preencher com os campos
				} else { //Se tiver no fim.......
					$query = $query . "'$array[$i]');"; //Preencher com o campo final e fechar a query
				}
			} 

			//Verificar se a query funcionou
			if($conn->query($query) === TRUE) { //Se sim......
				if($optional[0] != 1){ //Caso a opção de redirecionar não seja verdadeira
					echo "Registo criado!"; //Apresentar um feedback simples ao utilizador
					$conn->close();
					return true;
				} else { //Senão.......
					header("Location: $optional[1]", true, 301); //Redirecionar o utilizador para a página inicial
					$conn->close(); //Fechar a ligação à base de dados
				}
			} else { //Senão......
				die("Erro: " . $query . "<br>" . $conn->error); //Dá um erro ao utilizador a explicar porque
			}
	}


	/*Esta função serve para ir buscar registos a base de dados. Criei-a para não haver repetições de código. Foi inicialmente desenhada para o projeto web e copiada para usar neste. - Pedro
	Exemplo:
		$colunas = array("uid", "username", "password", "activated", "e_mail");
		
		if($resultado = selecionar_dados("user_info", false, $colunas)){ 
			for ($i = 1; $i <= count($resultado); $i++) { O loop tem de começar em 1, o php por alguma razao em loops nao 												  deteta arrays[0][0]
				for ($k = 0; $k < count($colunas); $k++) { 
					if($k != (count($colunas) - 1) ){
						echo $resultado[$i][$k] . ", ";
					} else {
						echo $resultado[$i][$k] . " <br>";
					}
				}
				
			} 
		} else {
			echo "Erro: Não foram encontrados registos.";
		}
	*/
	function selecionar_dados($table,  $specific, $columns = array(), $optional){ //Inicializar a função e as variáveis
			global $conn; //Recuperar a variável global de ligação à base de dados fora da função
			if($specific == false){ //Se não for buscar campos específicos
				$query = "SELECT * FROM $table;"; //Cria a query para a base de dados
				$resultado = $conn->$query($query); //Executa a query

				if($resultado->num_rows > 0){ //Se encontrar registos
					$i = 1; //Iniciar variável de contagem no 1
					$registos = array(); //Criar array que irá conter os registos

					while($linha = $resultado->fetch_assoc($resultado)){ //Enquanto houver registos por percorrer
						for ($k = 0; $k < count($columns); $k++) { //Passar por todos os registos numa linha
							$registos[$i][$k] = $linha[$columns[$k]]; //Guardar registos individuais de cada coluna de cada linha no array
						}
						$i = $i + 1; //Incrementar variável de contagem
					}

					$conn->close(); //Quando acabar os loops fechar a ligação à base de dados

					return $registos; //Devolver array com os registos
				} else { //Senão......

					$conn->close(); //Fechar ligação à base de dados

					return false; //Devolver falso, executar erro na página que chamou a função
				}	
			} else { //Senão......
				$query = "SELECT "; //Construir parte inicial da query

				for ($i=0; $i < count($columns); $i++) { //Inicio do loop para preencher a query
					if($i < (count($columns) - 1) ){ //Se não estiver no fim.......
						$query = $query . "$columns[$i], "; //Preencher com os campos
					} else { //Se tiver no fim.......
						$query = $query . " $columns[$i] "; //Preencher com o campo final e fechar a query
					}
				} 

				$query = $query . " FROM $table $optional"; //Finalizar construção de query

				if($resultado = $conn->query($query)){ //Se a query for bem sucedida
					if($resultado->num_rows > 0){ //Se houver resultados na query
					$i = 1; //Iniciar variável de contagem no 1
					$registos = array(); //Criar array que irá conter os registos

					while($linha = $resultado->fetch_assoc()){ //Enquanto houver registos por percorrer
						for ($k = 0; $k < count($columns); $k++) { //Passar por todos os registos numa linha
							$registos[$i][$k] = $linha[$columns[$k]]; //Guardar registos individuais de cada coluna de cada linha no array
						}
						$i = $i + 1; //Incrementar variável de contagem
					}

					$conn->close(); //Quando acabar os loops fechar a ligação à base de dados
					return $registos; //Devolver array com os registos

				} else { //Senão......
					$conn->close(); //Fechar ligação à base de dados

					return false;
				}

				} else { //Senão......
					die("Erro: " . $query . "<br>" . $conn->error); //Caso excecional - Apresentar erro diretamente ao utilizador
				}

			}
		}

	/*
		Mais uma para a lista, uma função que facilita o DELETE. Vai ajudar a manter o código limpo e evitar repetições. - Pedro
		Exemplo:
		$valor = "1"; //Valor da linha
		$coluna = "uid"; //Coluna onde procurar o valor
		$tabela = "user_info"; //Tabela a onde deve ir executar a query

		eliminar_dados($valor, $coluna, $tabela);			
	*/
	function eliminar_dados($value, $column, $table, $optional = array()){ // Inicializar a função e as variáveis
		global $conn; //Recuperar a variável global de ligação à base de dados fora da função
		
		$query = "DELETE FROM $table WHERE $column='$value';"; //Constroi a query

		if($conn->query($query) === TRUE){ //Verificar se a query foi bem executada
			if($optional[0] != 1){ //Caso a opção de redirecionar não seja verdadeira
				echo "Registo criado!"; //Apresentar um feedback simples ao utilizador
				$conn->close(); //Fechar ligação à base de dados;
				return true;
			} else { //Senão.......
				header("Location: $optional[1]", true, 301); //Redirecionar o utilizador para a página inicial
				$conn->close(); //Fechar a ligação à base de dados
			}
		} else {
			die("Erro: " . $query . "<br>" . $conn->error); //Caso excecional - Apresentar erro diretamente ao utilizador
		}
	}


	/*
		Esta função serve para organizar o update de dados numa base de dados. - Pedro
		Exemplo:
		$table = "user_info";
		$colunas = array("username", "password");
		$valores = array("user_teste2", "password2");
		$coluna_verificar = "uid";
		$valor = "1";
		editar_dados($table, $colunas, $valores, $coluna_verificar, $valor);
	*/

	function editar_dados($table, $columns = array(), $values = array(), $column_check, $value, $optional = array()){
		global $conn; //Recuperar a variável global de ligação à base de dados fora da função
		$array_size = count($columns); //Contar o tamanho do array para acrescentar os campos necessários.
		$query = "UPDATE $table SET "; //Inicio da query
		for ($i=0; $i < $array_size; $i++) { //Inicio do loop para preencher a query
			if($i < ($array_size - 1) ){ //Se não estiver no fim.......
				$query = $query . "$columns[$i] = '$values[$i]', "; //Preencher com os campos
			} else { //Se tiver no fim.......
				$query = $query . "$columns[$i] = '$values[$i]' WHERE $column_check = '$value';"; //Preencher com o campo final e fechar a query
			}
		}

		if($conn->query($query)){ //Verificar se a query foi bem executada
			if($optional[0] != 1){ //Caso a opção de redirecionar não seja verdadeira
				echo "Registo criado!"; //Apresentar um feedback simples ao utilizador
				$conn->close(); //Fechar ligação à base de dados;
				return true;
			} else { //Senão.......
				header("Location: $optional[1]", true, 301); //Redirecionar o utilizador para a página inicial
				$conn->close(); //Fechar a ligação à base de dados
			}
		} else {
			die("Erro: " . $query . "<br>" . $conn->error); //Caso excecional - Apresentar erro diretamente ao utilizador
		}	
	}
?>