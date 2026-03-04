<?php

return [
    'module' => [
        'title' => 'Ativos',
        'subtitle' => 'Gestao patrimonial',
        'menu' => 'Ativos',
    ],
    'actions' => [
        'view' => 'Visualizar',
        'create' => 'Criar',
        'edit' => 'Editar',
        'save' => 'Salvar',
        'back' => 'Voltar',
        'cancel' => 'Cancelar',
        'receive_stock' => 'Receber em estoque',
        'release' => 'Liberar',
        'transfer' => 'Transferir',
        'audit' => 'Auditar',
        'change_state' => 'Alterar estado',
        'return_to_patrimony' => 'Retornar ao patrimonio',
        'reports' => 'Relatorios',
    ],
    'invoices' => [
        'index' => [
            'title' => 'Notas de ativos',
            'subtitle' => 'Gerencie notas fiscais vinculadas ao patrimonio',
            'filters_title' => 'Filtros de notas',
            'empty' => 'Nenhuma nota fiscal encontrada.',
        ],
        'form' => [
            'create_title' => 'Nova nota fiscal',
            'create_subtitle' => 'Cadastre uma nota para entrada de ativos',
            'edit_title' => 'Editar nota fiscal',
            'edit_subtitle' => 'Ajuste os dados da nota cadastrada',
        ],
        'show' => [
            'title' => 'Nota :number',
            'subtitle' => 'Consulte os dados e itens da nota fiscal',
            'summary_title' => 'Resumo da nota',
        ],
        'table' => [
            'invoice' => 'Nota',
            'supplier' => 'Fornecedor',
            'issue_date' => 'Emissao',
            'items' => 'Itens',
            'actions' => 'Acoes',
        ],
        'fields' => [
            'search' => 'Busca',
            'per_page' => 'Itens por pagina',
            'invoice_number' => 'Numero da nota',
            'invoice_series' => 'Serie',
            'supplier_name' => 'Fornecedor',
            'supplier_document' => 'Documento',
            'issue_date' => 'Data de emissao',
            'received_date' => 'Data de recebimento',
            'total_amount' => 'Valor total',
            'notes' => 'Observacoes',
        ],
        'placeholders' => [
            'search' => 'Busque por numero da nota ou fornecedor',
            'per_page' => 'Selecione a quantidade',
        ],
        'labels' => [
            'total' => 'Total',
        ],
        'actions' => [
            'new' => 'Nova nota',
            'assets' => 'Ativos',
            'view_assets' => 'Ver ativos da nota',
        ],
        'messages' => [
            'created' => 'Nota fiscal cadastrada com sucesso.',
            'updated' => 'Nota fiscal atualizada com sucesso.',
        ],
        'items' => [
            'empty' => 'Nenhum item cadastrado para esta nota.',
            'create_title' => 'Novo item da nota',
            'edit_title' => 'Editar item da nota',
            'table' => [
                'description' => 'Item',
                'quantity' => 'Qtd',
                'total_price' => 'Valor total',
                'assets_created' => 'Ativos gerados',
            ],
            'fields' => [
                'item_code' => 'Codigo do item',
                'description' => 'Descricao',
                'quantity' => 'Quantidade',
                'unit_price' => 'Valor unitario',
                'total_price' => 'Valor total',
                'brand' => 'Marca',
                'model' => 'Modelo',
            ],
            'actions' => [
                'new' => 'Novo item',
                'delete' => 'Excluir item',
                'view_assets' => 'Ver ativos deste item',
            ],
            'messages' => [
                'saved' => 'Item da nota salvo com sucesso.',
                'deleted' => 'Item da nota removido com sucesso.',
            ],
        ],
        'receive_stock' => [
            'action' => 'Receber',
            'title' => 'Receber em estoque',
            'submit' => 'Confirmar entrada',
            'remaining' => 'Saldo disponivel para este item: :remaining unidade(s).',
            'remaining_short' => 'Saldo: :remaining',
            'summary' => 'Recebido: :received/:total | Saldo: :remaining',
            'fields' => [
                'quantity' => 'Quantidade',
                'acquired_date' => 'Data de aquisicao',
                'description' => 'Descricao',
                'brand' => 'Marca',
                'model' => 'Modelo',
            ],
            'messages' => [
                'success' => ':count ativo(s) gerado(s) no estoque com sucesso.',
            ],
        ],
    ],
    'filters' => [
        'all' => 'Todos',
        'all_units' => 'Todas as unidades',
        'all_sectors' => 'Todos os setores',
    ],
    'assets_index' => [
        'title' => 'Ativos',
        'subtitle' => 'Consulte o patrimonio por estado, unidade e setor',
        'filters_title' => 'Filtros de ativos',
        'invoice_scope_notice' => 'A lista esta filtrada pelos ativos vinculados a uma nota fiscal/item especifico.',
        'empty' => 'Nenhum ativo encontrado.',
        'fields' => [
            'search' => 'Busca',
            'state' => 'Estado',
            'unit' => 'Unidade',
            'sector' => 'Setor',
            'per_page' => 'Itens por pagina',
        ],
        'placeholders' => [
            'search' => 'Busque por codigo, descricao ou numeros do ativo',
        ],
        'table' => [
            'asset' => 'Ativo',
            'location' => 'Localizacao',
            'invoice' => 'Origem',
            'timeline' => 'Eventos',
            'actions' => 'Acoes',
        ],
        'labels' => [
            'no_unit' => 'Sem unidade',
            'no_sector' => 'Sem setor',
        ],
        'actions' => [
            'invoices' => 'Notas fiscais',
            'reports' => 'Relatorios',
            'clear_invoice_filter' => 'Limpar filtro da nota',
        ],
    ],
    'asset_show' => [
        'title' => 'Ativo :code',
        'subtitle' => 'Acompanhe dados cadastrais e historico operacional',
        'summary_title' => 'Resumo do ativo',
        'timeline_title' => 'Linha do tempo',
        'timeline_count' => ':count evento(s) registrados',
        'no_events' => 'Nenhum evento registrado para este ativo.',
        'fields' => [
            'description' => 'Descricao',
            'state' => 'Estado',
            'unit' => 'Unidade',
            'sector' => 'Setor',
            'serial_number' => 'Numero de serie',
            'patrimony_number' => 'Numero patrimonial',
            'invoice' => 'Nota fiscal',
        ],
        'labels' => [
            'system' => 'Sistema',
            'unit_change' => 'Unidade: :from -> :to',
            'sector_change' => 'Setor: :from -> :to',
            'state_change' => 'Estado: :from -> :to',
            'view_photo' => 'Ver foto',
        ],
        'actions' => [
            'load_more' => 'Carregar mais eventos',
        ],
    ],
    'operations' => [
        'fields' => [
            'unit' => 'Unidade de destino',
            'sector' => 'Setor de destino',
            'notes' => 'Observacoes',
        ],
        'placeholders' => [
            'optional_sector' => 'Setor opcional',
        ],
        'release' => [
            'action' => 'Liberar ativo',
            'title' => 'Liberar ativo',
            'submit' => 'Confirmar liberacao',
            'messages' => [
                'success' => 'Ativo liberado com sucesso.',
            ],
        ],
        'transfer' => [
            'action' => 'Transferir',
            'title' => 'Transferir ativo',
            'submit' => 'Confirmar transferencia',
            'messages' => [
                'success' => 'Ativo transferido com sucesso.',
            ],
        ],
        'change_state' => [
            'action' => 'Alterar estado',
            'title' => 'Alterar estado do ativo',
            'submit' => 'Salvar estado',
            'fields' => [
                'to_state' => 'Novo estado',
            ],
            'placeholders' => [
                'select_state' => 'Selecione o estado',
            ],
            'messages' => [
                'success' => 'Estado do ativo atualizado com sucesso.',
            ],
        ],
        'return' => [
            'action' => 'Retornar ao patrimonio',
            'title' => 'Retornar ao patrimonio',
            'description' => 'Essa acao remove o setor atual e envia o ativo para a unidade patrimonial configurada.',
            'submit' => 'Confirmar retorno',
            'messages' => [
                'success' => 'Ativo retornado ao patrimonio com sucesso.',
            ],
        ],
    ],
    'audit_mobile' => [
        'title' => 'Auditoria mobile',
        'subtitle' => 'Busque o ativo, capture a foto e registre a auditoria',
        'fields' => [
            'search_code' => 'Codigo do ativo',
            'photo' => 'Foto da auditoria',
            'notes' => 'Observacoes',
        ],
        'placeholders' => [
            'search_code' => 'Digite ou escaneie o codigo do ativo',
        ],
        'actions' => [
            'open' => 'Auditoria mobile',
            'search' => 'Buscar ativo',
            'submit' => 'Registrar auditoria',
        ],
        'messages' => [
            'not_found' => 'Nenhum ativo encontrado com o codigo informado.',
            'success' => 'Auditoria registrada com sucesso.',
        ],
    ],
    'reports' => [
        'empty' => 'Nenhum dado encontrado para os filtros informados.',
        'filters_title' => 'Filtros do relatorio',
        'actions' => [
            'export_csv' => 'Exportar CSV',
        ],
        'fields' => [
            'start_date' => 'Data inicial',
            'end_date' => 'Data final',
        ],
        'assets_by_unit' => [
            'title' => 'Relatorio de ativos por unidade',
            'subtitle' => 'Distribuicao patrimonial consolidada por unidade',
            'table' => [
                'unit' => 'Unidade',
                'total' => 'Total',
            ],
        ],
        'assets_by_state' => [
            'title' => 'Relatorio de ativos por estado',
            'subtitle' => 'Consolidado atual por estado patrimonial',
            'table' => [
                'state' => 'Estado',
                'total' => 'Total',
            ],
        ],
        'transfers_by_period' => [
            'title' => 'Transferencias por periodo',
            'subtitle' => 'Volume de transferencias registradas no periodo',
            'table' => [
                'date' => 'Data',
                'total' => 'Transferencias',
            ],
        ],
        'audits_by_period' => [
            'title' => 'Auditorias por periodo',
            'subtitle' => 'Volume de auditorias registradas no periodo',
            'table' => [
                'date' => 'Data',
                'total' => 'Auditorias',
            ],
        ],
        'purchases_by_period' => [
            'title' => 'Compras por periodo',
            'subtitle' => 'Notas fiscais emitidas no periodo informado',
            'table' => [
                'invoice' => 'Nota',
                'issue_date' => 'Emissao',
                'supplier' => 'Fornecedor',
                'items' => 'Itens',
                'total' => 'Valor',
            ],
        ],
    ],
    'events' => [
        'STOCK_RECEIVED' => 'Entrada em estoque',
        'RELEASED' => 'Ativo liberado',
        'IN_USE' => 'Ativo em uso',
        'MAINTENANCE' => 'Ativo em manutencao',
        'DAMAGED' => 'Ativo danificado',
        'RETURNED_TO_PATRIMONY' => 'Retorno ao patrimonio',
        'TRANSFERRED' => 'Transferencia registrada',
        'AUDITED' => 'Auditoria executada',
        'STATE_CHANGED' => 'Estado alterado',
    ],
    'states' => [
        'in_stock' => 'Em estoque',
        'released' => 'Liberado',
        'in_use' => 'Em uso',
        'maintenance' => 'Em manutencao',
        'damaged' => 'Danificado',
        'returned_to_patrimony' => 'Retornado ao patrimonio',
    ],
];
