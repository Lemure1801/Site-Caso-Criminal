# Resumo CSS - Caso 91-1821

## Utilidade do CSS e por que usar arquivo externo (style.css)

CSS (Cascading Style Sheets) é a linguagem responsável por definir a aparência visual e o layout de uma página web. Ele controla cores, fontes, espaçamentos, posicionamentos, animações e até efeitos que simulam "envelhecimento" ou "glitches", essenciais para criar imersão em um ARG como o Caso 91-1821.

Usar um arquivo externo **style.css** é o método mais recomendado porque:
- Separa completamente o conteúdo (HTML) da apresentação (CSS), facilitando manutenção e leitura do código.
- Permite reutilizar os mesmos estilos em várias páginas (ex: index.html e inscricao.html).
- Melhora o desempenho: o navegador faz cache do arquivo .css, carregando mais rápido em visitas subsequentes.
- Facilita versionamento no Git (commits só do CSS quando mudamos visual).
- Torna possível aplicar resets globais e temas consistentes (ex: vibe "arquivo antigo de 1991").

## Glossário das principais propriedades vistas

- **color**: Define a cor do texto. Ex: color: #2c1a0d; (marrom escuro, como tinta velha).
- **background-color**: Define a cor de fundo de um elemento. Ex: background-color: #f8f1e9; (off-white amarelado, papel antigo).
- **margin**: Cria espaço externo ao redor do elemento (fora da borda). Ex: margin: 3rem auto; (centraliza horizontalmente).
- **padding**: Cria espaço interno, entre o conteúdo e a borda do elemento. Ex: padding: 2.5rem; (dá "respiro" ao texto como em relatório).
- **display: flex**: Transforma o elemento em um container flexível. Permite alinhar itens filhos facilmente (ex: centralizar conteúdo vertical ou horizontal). Ex: display: flex; flex-direction: column; align-items: center;
- **box-shadow**: Adiciona sombra projetada ao elemento. Ex: box-shadow: 0 4px 12px rgba(0,0,0,0.4); (dá profundidade, como papel sobre mesa).
- **font-family**: Define a fonte do texto. Ex: font-family: 'Georgia', serif; (serif para tom documental antigo).
- **text-shadow**: Adiciona sombra ao texto. Ex: text-shadow: 2px 2px 4px rgba(0,0,0,0.6); (efeito envelhecido ou "sangrento").
- **border**: Adiciona borda ao elemento. Ex: border: 1px solid #d2b48c; (borda bege, como pasta antiga).

## Como as classes ajudam na estilização de elementos específicos

As **classes** (ex: class="intro" ou class="evidencia") são como etiquetas que colocamos nos elementos HTML para selecioná-los de forma específica no CSS.  

Exemplo:
```html
<section class="intro">...</section>