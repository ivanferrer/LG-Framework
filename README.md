LG-Framework
============

Para utilizar este APP com o LG Framework, é necessário realizar alguns passos iniciais. Vamos começar?

Passo 1 - Start
---------------

Os arquivos a seguir devem ser editados conforme indicado:

#### `.htaccess`
Na linha que define o "RewriteBase", indique a raiz do APP (ex: se for www.dominio.com.br/pasta/, a raiz será "/pasta/". Se não houver, deixe apenas "/")

#### `config/config_smarty.conf`
Na linha que define a "pasta_raiz", indique o mesmo valor do "RewriteBase" do ".htaccess"

#### `config/config.php`
Neste arquivo você tem uma série de opções sobre o funcionamento do Framework. Sinta-se a vontade para brincar ;]

#### `config/database.php`
Neste arquivo você indica os dados de acesso do seu banco de dados, para ambientes de produção e teste. Você também pode editar a forma como o sistema identifica produção ou teste no arquivo config.php

Passo 2 - Criando Classes
-------------------------

#### `Modelos e DAOs`
Estes podem ser gerados automaticamente pela página de "setup" do LG Framework. Basta acessar www.dominio.com/setup (esta opção deve estar habilitada no config.php) 
Modelos não devem ter sua estrutura alterada, mas você pode alterar os Gets and Setters para lançarem exceptions em situações não previstas pelo LGF. 
Já os DAOs tem as funcionalidades básicas de um CRUD, para efetuar querys personalizadas, crie seus próprios métodos dentro dos seus DAOs.

#### `Views e Controllers`
Nas classes de exemplo dentro das pastas, você verá um comentário no início que explica o que é necessário e como funcionam as Views e os Controllers