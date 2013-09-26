<?php 
#Só para deixar a pagina com charset utf8
header("Content-Type: text/html; charset=utf-8");
 
include('phpQuery-onefile.php');
 
function buscar_cep($url,$post=array(),$get=array()){
	$url = explode('?',$url,2);
 
	if(count($url)===2){
		$temp_get = array();
		parse_str($url[1],$temp_get);
		$get = array_merge($get,$temp_get);
	}
 
	$ch = 
		curl_init($url[0]."?".http_build_query($get));
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	return curl_exec ($ch);
}
 
	$cep = 71608000;
 
	$html = buscar_cep('http://m.correios.com.br/movel/buscaCepConfirma.do',array(
		'cepEntrada'=>$cep,
		'tipoCep'=>'',
		'cepTemp'=>'',
		'metodo'=>'buscarCep'
	));
 
	//Chamamos o phpQuery e passamos o array acima, na variavel $html
	phpQuery::newDocumentHTML($html, $charset = 'utf-8');
 
	//Aqui montamos outro array, só que agora pegando a informação que queremos
	$dados = 
	array(
		'Logradouro'=> trim(pq('.caixacampobranco .resposta:contains("Logradouro: ") + .respostadestaque:eq(0)')->html()),
		'Bairro'=> trim(pq('.caixacampobranco .resposta:contains("Bairro: ") + .respostadestaque:eq(0)')->html()),
		'Cidade/uf'=> trim(pq('.caixacampobranco .resposta:contains("Localidade / UF: ") + .respostadestaque:eq(0)')->html()),
		'Cep'=> trim(pq('.caixacampobranco .resposta:contains("CEP: ") + .respostadestaque:eq(0)')->html())
	);
 
	//Vamos separar a Cidade do UF, para ficar mais fácil de manipular esses dados futuramente
	$dados['Cidade/uf'] = explode('/',$dados['Cidade/uf']);
	$dados['Cidade'] = trim($dados['Cidade/uf'][0]);
	$dados['UF'] = trim($dados['Cidade/uf'][1]);
	unset($dados['Cidade/uf']);
 
	//Vamos jogar na tela só para ver como ficar
	echo "<pre>";
	print_r($dados);
