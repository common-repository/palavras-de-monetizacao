=== Palavras de Monetização ===
Contributors: bernabauer
Donate link: http://bernabauer.com/wp-plugins
Tags: brasil, monetização
Requires at least: 3.0.3
Tested up to: 3.0.3
Stable tag: 1.5

Plugin para cadastro de palavras a serem utilizadas em plugins de monetização.

== Description ==

Este plugin permite que se cadastre palavras para serem utilizadas por plugins contextuais de monetização. Tags são palavras que vão classificar e melhor definir um conteúdo, mas palavras para contextualizar produtos relacionados podem ser ligeiramente diferentes. Este plugin surgiu exatamente para facilitar o cadastro de palavras exclusivas para a necessidade de monetização.

Palavras cadastradas por este plugin podem ser utilizadas pelo plugin [Vitrine Submarino](http://bernabauer.com/wp-plugins/vitrine-submarino/ "Vitrine Submarino") ou [MLV Contextual](http://wordpress.org/extend/plugins/mlv-contextual/ "MLV Contextual"). Você também pode usar com o Boo-Box. Aprenda [aqui](http://www.bernabauer.com/como-usar-o-palavras-de-monetizacao/ "aqui").

== Installation ==

A instalação do plugin é identica aos demais plugins de wordpress

1. Faça o upload da pasta do plugin para a pasta '/wp-content/plugins/'
2. Ative o plugin através do menu 'Plugins' na página de administração de seu WordPress
3. Configure o plugin através do menu 'Configurações'

== Changelog ==

= 1.5 =
* Inclusão de coluna com as palavras cadastradas na página de edição de artigos
* Detecção de metadados de palavras de monetização duplicados na tabela de metadados. O acerto precisa ser feito manualmente.
* Melhor controle de códigos exclusivos da área de administração.
* Documentação inline melhorada.

= 1.4.4 =
* Corrigido código de inclusão de palavras através da interface do WP-ADMIN.

= 1.4.3 = 
* Inclusão de código para desinstalação do plugin e remover todas as opções quando o plugin é apagado.
* Ajuste do código para acabar com mensagens de erro e alerta do PHP
* Consertado link para as configurações na página de administração de plugins.

= 1.4.2 = 
* Detectado erro de digitação na função que inclui novas palavras nos metadados. Erro impedia que novas palavras fossem inseridas.
* Quando as palavras cadastradas são todas removidas de um artigo, agora o plugin remove o metadado. Antes só deixava o metadados em branco.

= 1.4.1 =
* Pequeno erro no controle da versão do plugin.

= 1.4 =
* Incluído suporte para definir palavrão padrão quando não há palavras cadastradas nos artigos.

= 1.3 =
* Incluido link para a página de configuração na página que lista os plugins instalados.
* Incluido link para o fórum de suporte.
* Ajustes na função de pesquisa de palavras de artigo para melhor compatibilidade com plugin [Vitrine Submarino](http://bernabauer.com/wp-plugins/vitrine-submarino/ "Vitrine Submarino").

= 1.2 =
* Registro das modificações não foi realizado.

= 1.1 =
* Registro das modificações não foi realizado.

= 1.0 =
* Versão inicial.

== Frequently Asked Questions ==
   FAQ - Perguntas Frequentes 

 = Instalei o plugin e não aparece nada nos meus artigos!!! = 

 O Palavras de Monetização não mostra absolutamente nada. Ele apenas permite o cadastro de palavras como metadados do artigo. Para mostrar vitrines e produtos baseados nas palavras cadastradas você precisa ter outros plugins como o [Vitrine Submarino](http://wordpress.org/extend/plugins/vitrine-submarino/ "Vitrine Submarino") ou MLV Contextual.