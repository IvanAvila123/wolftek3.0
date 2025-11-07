<?php


return [
    // Reglas por tipo de venta
    'reglas_por_tipo_venta' => [

        // Regla para Renovación General
        'renovacion' => [
            'descripcion' => 'Renovación General',
            'requiere_dn' => true,
            'requiere_plan_anterior' => true,
            'requiere_plan_nuevo' => true,
            'requiere_equipo_nuevo' => true,
            'requiere_sku' => true,
            'validaciones_fecha' => [
                'vencida' => [
                    'desde' => '-99999999',
                    'hasta' => '0',
                    'mensaje' => 'Línea vencida válida'
                ],
                't1' => ['desde' => '0', 'hasta' => '30', 'mensaje' => 'Línea T1 válida'],
                't2' => ['desde' => '31', 'hasta' => '60', 'mensaje' => 'Línea T2 válida'],
                't3' => ['desde' => '61', 'hasta' => '90', 'mensaje' => 'Línea T3 válida'],
            ],
            'campos_obligatorios' => ['fecha_linea', 'plan_anterior', 'plan_nuevo', 'equipo_nuevo', 'sku']
        ],

        // --- REGLAS AGREGADAS ---
        // Regla para Renovación T1
        'renovacion_anticipada_t1' => [
            'descripcion' => 'Renovación Anticipada T1',
            'requiere_dn' => true,
            'requiere_plan_anterior' => true,
            'requiere_plan_nuevo' => true,
            'requiere_equipo_nuevo' => true,
            'requiere_sku' => true,
            'validaciones_fecha' => [], // La validación real se hace en el código, no aquí
            'campos_obligatorios' => ['fecha_linea', 'plan_anterior', 'plan_nuevo', 'equipo_nuevo', 'sku']
        ],

        // Regla para Renovación T2
        'renovacion_anticipada_t2' => [
            'descripcion' => 'Renovación Anticipada T2',
            'requiere_dn' => true,
            'requiere_plan_anterior' => true,
            'requiere_plan_nuevo' => true,
            'requiere_equipo_nuevo' => true,
            'requiere_sku' => true,
            'validaciones_fecha' => [],
            'campos_obligatorios' => ['fecha_linea', 'plan_anterior', 'plan_nuevo', 'equipo_nuevo', 'sku']
        ],

        // Regla para Renovación T3
        'renovacion_anticipada_t3' => [
            'descripcion' => 'Renovación Anticipada T3',
            'requiere_dn' => true,
            'requiere_plan_anterior' => true,
            'requiere_plan_nuevo' => true,
            'requiere_equipo_nuevo' => true,
            'requiere_sku' => true,
            'validaciones_fecha' => [],
            'campos_obligatorios' => ['fecha_linea', 'plan_anterior', 'plan_nuevo', 'equipo_nuevo', 'sku']
        ],
        // --- FIN DE REGLAS AGREGADAS ---

        'venta_nueva' => [
            'descripcion' => 'Venta nueva - Cliente nuevo',
            'requiere_dn' => false,
            'requiere_plan_anterior' => false,
            'requiere_plan_nuevo' => true,
            'requiere_equipo_nuevo' => true,
            'requiere_sku' => true,
            'validaciones_fecha' => [],
            'campos_obligatorios' => ['plan_nuevo', 'equipo_nuevo', 'sku'],
            'campos_opcionales' => ['fecha_linea', 'plan_anterior'],
            'proceso_especial' => 'nueva_linea',
            'generar_dn_automatico' => true
        ],

        'adicion' => [
            'descripcion' => 'Adición de línea a cuenta existente',
            'requiere_dn' => false,
            'requiere_plan_anterior' => false,
            'requiere_plan_nuevo' => true,
            'requiere_equipo_nuevo' => true,
            'requiere_sku' => true,
            'validaciones_fecha' => [],
            'campos_obligatorios' => ['plan_nuevo', 'equipo_nuevo', 'sku'],
            'campos_opcionales' => ['fecha_linea'],
            'proceso_especial' => 'adicion_linea',
            'generar_dn_automatico' => true,
            'requiere_cuenta_existente' => true
        ],
    ],

    'planes_empresariales' => [

        'ATT ARMALO NEG 239 CTRL',
        'ATT ARMALO NEG 239 CTRL MPP',
        'ATT ARMALO NEG 239 LIBRE',
        'ATT ARMALO NEG 239 LIBRE MPP',

        'ATT ARMALO NEG 299 CTRL',
        'ATT ARMALO NEG 299 CTRL MPP',
        'ATT ARMALO NEG 299 LIBRE',
        'ATT ARMALO NEG 299 LIBRE MPP',

        'ATT ARMALO NEG 399 CTRL',
        'ATT ARMALO NEG 399 CTRL MPP',
        'ATT ARMALO NEG 399 LIBRE',
        'ATT ARMALO NEG 399 LIBRE MPP',

        'ATT ARMALO NEG 499 CTRL',
        'ATT ARMALO NEG 499 CTRL MPP',
        'ATT ARMALO NEG 499 LIBRE',
        'ATT ARMALO NEG 499 LIBRE MPP',

        'ATT ARMALO NEG 599 CTRL',
        'ATT ARMALO NEG 599 CTRL MPP',
        'ATT ARMALO NEG 599 LIBRE',
        'ATT ARMALO NEG 599 LIBRE MPP',

        'ATT ARMALO NEG 699 CTRL',
        'ATT ARMALO NEG 699 CTRL MPP',
        'ATT ARMALO NEG 699 LIBRE',
        'ATT ARMALO NEG 699 LIBRE MPP',

        'ATT ARMALO NEG 799 CTRL',
        'ATT ARMALO NEG 799 CTRL MPP',
        'ATT ARMALO NEG 799 LIBRE',
        'ATT ARMALO NEG 799 LIBRE MPP',

        'ATT ARMALO NEG 899 CTRL',
        'ATT ARMALO NEG 899 CTRL MPP',
        'ATT ARMALO NEG 899 LIBRE',
        'ATT ARMALO NEG 899 LIBRE MPP',

        'ATT ARMALO NEG 999 CTRL',
        'ATT ARMALO NEG 999 CTRL MPP',
        'ATT ARMALO NEG 999 LIBRE',
        'ATT ARMALO NEG 999 LIBRE MPP',

        'ATT ARMALO NEG 1299 CTRL',
        'ATT ARMALO NEG 1299 CTRL MPP',
        'ATT ARMALO NEG 1299 LIBRE',
        'ATT ARMALO NEG 1299 LIBRE MPP',

        'ATT ARMALO NEG 1499 CTRL',
        'ATT ARMALO NEG 1499 CTRL MPP',
        'ATT ARMALO NEG 1499 LIBRE',
        'ATT ARMALO NEG 1499 LIBRE MPP',
    ],

    'tipos_linea_validos' => [
        'renovacion' => ['vencida', 't1', 't2', 't3'],
        'renovacion_t1' => ['t1'],
        'renovacion_t2' => ['t2'],
        'renovacion_t3' => ['t3'],
        'venta_nueva' => [], // No requiere líneas existentes
        'adicion' => [], // No requiere líneas existentes
    ],

    'restricciones' => [
        'lineas_unicas' => true, // No permitir líneas duplicadas
    ],

    'mensajes_ayuda' => [
        'renovacion' => 'Selecciona las líneas vencidas o próximas a vencer que deseas renovar',
        'renovacion_t1' => 'Selecciona las líneas T1 (vencen en 30 días) para renovación urgente',
        'renovacion_t2' => 'Selecciona las líneas T2 (vencen en 31-60 días) para renovación planeada',
        'renovacion_t3' => 'Selecciona las líneas T3 (vencen en 61-90 días) para renovación anticipada',
        'venta_nueva' => 'Para venta nueva se creará una línea completamente nueva',
        'adicion' => 'Para adición se agregará una nueva línea a la cuenta existente',
    ],
]
;
