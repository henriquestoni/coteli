(function() {
    // Handler para links "NÃO DESIGNADO" na agenda, abrindo edição do pregão/repetição
    const onClick = (ev) => {
        const link = ev.target.closest('.tag-alerta');
        if (!link) return;
        ev.preventDefault();
        const idBase = link.dataset.id;
        if (!idBase) return;
        // Redireciona para edição do pregão base/repetição
        window.location.href = `<?= url('pregoes/editar/') ?>${idBase}`;
    };
    document.addEventListener('click', onClick);
})();
