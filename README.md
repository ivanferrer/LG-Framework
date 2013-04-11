LG-Framework
============

Para utilizar este APP com o LG Framework, � necess�rio realizar alguns passos iniciais. Vamos come�ar?

Passo 1 - Start
---------------

Os arquivos a seguir devem ser editados conforme indicado:

#### `.htaccess`
Na linha que define o "RewriteBase", indique a raiz do APP (ex: se for www.dominio.com.br/pasta/, a raiz ser� "/pasta/". Se n�o houver, deixe apenas "/")

#### `config/config_smarty.conf`
Na linha que define a "pasta_raiz", indique o mesmo valor do "RewriteBase" do ".htaccess"

#### `config/config.php`
Neste arquivo voc� tem uma s�rie de op��es sobre o funcionamento do Framework. Sinta-se a vontade para brincar ;]

#### `config/database.php`
Neste arquivo voc� indica os dados de acesso do seu banco de dados, para ambientes de produ��o e teste. Voc� tamb�m pode editar a forma como o sistema identifica produ��o ou teste no arquivo config.php

Passo 2 - Criando Classes
-------------------------

#### `Modelos e DAOs`
Estes podem ser gerados automaticamente pela p�gina de "setup" do LG Framework. Basta acessar www.dominio.com/setup (esta op��o deve estar habilitada no config.php) 
Modelos n�o devem ter sua estrutura alterada, mas voc� pode alterar os Gets and Setters para lan�arem exceptions em situa��es n�o previstas pelo LGF. 
J� os DAOs tem as funcionalidades b�sicas de um CRUD, para efetuar querys personalizadas, crie seus pr�prios m�todos dentro dos seus DAOs.

#### `Views e Controllers`
Nas classes de exemplo dentro das pastas, voc� ver� um coment�rio no in�cio que explica o que � necess�rio e como funcionam as Views e os Controllers

