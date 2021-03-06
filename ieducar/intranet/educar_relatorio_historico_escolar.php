<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/clsPDF.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Relat&oacute;rio Hist&oacute;rico Escolar" );
		$this->processoAp = "746";
	}
}

class indice extends clsCadastro
{

	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $ref_cod_escola;
	var $ref_cod_aluno;
	var $nm_aluno;
	var $nm_aluno_;

	var $ano;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		return $retorno;
	}

	function Gerar()
	{
		@session_start();
			$pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$nivel_acesso = $obj_permissoes->nivel_acesso( $pessoa_logada );
		if( $nivel_acesso == 1 || $nivel_acesso == 2 )
		{
			$obrigatorio = true;
			$get_escola = true;
			include("include/pmieducar/educar_campo_lista.php");
		}
		else
		{
			$this->ref_cod_escola = $obj_permissoes->getEscola( $pessoa_logada );
			$this->campoOculto("ref_cod_escola", $this->ref_cod_escola);
		}

		$this->nm_aluno = $this->nm_aluno_;

		$this->campoTexto("nm_aluno", "Aluno", $this->nm_aluno, 30, 255, true, false, false, "", "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/>","","",true);
		$this->campoOculto("nm_aluno_", $this->nm_aluno_);
		$this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);

		$this->acao_enviar = false;

		$this->array_botao = array( "Gerar Relat&oacute;rio" );
		$this->array_botao_url_script = array( "showExpansivelImprimir(400, 200,  'educar_relatorio_historico_escolar_proc.php',['ref_cod_escola', 'ref_cod_aluno'], 'Relat�rio i-Educar' )" );
	}

	function Novo()
	{
		return true;
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
<script>

function pesquisa_aluno()
{
	pesquisa_valores_popless('educar_pesquisa_aluno.php?ref_cod_escola='+document.getElementById('ref_cod_escola').value)
}

</script>