// JS geral do sistema.
// Modal utilitário.
window.coteliModal = function (title, bodyHtml) {
    $('#modalInfoLabel').text(title);
    $('#modalInfo .modal-body').html(bodyHtml);
    const modal = new bootstrap.Modal(document.getElementById('modalInfo'));
    modal.show();
};

document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('.app-shell');
    const overlay = document.querySelector('.sidebar-overlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    const closeMobileSidebar = () => {
        if (!shell) return;
        shell.classList.remove('is-open');
        if (overlay) {
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
        }
    };

    if (toggleBtn && shell) {
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                const willOpen = !shell.classList.contains('is-open');
                shell.classList.toggle('is-open');
                if (overlay) {
                    overlay.style.opacity = willOpen ? '1' : '0';
                    overlay.style.visibility = willOpen ? 'visible' : 'hidden';
                }
            } else {
                shell.classList.toggle('is-collapsed');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            closeMobileSidebar();
        }
    });

    const applySeiMask = (input) => {
        if (!input) return;
        const format = (raw) => {
            const digits = raw.replace(/\D/g, '').slice(0, 16); // 6+6+4 = 16 dígitos
            const p1 = digits.slice(0, 6);
            const p2 = digits.slice(6, 12);
            const p3 = digits.slice(12, 16);
            let out = 'SEI-';
            if (p1) out += p1;
            if (p2) out += '/' + p2;
            if (p3) out += '/' + p3;
            return out;
        };
        const apply = () => { input.value = format(input.value); };
        input.addEventListener('input', apply);
        apply();
    };

    const setupPregaoBase = () => {
        const form = document.querySelector('form[action*="pregoes/salvar-base"]');
        if (!form) return;

        applySeiMask(form.querySelector('input[name="processo_sei"]'));

        const numInput = form.querySelector('input[name="id_pregao"]');
        const tipoInput = form.querySelector('select[name="id_tipo_pregao"]');
        const anoInput = form.querySelector('input[name="ano_pregao"]');
        const dupUrl = form.dataset.dupUrl || '';
        const novaRepUrl = form.dataset.novaRepUrl || '';

        if (numInput) {
            const normalize = (raw) => raw.replace(/\D/g, '').slice(0, 3);
            numInput.addEventListener('input', () => {
                numInput.value = normalize(numInput.value);
            });
            numInput.addEventListener('blur', () => {
                const digits = normalize(numInput.value);
                if (digits !== '') {
                    numInput.value = digits.padStart(3, '0');
                }
            });
            if (numInput.value) {
                numInput.value = normalize(numInput.value).padStart(3, '0');
            }
        }

        if (!numInput || !tipoInput || !anoInput || !dupUrl) return;

        let chaveValida = true;

        const checarDuplicidade = async () => {
            const id_tipo_pregao = parseInt(tipoInput.value || '0', 10);
            const ano_pregao = parseInt(anoInput.value || '0', 10);
            const id_pregao = (numInput.value || '').trim();
            const id_pregao_repeticao = 0;

            if (!id_tipo_pregao || !ano_pregao || id_pregao.length !== 3) {
                return;
            }

            try {
                const params = new URLSearchParams({
                    id_tipo_pregao,
                    ano_pregao,
                    id_pregao,
                    id_pregao_repeticao
                });
                const resp = await fetch(`${dupUrl}?${params.toString()}`, { method: 'GET' });
                const data = await resp.json();
                if (data.status === 'duplicado_base') {
                    chaveValida = false;
                    const sugBase = data.pregao?.proximo_base ?? '';
                    const sugRep = data.pregao?.proxima_repeticao_sugerida ?? '';
                    const idBase = data.pregao?.id_base_pregoes ?? '';
                    const repNum = data.pregao?.proxima_repeticao_num ?? '';

                    const body = `
                        <p>Já existe um pregão cadastrado com as informações abaixo:</p>
                        <div class="alert alert-light border">
                            <div><strong>Número atual:</strong> ${data.pregao?.descricao_resumida ?? ''}</div>
                            <div><strong>Próximo pregão disponível:</strong> ${sugBase}</div>
                            <div><strong>Próxima repetição sugerida:</strong> ${sugRep}</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-end modal-dup-botoes">
                            <button type="button" class="btn btn-primario" id="btnModalNovoPregao">Cadastrar novo pregão</button>
                            <button type="button" class="btn btn-sucesso" id="btnModalNovaRep">Criar nova repetição</button>
                        </div>
                    `;
                    window.coteliModal('Pregão já cadastrado', body);

                    setTimeout(() => {
                        const btnNovo = document.getElementById('btnModalNovoPregao');
                        const btnRep = document.getElementById('btnModalNovaRep');
                        if (btnNovo) {
                            btnNovo.onclick = () => {
                                if (sugBase) {
                                    const numero = (sugBase.split(' ')[1] || '').split('/')[0] || '';
                                    numInput.value = numero.padStart(3, '0');
                                }
                                const modalEl = document.getElementById('modalInfo');
                                const modal = bootstrap.Modal.getInstance(modalEl);
                                modal && modal.hide();
                                setTimeout(() => numInput.focus(), 100);
                            };
                        }
                        if (btnRep && idBase && repNum && novaRepUrl) {
                            btnRep.onclick = () => {
                                const url = new URL(novaRepUrl, window.location.origin);
                                url.searchParams.set('id_base_pregao_r0', idBase);
                                url.searchParams.set('rep_sugerida', String(repNum));
                                window.location.href = url.toString().replace(window.location.origin, '');
                            };
                        }
                    }, 50);
                } else {
                    chaveValida = true;
                }
            } catch (e) {
                console.error('Falha ao verificar duplicidade', e);
            }
        };

        numInput.addEventListener('blur', checarDuplicidade);

        form.addEventListener('submit', (ev) => {
            if (!chaveValida) {
                ev.preventDefault();
                numInput.focus();
            }
        });
    };

    const setupPregaoRepeticao = () => {
        const form = document.querySelector('form[action*="pregoes/salvar-repeticao"]');
        if (!form) return;

        applySeiMask(form.querySelector('input[name="processo_sei"]'));

        const repInput = form.querySelector('input[name="id_pregao_repeticao"]');
        if (repInput) {
            repInput.addEventListener('input', () => {
                const v = parseInt(repInput.value || '0', 10);
                repInput.value = v > 0 ? v : '';
            });
        }
    };

    setupPregaoBase();
    setupPregaoRepeticao();
});
