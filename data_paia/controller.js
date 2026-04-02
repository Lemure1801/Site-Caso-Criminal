let dia = 1;
let mes = 1;
let ano = 1900;
let rodando = true;
let velocidade = 50;

const display = document.getElementById('data-display');
const slider = document.getElementById('speed-slider');
const btnStop = document.getElementById('btn-stop');
const btnRandom = document.getElementById('btn-random');

// Loop de data infinita
function atualizarData() {
    if (rodando) {
        dia++;
        if (dia > 31) { dia = 1; mes++; }
        if (mes > 12) { mes = 1; ano++; }
        if (ano > 2100) { ano = 1900; }
        
        display.innerText = `${dia.toString().padStart(2, '0')} / ${mes.toString().padStart(2, '0')} / ${ano}`;
    }
    
    // A velocidade é inversamente proporcional ao valor do slider (para irritar)
    setTimeout(atualizarData, 101 - slider.value);
}

btnStop.addEventListener('click', () => {
    rodando = !rodando;
    btnStop.innerText = rodando ? "PARAR NO DIA CERTO" : "OPS, VOLTAR A RODAR";
    if (!rodando) {
        alert(`Você selecionou: ${display.innerText}. Tem certeza? (Não importa, o sistema vai reiniciar)`);
        rodando = true;
    }
});

// Faz o botão aleatório fugir quando o mouse chega perto
btnRandom.addEventListener('mouseover', () => {
    btnRandom.style.position = 'absolute';
    btnRandom.style.top = Math.random() * 90 + 'vh';
    btnRandom.style.left = Math.random() * 90 + 'vw';
});

// Inicia o caos
atualizarData();