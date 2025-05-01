<style>
    aside {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 60px;
        background-color: #1e3a8a;
        color: white;
        transition: width 0.3s ease;
    }

    aside.expanded {
        width: 250px;
    }

    .menu {
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding: 0 10px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 5px;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .menu-item:hover {
        background-color: #3749b3;
    }

    .menu-item i {
        font-size: 20px;
        margin-right: 10px;
        min-width: 20px;
        text-align: center;
    }

    .menu-item span {
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    aside.expanded .menu-item span {
        opacity: 1;
    }

    .menu-separator {
        border-top: 1px solid #fff;
        margin: 20px 0;
    }

    .logout-btn {
        background: none;
        border: none;
        color: inherit;
        padding: 0;
        cursor: pointer;
    }

    .logout-btn:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        aside.expanded {
            width: 250px;
        }

        .menu-item {
            justify-content: center;
        }

        .menu-item i {
            font-size: 24px;
        }

        .menu-item span {
            opacity: 1;
        }

        aside {
            width: 0;
            overflow: auto;
            z-index: 50;
        }

        aside.expanded {
            width: 250px;
        }

        .menu-item {
            justify-content: center;
        }

        .menu-item span {
            opacity: 1;
        }
    }
</style>
<div x-data="{ sidebarOpen: false }">

    <!-- Botón de menú para móviles -->
    <div class="md:hidden flex items-center justify-between p-4 bg-white shadow">
        <button @click="sidebarOpen = !sidebarOpen" class="text-blue-600 text-2xl">
            <i class="bi bi-list"></i>
        </button>
        <span class="text-lg font-semibold">Mi App</span>
    </div>
    <!-- Botón cerrar (solo en móviles) -->
<div class="md:hidden flex justify-end p-2">
    <button @click="sidebarOpen = false" class="text-white text-xl">
        <i class="bi bi-x-lg"></i>
    </button>
</div>
    <aside id="sidebar" :class="sidebarOpen ? 'w-64' : 'w-0 md:w-16'"
        class="fixed top-0 left-0 h-full bg-blue-900 text-white transition-all duration-300 overflow-x-hidden z-50 md:w-16"
        @click.outside="sidebarOpen = false">
        <div class="menu">
            <div class="menu-item">
                <i class="bi bi-house-door"></i>
                <span>Inicio</span>
            </div>
            @can('ver-conductores')
                <div class="menu-item">
                    <i class="bi bi-people-fill"></i>
                    <a href="{{ route('drives.index') }}"><span>Clientes</span></a>
                </div>
            @endcan
            @can('ver-mecanicos')
                <div class="menu-item">
                    <i class="bi bi-gear"></i>
                    <a href="{{ route('mechanics.index') }}"><span>Mecánicos</span></a>
                </div>
            @endcan
            @can('ver-vehiculos')
                <div class="menu-item">
                    <i class="bi bi-car-front-fill"></i>
                    <a href="{{ route('cars.index') }}"><span>Vehículos</span></a>
                </div>
            @endcan
            @can('ver-trabajadores')
                <div class="menu-item">
                    <i class="bi bi-pencil-square"></i>
                    <a href="{{ route('workers.index') }}"><span>Registro de Trabajadores</span></a>
                </div>
            @endcan
            @can('ver-servicios')
                <div class="menu-item">
                    <i class="bi bi-tools"></i>
                    <a href="{{ route('services.index') }}"><span>Servicios Realizados</span></a>
                </div>
            @endcan
            @can('ver-garantias')
                <div class="menu-item">
                    <i class="bi bi-gear"></i>
                    <a href="{{ route('garantines.index') }}"><span>Garantías</span></a>
                </div>
            @endcan
            @can('ver-productos')
                <div class="menu-item">
                    <i class="bi bi-list-ul"></i>
                    <a href="{{ route('products.index') }}"><span>Productos</span></a>
                </div>
            @endcan
            <div class="menu-item">
                <i class="bi bi-cart"></i>
                <a href="{{ route('sales.index') }}"><span>Ventas</span></a>
            </div>
            <div class="menu-item">
                <i class="bi bi-file-earmark-text"></i>
                <a href="{{ route('quotations.index') }}"><span>Cotizaciones</span></a>
            </div>
            <div class="menu-item">
                <i class="bi bi-people"></i>
                <a href="{{ route('wholesalers.index') }}"><span>Mayoristas</span></a>
            </div>
            <div class="menu-item">
                <i class="bi bi-people"></i>
                <a href="{{ route('buys.index') }}"><span>Compras</span></a>
            </div>
            
            <!-- Separador -->
            <hr class="menu-separator">

            <!-- Perfil y Cerrar sesión -->
            <div class="menu-item">
                <i class="bi bi-person-circle"></i>
                <a href="{{ route('profile.edit') }}"><span>Perfil</span></a>
            </div>
            <div class="menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>
