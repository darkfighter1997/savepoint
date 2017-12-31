<?php
	/*
		Funções de Segurança e de Tratamento de Dados
	*/

	/* 
		Função de validação de input - Testado e Funcional - 17/11/17
		Esta função vai limpar qualquer string recebida de um utilizador num formulário de código javascript e/ou afins (exceto mysql, ver trello)
		Exemplo:
		$dados_tratados = $data_validation($_POST['dados_nao_tratados']);
	*/
	function validar_dados($data){ //Inicializar a função e as variáveis
		$data = trim($data); // Retira os espaços no início e no final de uma string
		$data = stripslashes($data); // Tira as aspas de texto com aspas (tipo strings javascript)
		$data = htmlspecialchars($data); // Converte caracteres de html especiais (tipo < ou &) para código html (& = &amp; e < = &lt;) tornando-os inuteis quando utilizados via input
		return $data; //Devolve os dados tratados
	}
?>