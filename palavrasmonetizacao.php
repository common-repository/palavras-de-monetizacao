<?php
/*
Plugin Name: Palavras de Monetização
Plugin URI: http://www.bernabauer.com/wp-plugins/
Description: Cadastro de palavras utilizaveis para monetização. Este plugin é parte fundamental para o <a href="http://www.bernabauer.com/wp-plugins/">Vitrine Submarino</a>.
Version: 1.5
Author: Bernardo Bauer
Author URI: http://www.bernabauer.com/
*/

global $wpdb;
global $pm_options;
global $version;
global $domain;

$domain = '';
$version = "1.5";

$pm_options = get_option('pm_options');

register_activation_hook(__FILE__, 'pm_activate');
register_deactivation_hook(__FILE__, 'pm_deactivate');

if( is_admin() ) {

	// insere formulário para palavras de monetização na edição de artigo
	add_action('edit_post', 'pm_update_words');
	
	// atualiza palavras de monetização quando o artigo é salvo ou publicado
	add_action('publish_post', 'pm_update_words');
	add_action('save_post', 'pm_update_words');
	
	add_action('admin_menu', 'pm_create_meta_box');
	
	add_action('admin_notices', 'pm_alerta');
	
	// Run plugin code and init
	add_action('admin_menu', 'pm_option_menu');
	
	if (isset( $pm_options['coluna'] ) && $pm_options['coluna'] == "sim") {
	
		// inclui coluna com palavras de monetização na lista de artigos
		add_filter('manage_posts_columns', 'pm_column');
		
		// mostra dados na coluna Palavras de Monetização
		add_action('manage_posts_custom_column', 'pm_custom_column', 10, 2);
	}

	//filtro para mostrar apenas artigos com palavra de monetização selecionada
	add_filter('posts_where', 'pm_posts_where');
	
	// mostra link para configuração do plugin na página de administração de plugins
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'pm_plugin_actions' );

}

/**************************************************************************************************
 *  Rearruma array com as colunas de dados da página de edição de artigos
 */
function pm_column($defaults) {

	//guarda duas colunas para incluir no final do array
	$v = $defaults['comments'];
	$x = $defaults['date'];
	unset($defaults['comments']);
	unset($defaults['date']);

	// inclui nova coluna
    $defaults['pm'] = 'Palavras de Monetização';

	// volta com as duas colunas
	$defaults['comments'] = $v;
	$defaults['date'] = $x;

	return $defaults;
}

/**************************************************************************************************
 *  Mostra as palavras de monetização na coluna
 */

function pm_custom_column($column_name, $post_id) {
    global $wpdb;
    global $pm_options;
    $links ='';
    
   	if ($pm_options['meta'] == "mlv")
		$pm_meta_tag = 'mlv"';
	else
		$pm_meta_tag = 'tags';
    
    if( $column_name == 'pm' ) {
    	$pms = explode(",", get_post_meta($post_id, $pm_meta_tag, true));
    	foreach ($pms as $pm) {
			$links .= "<a href='edit.php".fixGet("pm=".ltrim($pm))."'>".ltrim($pm)."</a>, ";
		}
    }
    	echo rtrim($links,", ");
}

   // Author: Alberto Lepe (www.alepe.com)
    /* Process $_GET to preserve user custom parameters
     * the arguments is a list of URL parameters that should be removed/changed from URL
     * for example:
     *
     * URL = "index.php?s=1&fi=2&m=4&p=3
     *
     * if called: fixGet("s"); the result has to be: ?fi=2&m=4&p=3
     * if called: fixGet("s&m"); the result has to be: ?fi=2&p=3
     * if called: fixGet("s=4"); the result has to be: ?s=4&fi=2&m=4&p=3
     * if called: fixGet("s=2&m"); the result has to be: ?s=2&fi=2&p=3
     * if called: fixGet("s=&m=3"); the result has to be: ?s=&fi=2&m=3&p=3
     * if called: fixGet("s=2&m="); the result has to be: ?s=2&fi=2&m=&p=3
     * Special: when it ends with a =":" its to leave it open at the end
     * (just first occurrence) to facilitate concatenation:
     * if called: fixGet("s=2&m:"); the result has to be: ?s=2&fi=2&p=3&m
     * if called: fixGet("s=2&m:="); the result has to be: ?s=2&fi=2&p=3&m=
     *
     * Usage with HTML (using the URL example above and $id = 99):
     *
     * <a href="index.php<?php echo fixGet('m=2&s&fi:=').$id ?>" >Link</a>
     * Explanation: change "m" to 2, delete "s" and "fi" gets the $id value. ("p" is kept as it is not specified)
     * will output: <a href='index.php?m=2&p=3&fi=99'>Link</a>
     */
    function fixGet($args) {
        if(count($_GET) > 0) {
            if(!empty($args)) {
                $lastkey = "";
                $pairs = explode("&",$args);
                foreach($pairs as $pair) {
                    if(strpos($pair,":") !== false) {
                        list($key,$value) = explode(":",$pair);
                        unset($_GET[$key]);
                        $lastkey = "&$key$value";
                    } elseif(strpos($pair,"=") === false)
                        unset($_GET[$pair]);
					else {
                        list($key, $value) = explode("=",$pair);
                        $_GET[$key] = $value;
                    }
                }
            }
            return "?".((count($_GET) > 0)?http_build_query($_GET).$lastkey:"");
        }
	}


