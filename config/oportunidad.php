<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Oportunidad Settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for the Oportunidad module.
    | These settings include default values and options for managing
    | opportunities within the application.
    |
    */

    'estados' => [
        'pendiente' => 'Pendiente',
        'revision' => 'Revision',
        'haciendo_contratos' => 'Haciendo contratos',
        'entrega_contratos' => 'Entrega de contratos',
        'revision_contratos' => 'Revision de contratos',
        'captura' => 'Captura',
        'verificacion_credito' => 'Verificacion de credito',
        'rechazada_credito' => 'Rechazada por credito',
        'verificacion_rechazada' => 'Verificacion de credito rechazada',
        'verificacion_aprobada' => 'Verificacion de credito aprobada',
        'asignacion_equipo' => 'Asignacion de equipo',
        'cancela_envios' => 'Cancela/envios',
        'envios_confirmar' => 'Envios/por confirmar',
        'envios_ruta' => 'Envios/en ruta',
        'orden_entregada' => 'Orden entregada',
        'aprobada' => 'Aprobada',
        'rechazada' => 'Rechazada',
        'cancelada' => 'Cancelada',
    ],

    'tipos_venta' => [
        'renovacion' => 'Renovacion Vencida',
        'renovacion_anticipada_t1' => 'Renovacion Anticipada T1',
        'renovacion_anticipada_t2' => 'Renovacion Anticipada T2',
        'renovacion_anticipada_t3' => 'Renovacion Anticipada T3',
        'adicion' => 'Adicion',
        'venta_nueva'=> 'Venta Nueva',
    ],

    'estados_no_editables' => [
        'cancelada',
        'aprobada', // Una vez aprobada, solo roles altos pueden editar
    ],

    'estados_reeditable_roles_menores' => [
        'rechazada_credito',
        'rechazada',
    ],

    'jerarquia_roles' => [
        'vendedor' => 1,
        'supervisor' => 2,
        'capturista' => 3,
        'gerente' => 4,
        'director' => 5,
        'admin' => 6,
    ],

    'roles_alto_rango' => [
        'gerente',
        'director',
        'admin'
    ],

    'permisos_acciones' => [
        'puede_aprobar' => [
            'supervisor',
            'gerente',
            'director',
            'admin'
        ],
        'puede_rechazar' => [
            'supervisor',
            'gerente',
            'director',
            'admin'
        ],
        'puede_cancelar' => [
            'vendedor',
            'supervisor',
            'gerente',
            'director',
            'admin'
        ],
        'puede_reabrir' => [
            'gerente',
            'director',
            'admin'
        ],
    ],

    'evidencias_config' => [
        'max_files' => 5,
        'max_file_size' => 10 * 1024, // 10MB en KB
        'allowed_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'storage_path' => 'oportunidades/rechazos',
    ],

];
?>
