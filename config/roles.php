<?php
/**
 * Perfis de acesso simples.
 * - EXPANDIR conforme a necessidade de regras de autorização.
 */
return [
    // Nível 1: usuário básico - somente alguns relatórios específicos.
    'BASICO'       => 1,
    // Nível 2: operador - cadastra/edita módulos específicos com restrições.
    'OPERADOR'     => 2,
    // Nível 3: gestor - pode aprovar/alterar status de pregões/amostras.
    'GESTOR'       => 3,
    // Nível 4: administrador funcional - gerencia cadastros de apoio e usuários.
    'ADMIN_FUNC'   => 4,
    // Nível 5: admin do sistema - topo de permissões, exceto apagar logs ou alterar regras centrais.
    'ADMIN_SIST'   => 5,
];