/**************************************************************************************************
 *  Metabox para inclusão das Palavras de Monetização
 */
function pm_create_meta_box() {
	if ( function_exists('add_meta_box') ) {
		add_meta_box( 'pm_word_input', 'Palavras de Monetização', 'pm_word_input', 'post', 'normal', 'high' );
	}
}

/**************************************************************************************************
 *  Ativação do plugin
 */
function pm_activate() {

	global $wpdb;
	global $version;
	global $pm_options;
	
	/* INSTALACAO */
	if ($pm_options == FALSE) {
		$pm_options = array('version'=> $version, 'palpad'=> 'celular, LCD, DVD', 'meta'=>'tags', 'coluna'=>'sim');
		add_option('pm_options', $pm_options);
	} else {
		/* UPGRADE */
		$pm_options['version'] = $version;
		$pm_options['coluna'] = 'sim';
		update_option('pm_options',$pm_options);
	}

}


/**************************************************************************************************
 *  Desativação do plugin
 */
 function pm_deactivate() {

	global $wpdb;
	
#	delete_option('pm_options');
}


/**************************************************************************************************
 *  Pega palavras armazenadas no artigo
 */
function pm_get_words($post_id = NULL) 
{	
	global $pm_options;
   
	if ($pm_options['meta'] == "mlv")
		$pm_meta_tag = 'mlv"';
	else
		$pm_meta_tag = 'tags';

	$tags = get_post_custom_values($pm_meta_tag, $post_id);
	
	if(!empty($tags[0]))
	{
		$tags_array = explode(", ", $tags[0]);
	} else
		$tags_array = explode(", ", $pm_options['palpad']);

	return($tags_array); // return an array of tags
} 

/**************************************************************************************************
 *  Função para usar com o boobox
 */
function pm_boobox() {

	global $pm_options;

	$pal = pm_get_words();
	
	echo $pal[0];

}


/**************************************************************************************************
 *  Caixa de entrada de dados no metabox
 */
function pm_word_input() {

	global $post;
	global $pm_options;
    
	if ($pm_options['meta'] == "mlv")
		$pm_meta_tag = 'mlv"';
	else
		$pm_meta_tag = 'tags';
	
	$palavrasmonetizacao = get_post_meta($post->ID, $pm_meta_tag, true);
	
	echo '<div><input type="text" name="palavrasmonetizacao" id="palavrasmonetizacao" style="width:99%;" value="' . $palavrasmonetizacao . '" /><input type="hidden" name="pm-key" id="pm-key" value="' . wp_create_nonce('pm') . '" /><span class="howto">Separe as palavras com vírgulas.</span></div>';

}

/**************************************************************************************************
 *  Arruma palavras para cadastra na base
 */
function pm_fmt_pal($setting) {

	$palavras = '';
	
	$setting = explode(",", $setting);

	for ($i=0; $i<count($setting); $i++) {
		if ($i == count($setting)-1) {
			$palavras .= trim($setting[$i]);
		} else {
			$palavras .= trim($setting[$i]).", ";
		}
	}

	return rtrim(rtrim($palavras), ",");
}

/**************************************************************************************************
 *  Atualiza dados armazenados no artigo
 */
function pm_update_words($id)
{
	global $pm_options;
	$meta_exists = '';
    
	if ($pm_options['meta'] == "mlv")
		$pm_meta_tag = 'mlv"';
	else
		$pm_meta_tag = 'tags';

  // authorization
	if ( !current_user_can('edit_post', $id) )
		return $id;
	// origination and intention
	if (isset($_POST['meta'])) {
		if ( !wp_verify_nonce($_POST['pm-key'], 'pm') )
			return $id;
	}
	if (isset($_POST['palavrasmonetizacao'])) {
		$setting = $_POST['palavrasmonetizacao'];

		$palavras = pm_fmt_pal($setting);
		if (!$setting)
			delete_post_meta($id, $pm_meta_tag);
		else
			$meta_exists = update_post_meta($id, $pm_meta_tag, $palavras);
	}
}

/**************************************************************************************************
 *  Menu de configuracao
 */
function pm_option_menu() {
    if ( function_exists('add_options_page') ) {
        add_options_page('Palavras de Monetização', 'Palavras de Monetização', 'manage_options', 'palavrasmonetizacao.php', 'pm_options_subpanel');
	}
}

/**************************************************************************************************
 *  Alerta sobre problemas com a configuracao do plugin
 */
function pm_alerta() {

	global $pm_options;
	global $version;
	global $domain;
	$msg = '';

	if (  !isset($_POST['info_update']) ) {
		if ($pm_options['version'] != $version) {
			$msg = '* Parece que você atualizou a versão nova sem desativar o plugin!! Por favor desative e re-ative <a href="plugins.php">aqui</a>. Você está usando o código na versão '.$version.' e sua base de dados para o plugin é '.$pm_options['version'];
		} 
		
		if ($msg) {
			echo "<div class='updated fade-ff0000'><p><strong>".__('Alerta Palavras de Monetização!', $domain)."</strong><br /> ".$msg."</p></div>";
		}
		return;
	}
}

