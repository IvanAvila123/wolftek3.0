<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\Login;
use App\Filament\Admin\Pages\CatalogoEquipos;
use App\Filament\Admin\Pages\Equipo;
use App\Filament\Admin\Pages\Renovaciones;
use App\Filament\Admin\Resources\Equipos\Pages\Equipos;
use App\Filament\Admin\Widgets\EstadisticasVentas;
use App\Filament\Admin\Widgets\UltimasVentasTable;
use App\Filament\Admin\Widgets\VentasMensualesChart;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;
use Termwind\Components\Ul;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class, LoginResponse::class)
            ->topNavigation()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->pages([
                Dashboard::class,
                Renovaciones::class,
                CatalogoEquipos::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                EstadisticasVentas::class,
                VentasMensualesChart::class,
                UltimasVentasTable::class,
            ])
            ->databaseNotifications()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,

            ])
        ->plugins([
            SimpleLightBoxPlugin::make(),
            FilamentShieldPlugin::make()
        ]);
    }
}
