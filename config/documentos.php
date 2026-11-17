<?php

return [
    'renovacion' => [
        ['tipo' => 'INE', 'obligatorio' => true],
        ['tipo' => 'OP', 'obligatorio' => true],
        ['tipo' => 'OC', 'obligatorio' => true],
        ['tipo' => 'FORMATO SAE', 'obligatorio' => true],
        ['tipo' => 'FOLIO DE AUTORIZACION', 'obligatorio' => true],
        ['tipo' => 'ACTA CONSTITUTIVA', 'obligatorio' => 'condicional'], // solo si aplica
        // Opcionales - disponibles pero no obligatorios
        ['tipo' => 'RFC', 'obligatorio' => false],
        ['tipo' => 'COMPROBANTE DE DOMICILIO', 'obligatorio' => false],
        ['tipo' => 'DOCUMENTO ADICIONAL', 'obligatorio' => false],
    ],

    'renovacion_anticipada_t1' => [
        ['tipo' => 'INE', 'obligatorio' => true],
        ['tipo' => 'OP', 'obligatorio' => true],
        ['tipo' => 'OC', 'obligatorio' => true],
        ['tipo' => 'FORMATO SAE', 'obligatorio' => true],
        ['tipo' => 'FOLIO DE AUTORIZACION', 'obligatorio' => true],
        ['tipo' => 'ACTA CONSTITUTIVA', 'obligatorio' => 'condicional'],
        // Opcionales
        ['tipo' => 'RFC', 'obligatorio' => false],
        ['tipo' => 'COMPROBANTE DE DOMICILIO', 'obligatorio' => false],
        ['tipo' => 'DOCUMENTO ADICIONAL', 'obligatorio' => false],
    ],

    'renovacion_anticipada_t2' => [
        ['tipo' => 'INE', 'obligatorio' => true],
        ['tipo' => 'OP', 'obligatorio' => true],
        ['tipo' => 'OC', 'obligatorio' => true],
        ['tipo' => 'FORMATO SAE', 'obligatorio' => true],
        ['tipo' => 'FOLIO DE AUTORIZACION', 'obligatorio' => true],
        ['tipo' => 'ACTA CONSTITUTIVA', 'obligatorio' => 'condicional'],
        // Opcionales
        ['tipo' => 'RFC', 'obligatorio' => false],
        ['tipo' => 'COMPROBANTE DE DOMICILIO', 'obligatorio' => false],
        ['tipo' => 'DOCUMENTO ADICIONAL', 'obligatorio' => false],
    ],

    'renovacion_anticipada_t3' => [
        ['tipo' => 'INE', 'obligatorio' => true],
        ['tipo' => 'OP', 'obligatorio' => true],
        ['tipo' => 'OC', 'obligatorio' => true],
        ['tipo' => 'FORMATO SAE', 'obligatorio' => true],
        ['tipo' => 'FOLIO DE AUTORIZACION', 'obligatorio' => true],
        ['tipo' => 'ACTA CONSTITUTIVA', 'obligatorio' => 'condicional'],
        // Opcionales
        ['tipo' => 'RFC', 'obligatorio' => false],
        ['tipo' => 'COMPROBANTE DE DOMICILIO', 'obligatorio' => false],
        ['tipo' => 'DOCUMENTO ADICIONAL', 'obligatorio' => false],
    ],

    'venta_nueva' => [
        ['tipo' => 'INE', 'obligatorio' => true],
        ['tipo' => 'CARTA BURO', 'obligatorio' => true],
        ['tipo' => 'COMPROBANTE DE DOMICILIO', 'obligatorio' => true],
        ['tipo' => 'RFC', 'obligatorio' => true],
        ['tipo' => 'ACTA CONSTITUTIVA', 'obligatorio' => true],
        ['tipo' => 'ESTADO DE CUENTA', 'obligatorio' => true],
        ['tipo' => 'FORMATO SAE', 'obligatorio' => true],
        ['tipo' => 'FOLIO DE AUTORIZACION', 'obligatorio' => true],
        ['tipo' => 'OP', 'obligatorio' => true],
        ['tipo' => 'OC', 'obligatorio' => true],
        ['tipo' => 'FORMATO DE ACCIONISTAS', 'obligatorio' => true],
        ['tipo' => 'DOCUMENTO ADICIONAL', 'obligatorio' => 'condicional'],
    ],

    'adicion' => [
        ['tipo' => 'INE', 'obligatorio' => true],
        ['tipo' => 'OP', 'obligatorio' => true],
        ['tipo' => 'OC', 'obligatorio' => true],
        ['tipo' => 'FORMATO SAE', 'obligatorio' => true],
        ['tipo' => 'FOLIO DE AUTORIZACION', 'obligatorio' => true],
        // Opcionales
        ['tipo' => 'RFC', 'obligatorio' => false],
        ['tipo' => 'COMPROBANTE DE DOMICILIO', 'obligatorio' => false],
        ['tipo' => 'DOCUMENTO ADICIONAL', 'obligatorio' => false],
    ],

    'reporte' => [
        ['tipo' => 'motivo_rechazo', 'obligatorio' => 'condicional'],
    ],
];