/**************************************************************************************************
 *  Pagina de opcoes
 */
function pm_options_subpanel() {

	global $wpdb;
	global $pm_options;
	
	$pm_meta_mlv = '' ;
	$pm_meta_padrao = '' ;

	//processa novos dados para atualizacao
    if ( isset($_POST['info_update']) ) {

        if (isset($_POST['meta'])) 
			$pm_options['meta'] = $_POST['meta'];
        if (isset($_POST['palpad'])) 
			$pm_options['palpad'] = pm_fmt_pal($_POST['palpad']);
        if (isset($_POST['coluna'])) 
			$pm_options['coluna'] = "sim";
		else
			$pm_options['coluna'] = "não";
            
		//atualiza base de dados com informacaoes do formulario		
		update_option('pm_options',$pm_options);
    }
    if (array_key_exists('meta', $pm_options)) {
		if ($pm_options['meta'] == "mlv")
			$pm_meta_mlv = 'checked=\"checked\"';
		else
			$pm_meta_padrao = 'checked=\"checked\"';
	}
?>
	<div class=wrap>
	
	<h2>Palavras de Monetização <?php echo $pm_options['version']; ?></h2>

    <h2>Configurações</h2>
  <form method="post">

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top">Identificação de Metadados</th>
		<td>
			<input type="radio" id="pad" name="meta" value="padrao" <?php echo $pm_meta_padrao; ?>> <label for="pad">Padrão</label>
			<br>
			<input type="radio" id="mlv" name="meta" value="mlv" <?php echo $pm_meta_mlv; ?>> <label for="mlv">MLV Contextual</label>
		
			<label for="id"><br />Para que o plugin MLV Contextual veja palavras cadastradas com este plugin você precisa selecionar a opção MLV Contextual, caso contrário fique com a opção Padrão.</label>
		</td>
	 </tr>
	</table>
	<br />

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top">Palavra padrão</th>
		<td>
			 <input name="palpad" type="text" id="palpad" value="<?php echo $pm_options['palpad']; ?>" size=28  />
		
			<label for="palpad"><br />Defina a palavra padrão que será utilizada quando não há palavra cadastrada para o artigo. Você definir mais de uma palavra, basta separar por vírgulas.</label>
		</td>
	 </tr>
	</table>
	<br />

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top">Coluna com Palavras de Monetização</th>
		<td>
			 <input type="checkbox" name="coluna" value="sim" <?php if ($pm_options['coluna']=="sim") { echo "checked";} ?> />
		
			<label for="coluna">Habilite esta opção para mostrar uma coluna com as Palavras de Monetização na <a href="edit.php">página que lista artigos</a>.</label>
		</td>
	 </tr>
	</table>
	<br />

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top">Detecção de Metadados duplicados</th>
		<td>
			Confira abaixo os artigos que estão com Palavras de Monetização Duplicados <br />
			<?php

				$select = "SELECT distinct(post_id) as post_id, count(post_id) as post_id_count, wp_posts.ID as ID, wp_posts.post_title as title FROM wp_postmeta LEFT OUTER JOIN wp_posts ON post_id = wp_posts.ID WHERE wp_postmeta.meta_key = 'tags' AND ID IS NOT NULL GROUP BY post_id HAVING post_id_count > 1 ORDER BY title DESC";

				$results = $wpdb->get_results( $wpdb->prepare($select) , ARRAY_A);
				$i=1;
					foreach ($results as $result) {
						echo "<br />".$i."- <a href='post.php?post=".$result['post_id']."&action=edit'>".$result['title']."</a> ";
						$i++;
					}
				if ($i==1)
					echo "Legal! Não existem metadados duplicados. Sua base de dados está limpinha! ;-)";
			?>
		</td>
	 </tr>
	</table>
	<br />




<div class="submit">
  <input type="submit" name="info_update" value="Atualizar" />
</div>
</form> 

<?php
}

/**************************************************************************************************
 * Link para configuração do plugin na página de administração de plugins
 */
function pm_plugin_actions($links){

	$settings_link = '<a href="options-general.php?page=palavrasmonetizacao.php">' . __('Settings') . '</a>';
	array_unshift( $links, $settings_link );
 
	return $links;
}

/**************************************************************************************************
 * Processa o filtro de artigos por determinada Palavra de Monetização
 */
function pm_posts_where($where) {
	if( is_admin() ) {
		global $wpdb;
		if ( isset( $_GET['pm'] ) && !empty( $_GET['pm'] ) ) {
			$where .= " AND ID IN (SELECT wp_posts.ID FROM wp_posts LEFT OUTER JOIN wp_postmeta ON wp_posts.ID = wp_postmeta.post_id WHERE wp_postmeta.meta_value LIKE '%".$_GET['pm']."%')";
		}
		return $where;
	} else
		return '';
}


?>