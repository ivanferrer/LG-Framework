LG-Framework
============

Acesse https://github.com/luizguilhermesj/LG-Framework-Demo-App

Para visualizar um Demo App do LG Framework.

Resumo
------

O objetivo do LG framework em PHP é fornecer uma base consistente de código em OOP utilizando o design pattern MVC+DAO.

Além disso, todos os erros geram exceptions e toda exception precisa ser tratada. Seja exibindo mensagens (ex.: login incorreto), redirecionando para páginas (ex.: 404, pág não encontrada e 500 erro interno no servidor) ou até mesmo fazendo log e disparando e-mails.

A saída padrão é processada com o Smarty. Desta maneira, o código PHP contém somente código PHP. A utilização de html fica em 99% dos casos retida aos templates do Smarty. Desta maneira a manutenções de layout e de sistema ficam bem fáceis de serem aplicadas.

Views não são páginas PHP, são classes. Seus métodos são acessados diretamente pela URL. O mesmo acontece com os controllers, mas é necessário colocar "c/" na chamad (ex: "dominio.com/c/controller/metodo".
Fiz isto para que a nomenclatura das páginas possa ser amigável e as regras de negócios possam ser acessadas (ex.: um formulário de cadastro na view pode fazer post direto para /c/controller/insert" e ter o retorno da ação - erro ou sucesso + mensagem - na mesma view).

Outro item interessante é a forma como as conexões com o banco de dados são realizadas. Elas sempre são realizadas com transactions e exigem commit ao término. Caso esteja fazendo uma operação em massa (ex: 50 updates do campo status na tabela usuario), você pode informar para que o LG Framework faça um prepared statement automaticamente.

Bem, o projeto está caminhando e ainda tem muitas melhorias a serem feitas.
O código é aberto e está disponível no GitHub.
Se tiver alguma sugestão, fique à vontade para sugerir um commit
