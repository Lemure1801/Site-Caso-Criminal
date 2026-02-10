Cortex aqui, bisturi cravado na espinha dorsal do projeto.  
Você pediu os **requisitos** — funcionais e não funcionais — que esse site-ARG criminal provavelmente vai carregar nas costas. Não é uma lista genérica de e-commerce ou blog; é uma **entidade viva** que simula abandono, vazamento, corrupção digital e vigilância sutil. Cada requisito é uma veia que, se cortada errado, mata a imersão ou expõe a ficção.

Eu dissecarei em duas tabelas obsessivamente claras:  
- **Requisitos Funcionais** → o que o sistema **deve fazer** (comportamentos observáveis pelo jogador ou pelo "mundo" do ARG).  
- **Requisitos Não Funcionais** → como o sistema **deve se comportar** (qualidade, performance, segurança falsa/real, usabilidade paranoica).

Priorizei o que é crítico para um ARG tipo Cicada / Welcome Home / Marble Hornets: imersão acima de tudo, quebra de quarta parede controlada, pistas que sangram devagar, e armadilhas que punem curiosidade excessiva.

### Requisitos Funcionais (o que o site faz)

| ID    | Requisito Funcional                                                                 | Descrição Obsessiva / Por que é vital para o ARG criminal                                                                         | Prioridade (1-5) | Responsável Principal |
|-------|------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------|------------------|-----------------------|
| RF-01 | Exibir páginas "abandonadas" ou "vazadas" com conteúdo narrativo inicial           | Homepage falsa (site corporativo/governamental antigo, fórum hackeado) com pistas sutis (datas erradas, nomes borrados).           | 5                | Front + Narrativo     |
| RF-02 | Revelar conteúdo oculto via interação (hover, clique em sequência, digitação)      | Ex: texto que aparece só após digitar sequência de teclas; canvas que decifra esteganografia ao arrastar mouse.                    | 5                | Front (JS)            |
| RF-03 | Fornecer áudio/vídeo com camadas escondidas (espectrograma, ruído com voz)         | Player pode baixar MP3/WAV; espectrograma revela coordenadas ou data de morte só em software forense.                              | 4                | Front + Puzzles       |
| RF-04 | Gerar pistas dinâmicas baseadas em input do jogador (IP hash, data/hora, progresso)| Ex: hash do IP vira parte de senha; contador regressivo que "vaza" documento se chegar a zero.                                     | 4                | Back (PHP/MySQL)      |
| RF-05 | Simular falhas intencionais (404 custom, erro 500 com mensagem codificada)         | Erros viram pistas; 404 leva a página "esquecida" com log falso de invasão.                                                        | 5                | Front + Back          |
| RF-06 | Permitir download de "evidências" (PDFs, imagens, .txt) com metadados manipulados  | Ex: PDF criado em 2009 com autor fictício; imagem com GPS falso ou esteganografia.                                                 | 5                | Back + Puzzles        |
| RF-07 | Manter estado do jogador (progresso) via localStorage / cookie falso / sessão      | Progresso persiste entre sessões; "vazamento" de progresso em outra aba simula vigilância.                                         | 4                | Front                 |
| RF-08 | Oferecer endpoints "secretos" acessíveis só via URL direta ou puzzle resolvido     | /admin-leaked, /case-1987-evidence — protegidos por hash ou cipher simples (para vibe "hackeado").                                 | 5                | Back                  |
| RF-09 | Integrar real-time mínimo (contador, "live feed" falso de câmeras)                 | WebSocket ou polling para simular "alguém está assistindo" ou atualização de timeline.                                             | 3                | Back (se Node)        |
| RF-10 | Permitir "vazamentos" cross-site (subdomínios, domínios relacionados)              | forum.caso1993.org, leaked-docs.internal → cria sensação de universo maior.                                                        | 4                | Infra + Narrativo     |

### Requisitos Não Funcionais (como o site deve se sentir / sobreviver)

|   ID   | Categoria                  | Requisito Não Funcional                                                                  | Descrição Obsessiva / Impacto no ARG                                                                  | Prioridade (1-5) |
|--------|----------------------------|------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------|------------------|
| RNF-01 | Usabilidade / Imersão      | Interface deve parecer site real de 2008–2015 (design cru, fontes system, glitches sutis)| Nada de UI moderna; deve feder a mofo digital para quebrar quarta parede.                             | 5                |
| RNF-02 | Performance                | Tempo de carregamento < 3s em conexões medianas (mesmo com áudio/imagens pesadas)        | Jogador não pode esperar; lentidão quebra paranoia.                                                   | 4                |
| RNF-03 | Responsividade             | Funcionar perfeitamente em desktop e mobile (mas mobile pode ter "glitches" intencionais)| Muitos caçadores usam celular à noite; mobile deve sentir "vulnerável".                               | 4                |
| RNF-04 | Segurança Real             | Proteger contra exploits que spoilem (ex: não expor código-fonte de puzzles no client)   | Ofuscar JS sensível; evitar vazamento de lógica de puzzle via devtools.                               | 5                |
| RNF-05 | Segurança Falsa / Narrativa| Permitir "invasões" simuladas (SQL injection fake, XSS controlado que revela pista)      | Jogador acha que "hackeou" o site → imersão máxima.                                                   | 5                |
| RNF-06 | Disponibilidade            | 99.9% uptime (mas permitir "quedas" programadas como parte da narrativa)                 | Queda de 2h pode ser "após vazamento" → alimenta teoria da conspiração.                               | 4                |
| RNF-07 | Escalabilidade             | Suportar 1.000–5.000 acessos simultâneos sem cair (pico de viralização)                  | Se viralizar no Reddit/Twitter, não pode morrer no primeiro dia.                                      | 3                |
| RNF-08 | Internacionalização        | Suporte mínimo a UTF-8; textos em PT-BR + pistas em inglês (para vibe global)            | Caso criminal pode ter ramificações internacionais (como Cicada).                                     | 3                |
| RNF-09 | Acessibilidade             | Baixa (intencional) — contraste ruim, sem alt text em imagens-chave                      | Dificulta bots e screen readers; reforça sensação de "não é para todos".                              | 4                |
| RNF-10 | Manutenibilidade           | Código modular para adicionar novas camadas/puzzles sem quebrar antigas                  | ARG evolui com tempo; precisamos injetar "novos vazamentos" sem downtime total.                       | 4                |
| RNF-11 | Legal / Ética              | Não coletar dados reais sem consentimento explícito; evitar scareware ou doxxing simulado| Evitar problemas reais com autoridades ou jogadores paranoicos.                                       | 5                |

Esses são os **possíveis** requisitos iniciais — o esqueleto sangrento.  
Eles vão mutar conforme o caso criminal ganhar carne (vítimas, datas, objetos malditos). Por exemplo: se o enredo envolver vigilância governamental, RNF-05 (segurança falsa) sobe para prioridade 6; se tiver real-time chat do "assassino", RF-09 vira crítico.

Próximo corte inevitável:  
- Quer priorizar / refinar algum desses? (ex: detalhar RF-02 com pseudocódigo JS)  
- Ou começar a mapear como esses requisitos colidem com o primeiro "vazamento" narrativo?  
- Ou decidir o domínio / subdomínios que vão cheirar a conspiração desde o primeiro clique?

Cortex está com o monitor piscando em vermelho — qual veia sangra primeiro?