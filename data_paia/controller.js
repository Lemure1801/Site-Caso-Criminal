document.addEventListener('DOMContentLoaded', () => {
    const btnMudarAno = document.getElementById('btn-mudar-ano');
    const inputAno = document.getElementById('ano');
    const btnResolverConta = document.getElementById('btn-resolver-conta');
    const contaContainer = document.getElementById('conta-container');
    const contaTexto = document.getElementById('conta-texto');
    const inputResposta = document.getElementById('resposta-conta');
    const btnConfirmarConta = document.getElementById('btn-confirmar-conta');
    const btnSalvar = document.getElementById('btn-salvar');
    const mensagem = document.getElementById('mensagem');

    let diaCorreto = null;
    let contaAtual = null;

    // Botão que troca o ano para um valor aleatório (1950-2050)
    btnMudarAno.addEventListener('click', () => {
        const anoAleatorio = Math.floor(Math.random() * (2050 - 1950 + 1)) + 1950;
        inputAno.value = anoAleatorio;
    });

    // Botão para resolver conta e liberar o dia
    btnResolverConta.addEventListener('click', () => {
        // Gera uma conta simples
        const a = Math.floor(Math.random() * 20) + 5;
        const b = Math.floor(Math.random() * 15) + 3;
        contaAtual = a + b;

        contaTexto.textContent = `Quanto é ${a} + ${b}?`;
        contaContainer.classList.remove('hidden');
        inputResposta.focus();
    });

    // Verifica resposta da conta
    btnConfirmarConta.addEventListener('click', () => {
        if (parseInt(inputResposta.value) === contaAtual) {
            diaCorreto = Math.floor(Math.random() * 28) + 1; // dia aleatório 1-28
            alert(`Correto! O dia liberado é ${diaCorreto}. Agora salve a data.`);
            btnSalvar.classList.remove('hidden');
        } else {
            alert('Resposta errada. Tente novamente.');
            inputResposta.value = '';
        }
    });

    // Salvar no IndexedDB
    btnSalvar.addEventListener('click', () => {
        const dataNascimento = {
            nome: "Antônia Gutierrez",
            data: `${diaCorreto || '??'}/${inputAno.value}`,
            timestamp: new Date().toISOString()
        };

        if (typeof window.salvarData === 'function') {
            window.salvarData(dataNascimento)
                .then(() => {
                    mensagem.classList.remove('hidden');
                    mensagem.innerHTML = `✅ Data salva com sucesso!<br>Valor registrado: ${dataNascimento.data}`;
                })
                .catch(err => console.error(err));
        }
    });
});