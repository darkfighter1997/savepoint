<?php
	/*
	  Funções de Registo dos utilizadores
	*/
	  //Incluir as funções da BD
	  require $_SERVER['DOCUMENT_ROOT'] . '/script/php/bd/bd_functions.php'; // Obrigatório existir, o código não corre sem este ficheiro
	  require $_SERVER['DOCUMENT_ROOT'] . '/script/php/security/security_functions.php'; // Obrigatório existir, o código não corre sem este ficheiro

	  /*
		Função de Registo de Utilizador - Criada mas falta teste com formulário - 17/11/17
		Esta função é óbvia demais para explicar o que faz, basta os comentários em cada linha. Por isso, eis uma piada antes do exemplo.
		O que diz um vulcão a uma vulcoa? "I lava you.". Lindo né?
		Exemplo:
			Igual ao exemplo na linha número 32 do ficheiro bd_functions.php

		Nota Importante: Caso tenham reparado, esta função não leva die nem verificação de falha. Isso é porque a função em si é para otimizar espaço e manter tudo organizado e assim é só chama-la noutra página. A função que verifica se foi corretamente efetuado o registo na base de dados é a insert_data() no ficheiro bd_functions.php que termina o script se der erro e não passa dali. 
	  */
	 function registar_utilizador($dados = array()){ //inicializar a função e as variáveis
	 	global $conn; //Recuperar a variável global de ligação à base de dados fora da função que se encontra no ficheiro bd_functions.php

 		$dados = array(
 				"", 
 				validar_dados($_POST['utilizador']), 
 				validar_dados($_POST['password']), 
 				"0", 
 				validar_dados($_POST['e_mail'])
 				);

 		$registo_opcional = array(true, "http://localhost/savepoint/index.html");
 		inserir_dados($dados, "user_info", $registo_opcional);


	 }

	 function logar($username, $password){
	 	global $conn; //Recuperar a variável global de ligação à base de dados fora da função que se encontra no ficheiro bd_functions.php



	 		$colunas = array("username", "password");

	 		$dados = array($username, $password);
	 		
	 		if($resultado = selecionar_dados("user_info", true, $colunas, "WHERE username = '$dados[0]' AND password = '$dados[1]'" )) {
	 			if($resultado[1][0] == $dados[0] && $resultado[1][1] == $dados[1]){
	 				return true;
	 			} else {	
	 				return false;
	 			}
	 		}


	 	}

	 if(logar("pe_fontes", "teste2")){
	 	echo "yay";
	 } else {
	 	echo "shit";
	 }
	
?>