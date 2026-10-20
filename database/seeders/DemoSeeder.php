<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\Linea;
use App\Models\Oportunidad;
use App\Models\OportunidadLinea;
use App\Models\RevisionOportunidad;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Datos de ejemplo para demostrar el sistema. Solo se ejecuta si no hay clientes.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Cliente::exists()) {
            return;
        }

        Model::unguard();

        $admin = User::where('username', 'admin')->first();

        // --- Rol y usuarios vendedores ---
        $vendedorRol = Role::firstOrCreate(['name' => 'vendedor', 'guard_name' => 'web']);
        $vendedorRol->syncPermissions(
            Permission::where(fn ($q) => $q
                ->where('name', 'like', '%:Cliente')
                ->orWhere('name', 'like', '%:Linea')
                ->orWhere('name', 'like', '%:Oportunidad')
                ->orWhere('name', 'like', '%:Equipo'))
                ->where('name', 'not like', '%Delete%')
                ->get()
        );

        $vendedores = collect([
            ['name' => 'Carlos Mendoza', 'username' => 'cmendoza', 'email' => 'carlos.mendoza@example.com'],
            ['name' => 'Laura Gutiérrez', 'username' => 'lgutierrez', 'email' => 'laura.gutierrez@example.com'],
            ['name' => 'Miguel Ángel Ruiz', 'username' => 'mruiz', 'email' => 'miguel.ruiz@example.com'],
        ])->map(function ($datos) use ($vendedorRol) {
            $user = User::create($datos + ['password' => 'password']);
            $user->assignRole($vendedorRol);

            return $user;
        });

        // --- Catálogo de equipos ---
        $equipos = [
            ['iPhone 16 Pro', '256 GB', '6.3"', '2622 x 1206', '48 MP', 'iOS 18'],
            ['iPhone 15', '128 GB', '6.1"', '2556 x 1179', '48 MP', 'iOS 18'],
            ['Samsung Galaxy S25 Ultra', '512 GB', '6.9"', '3120 x 1440', '200 MP', 'Android 15'],
            ['Samsung Galaxy A55', '256 GB', '6.6"', '2340 x 1080', '50 MP', 'Android 14'],
            ['Motorola Edge 50 Pro', '256 GB', '6.7"', '2712 x 1220', '50 MP', 'Android 14'],
            ['Xiaomi Redmi Note 14 Pro', '256 GB', '6.67"', '2712 x 1220', '200 MP', 'Android 14'],
            ['Google Pixel 9', '128 GB', '6.3"', '2424 x 1080', '50 MP', 'Android 15'],
        ];
        foreach ($equipos as [$nombre, $capacidad, $pantalla, $resolucion, $camara, $so]) {
            Equipo::create([
                'nombre' => $nombre,
                'capacidad' => $capacidad,
                'pantalla' => $pantalla,
                'resolucion' => $resolucion,
                'camara' => $camara,
                'sistema_operativo' => $so,
            ]);
        }

        // --- Clientes empresariales ---
        $clientes = collect([
            ['Grupo Industrial del Norte S.A. de C.V.', 'Roberto Salinas', 'Monterrey, N.L.', 'GIN980312AB1'],
            ['Transportes Rápidos del Bajío', 'Ana Lucía Torres', 'León, Gto.', 'TRB050611KL3'],
            ['Constructora Pacífico', 'Jorge Ramírez', 'Guadalajara, Jal.', 'CPA101120MN7'],
            ['Farmacias San Rafael', 'Patricia Delgado', 'Puebla, Pue.', 'FSR120415QR2'],
            ['Logística Integral MX', 'Fernando Ochoa', 'Querétaro, Qro.', 'LIM150930ST5'],
            ['Hoteles Costa Azul', 'Mariana Vega', 'Cancún, Q.R.', 'HCA090201UV8'],
            ['Despacho Contable Herrera', 'Luis Herrera', 'Ciudad de México', 'DCH170722WX4'],
            ['Agroexportadora del Valle', 'Sofía Navarro', 'Culiacán, Sin.', 'ADV080505YZ6'],
        ])->map(fn ($c, $i) => Cliente::create([
            'nombre' => $c[0],
            'cuenta' => sprintf('CTA-%06d', 100100 + $i * 37),
            'id_cliente' => sprintf('CLI-%04d', 1001 + $i),
            'representante' => $c[1],
            'telefono' => sprintf('55%08d', 41230000 + $i * 1111),
            'correo' => 'contacto'.($i + 1).'@example.com',
            'direccion_fiscal' => "Av. Principal #".(100 + $i * 15).", Col. Centro, {$c[2]}",
            'direccion_entrega' => "Blvd. Industrial #".(200 + $i * 9).", {$c[2]}",
            'direccion_preferencia' => 'fiscal',
            'rfc' => $c[3],
        ]));

        // --- Líneas con vencimientos repartidos (vencidas, 30, 60, 90 días y a futuro) ---
        $planes = ['Empresarial 3GB', 'Empresarial 8GB', 'Empresarial Ilimitado', 'Negocio 12GB', 'Negocio Plus 25GB'];
        $equiposLinea = ['iPhone 13', 'Galaxy A34', 'Moto G84', 'iPhone 14', 'Redmi Note 12'];
        $diasVencimiento = [-45, -10, 5, 18, 27, 40, 52, 58, 70, 85, 120, 200, 365];

        $lineas = collect();
        foreach ($clientes as $ci => $cliente) {
            $vendedor = $vendedores[$ci % $vendedores->count()];
            $cantidad = 2 + ($ci % 3);
            for ($n = 0; $n < $cantidad; $n++) {
                $k = $ci * 3 + $n;
                $lineas->push(Linea::create([
                    'dn' => sprintf('55%08d', 10000000 + $k * 73211),
                    'fecha_fin' => now()->addDays($diasVencimiento[$k % count($diasVencimiento)])->toDateString(),
                    'plan' => $planes[$k % count($planes)],
                    'equipo' => $equiposLinea[$k % count($equiposLinea)],
                    'cliente_id' => $cliente->id,
                    'usuario_id' => $vendedor->id,
                    'asignado_en' => now()->subMonths(6),
                ]));
            }
        }

        // --- Oportunidades en distintas etapas del flujo ---
        $oportunidades = [
            // [cliente, estado, tipo_venta, meses atrás, notas]
            [0, 'pendiente', 'renovacion', 0, 'Cliente solicita renovar líneas vencidas.'],
            [1, 'revision', 'renovacion_anticipada_t1', 0, 'Interesado en equipos de gama alta.'],
            [2, 'haciendo_contratos', 'adicion', 0, 'Agregar líneas para supervisores de obra.'],
            [3, 'captura', 'renovacion_anticipada_t2', 1, null],
            [4, 'verificacion_credito', 'venta_nueva', 1, 'Nueva flotilla de 10 líneas.'],
            [5, 'asignacion_equipo', 'renovacion_anticipada_t1', 1, null],
            [6, 'envios_ruta', 'renovacion', 2, 'Entrega en oficina principal.'],
            [7, 'orden_entregada', 'adicion', 0, null],
            [6, 'orden_entregada', 'renovacion_anticipada_t1', 1, null],
            [0, 'orden_entregada', 'renovacion_anticipada_t3', 3, null],
            [2, 'orden_entregada', 'venta_nueva', 4, null],
            [4, 'aprobada', 'renovacion', 3, null],
            [1, 'rechazada', 'venta_nueva', 2, 'Documentación incompleta.'],
            [5, 'cancelada', 'adicion', 4, 'Cliente decidió posponer la compra.'],
            [3, 'rechazada_credito', 'venta_nueva', 5, null],
        ];

        foreach ($oportunidades as $i => [$ci, $estado, $tipo, $mesesAtras, $notas]) {
            $cliente = $clientes[$ci];
            $fecha = now()->subMonths($mesesAtras)->subDays($i % 7);
            $vendedor = $vendedores[$ci % $vendedores->count()];

            $oportunidad = Oportunidad::create([
                'cliente_id' => $cliente->id,
                'usuario_id' => $vendedor->id,
                'estado' => $estado,
                'tipo_venta' => $tipo,
                'notas' => $notas,
                'direccion_entrega' => $cliente->direccion_entrega,
                'fecha_captura' => in_array($estado, ['pendiente', 'revision', 'haciendo_contratos']) ? null : $fecha->toDateString(),
                'fecha_entrega' => $estado === 'orden_entregada' ? $fecha->copy()->addDays(5)->toDateString() : null,
                'fecha_cancelacion' => $estado === 'cancelada' ? $fecha->copy()->addDays(3)->toDateString() : null,
                'created_at' => $fecha,
                'updated_at' => $fecha->copy()->addDays(5),
            ]);

            foreach ($lineas->where('cliente_id', $cliente->id)->take(2) as $j => $linea) {
                $nuevo = $equipos[($i + $j) % count($equipos)];
                OportunidadLinea::create([
                    'oportunidad_id' => $oportunidad->id,
                    'linea_id' => $linea->id,
                    'fecha_linea' => $linea->fecha_fin,
                    'plan_anterior' => $linea->plan,
                    'plan_nuevo' => $planes[($i + 2) % count($planes)],
                    'equipo_nuevo' => $nuevo[0].' '.$nuevo[1],
                    'sku' => 'SKU-'.strtoupper(substr(md5($nuevo[0].$nuevo[1]), 0, 8)),
                ]);
            }

            // Revisiones de mesa de control
            if (in_array($estado, ['revision', 'captura', 'aprobada', 'orden_entregada'])) {
                RevisionOportunidad::create([
                    'oportunidad_id' => $oportunidad->id,
                    'usuario_id' => $admin?->id ?? $vendedor->id,
                    'estado' => $estado === 'revision' ? 'pendiente' : 'aprobada',
                    'comentarios' => $estado === 'revision' ? 'En revisión de documentos.' : 'Documentación correcta.',
                    'visto_por_vendedor' => $estado !== 'revision',
                    'fecha_aprobacion' => $estado === 'revision' ? null : $fecha->copy()->addDay()->toDateString(),
                ]);
            } elseif (in_array($estado, ['rechazada', 'rechazada_credito'])) {
                RevisionOportunidad::create([
                    'oportunidad_id' => $oportunidad->id,
                    'usuario_id' => $admin?->id ?? $vendedor->id,
                    'estado' => 'rechazada',
                    'comentarios' => $notas ?? 'No aprobó la verificación de crédito.',
                    'visto_por_vendedor' => false,
                    'fecha_rechazo' => $fecha->copy()->addDay()->toDateString(),
                ]);
            }
        }

        Model::reguard();
    }
}
