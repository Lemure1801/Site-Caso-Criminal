Resumo sobre HTML
O que é HTML?

HTML (HyperText Markup Language) é uma linguagem de marcação, e não uma linguagem de programação.

Isso significa que ele não executa lógica, cálculos ou toma decisões como linguagens de programação (Python, JavaScript, etc.).

O HTML serve para estruturar o conteúdo de uma página da web, organizando textos, imagens, links e outros elementos que serão exibidos no navegador.

Ele funciona por meio de tags, que indicam ao navegador o papel de cada conteúdo dentro da página.

Anatomia de uma Tag

A maioria das tags HTML possui três partes principais:

<tag>conteúdo</tag>

Tag de abertura: <tag>

Conteúdo: o que fica dentro da tag

Tag de fechamento: </tag>

Exemplo:

<p>Este é um parágrafo.</p>

Algumas tags não possuem fechamento, como a tag de imagem:

<img src="imagem.jpg">
Estrutura básica obrigatória de um documento HTML

Todo documento HTML deve começar com uma estrutura padrão:

<!DOCTYPE html>
<html>
<head>
    <title>Título da página</title>
</head>
<body>
    Conteúdo da página
</body>
</html>
Explicação:

<!DOCTYPE html> → Informa ao navegador que o documento é do tipo HTML5.

<html> → Indica o início do documento HTML.

<head> → Contém informações sobre a página (como título, configurações e metadados).

<body> → Contém o conteúdo visível da página (textos, imagens, links, etc.).

Glossário das principais tags
<h1> até <h6>

São usadas para títulos e subtítulos.

<h1> é o título mais importante.

<h6> é o menos importante.

<p>

Usada para criar parágrafos de texto.

Exemplo:

<p>Este é um parágrafo.</p>
<a>

Usada para criar links.

Exemplo:

<a href="https://www.google.com">Ir para o Google</a>

O atributo href indica o destino do link.

<img>

Usada para inserir imagens na página.

Exemplo:

<img src="imagem.jpg" alt="Descrição da imagem">

src → Caminho da imagem

alt → Texto alternativo (importante para acessibilidade)

<div>

A tag <div> é usada para organizar e agrupar elementos dentro da página.

Ela não tem significado visual por si só, mas é muito importante para:

Organizar o código

Criar divisões na página

Aplicar estilos com CSS

Melhorar o aninhamento dos elementos

Exemplo:

<div>
    <h1>Título</h1>
    <p>Texto dentro da divisão.</p>
</div>

A <div> ajuda a manter o código estruturado e facilita a manutenção e estilização do site.