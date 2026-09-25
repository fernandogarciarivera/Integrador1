@php
    $estados = [
        'REGISTRADO' => ['label' => 'Registrado',   'bg' => 'bg-secondary-container', 'fg' => 'text-on-secondary-container'],
        'PREPARANDO' => ['label' => 'Preparando',   'bg' => 'bg-tertiary-fixed',      'fg' => 'text-on-tertiary-fixed'],
        'LISTO'      => ['label' => 'Listo',        'bg' => 'bg-primary-fixed',       'fg' => 'text-on-primary-fixed'],
        'ENTREGADO'  => ['label' => 'Entregado',    'bg' => 'bg-secondary-container', 'fg' => 'text-on-secondary-container'],
        'CANCELADO'  => ['label' => 'Cancelado',    'bg' => 'bg-error-container',     'fg' => 'text-on-error-container'],
    ];
@endphp

<x-app-layout>
    <div class="caja-page"
         x-data="cajaApp({
            clientesBuscarUrl: '{{ route('clientes.buscar') }}',
            cajaStoreUrl: '{{ route('caja.store') }}',
            cajaEstadoUrlTpl: '{{ route('caja.estado', ['pedido' => '__ID__']) }}',
            csrf: '{{ csrf_token() }}',
            nextOrderNumber: {{ ($metricas['total'] ?? 0) + 1045 }}
         })"
         x-init="init()">

        {{-- Header con métricas de sesión --}}
        <div class="caja-shell">
            @if (session('status'))
                <div class="caja-flash">{{ session('status') }}</div>
            @endif

            <div class="caja-summary">
                <div class="flex flex-col">
                    <div class="flex items-center gap-xs">
                        <span class="material-symbols-outlined text-secondary text-body-md">point_of_sale</span>
                        <span class="text-label-caps font-label-caps text-on-surface-variant uppercase tracking-wider">
                            Sesión activa: {{ $trabajador->local?->nombre ?? 'Caja' }} • {{ $trabajador->puesto ?? 'Caja' }}
                        </span>
                    </div>
                    <h2 class="text-headline-sm font-headline-sm text-on-surface mt-xs">Historial de Pedidos</h2>
                    <p class="text-body-md font-body-md text-on-surface-variant">Consulta y gestiona las transacciones del turno.</p>
                </div>

                <div class="caja-summary__meta">
                    <div class="caja-summary__item">
                        <span class="material-symbols-outlined text-secondary">receipt</span>
                        <div class="flex flex-col">
                            <span class="text-label-caps font-label-caps text-on-surface-variant">Total Pedidos</span>
                            <span class="text-body-md font-body-md font-bold text-on-surface">{{ $metricas['total'] }}</span>
                        </div>
                    </div>
                    <div class="caja-summary__item">
                        <span class="material-symbols-outlined text-primary">payments</span>
                        <div class="flex flex-col">
                            <span class="text-label-caps font-label-caps text-on-surface-variant">Ventas Sesión</span>
                            <span class="text-body-md font-body-md font-bold text-primary">S/ {{ number_format($metricas['ventas'], 2) }}</span>
                        </div>
                    </div>
                    <button type="button"
                            class="caja-new-btn h-12 px-lg rounded-xl bg-primary text-on-primary hover:bg-primary-container font-body-md text-body-md font-bold flex items-center gap-xs shadow-md transition-all active:scale-95"
                            @click="openForm()">
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        <span>Nuevo Pedido</span>
                    </button>
                </div>
            </div>

            {{-- Filtros por estado --}}
            <div class="caja-filter-bar">
                <a href="{{ route('caja.index') }}" class="caja-filter-chip {{ !$estado ? 'is-active' : '' }}">
                    <span>Todos</span>
                    <span class="caja-filter-count">{{ $metricas['total'] }}</span>
                </a>
                @foreach (['REGISTRADO','PREPARANDO','LISTO','ENTREGADO','CANCELADO'] as $e)
                    <a href="{{ route('caja.index', ['estado' => $e]) }}" class="caja-filter-chip {{ $estado === $e ? 'is-active' : '' }}">
                        <span>{{ $estados[$e]['label'] }}</span>
                        <span class="caja-filter-count">{{ $metricas['estados'][$e] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Lista de pedidos --}}
            <div class="caja-order-list" id="order-card-container">
                @forelse ($pedidos as $pedido)
                    @php($meta = $estados[$pedido->estado] ?? $estados['REGISTRADO'])
                    <div class="caja-order-card"
                         data-status="{{ strtolower($pedido->estado) }}"
                         data-pedido-id="{{ $pedido->id }}">
                        <div class="caja-order-card__left">
                            <div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shrink-0 shadow-sm">
                                <span class="material-symbols-outlined text-headline-sm">receipt_long</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-sm flex-wrap">
                                    <span class="text-body-md font-body-md font-bold text-on-surface">#{{ $pedido->codigo_pedido }}</span>
                                    <span class="text-label-caps font-label-caps px-sm py-0.5 rounded-full font-semibold {{ $meta['bg'] }} {{ $meta['fg'] }}">
                                        {{ strtoupper($meta['label']) }}
                                    </span>
                                </div>
                                <span class="text-body-md font-body-md text-on-surface truncate">
                                    {{ $pedido->cliente?->nombre ?? 'Cliente express' }}
                                </span>
                                <span class="text-label-caps font-label-caps text-on-surface-variant mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                                    {{ $pedido->fecha_pedido->format('H:i') }} • {{ $pedido->tipo }}
                                </span>
                            </div>
                        </div>

                        <div class="caja-order-card__right">
                            <div class="text-right">
                                <span class="text-body-md font-body-md font-bold text-primary block">
                                    S/ {{ number_format((float) $pedido->total, 2) }}
                                </span>
                                <span class="text-label-caps font-label-caps text-on-surface-variant">
                                    {{ $pedido->detalle_pedidos()->count() ?? 0 }} ítems
                                </span>
                            </div>

                            <select class="caja-estado-select caja-control !min-h-9 !text-xs"
                                    @change="cambiarEstado({{ $pedido->id }}, $event.target.value, $event)">
                                @foreach (['REGISTRADO','PREPARANDO','LISTO','ENTREGADO','CANCELADO'] as $opt)
                                    <option value="{{ $opt }}" @selected($pedido->estado === $opt)>{{ $estados[$opt]['label'] }}</option>
                                @endforeach
                            </select>

                            <button type="button"
                                    class="w-10 h-10 rounded-xl bg-surface-container-low hover:bg-surface-container-high text-on-surface flex items-center justify-center transition-colors focus:outline-none"
                                    title="Ver detalles"
                                    @click="verDetalle({{ $pedido->id }})">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="caja-empty">No hay pedidos {{ $estado ? 'con ese estado' : 'registrados todavía' }}.</div>
                @endforelse
            </div>
        </div>

        {{-- Panel lateral: Formulario de nuevo pedido --}}
<div class="fixed inset-0 z-50 caja-form-modal"
             x-show="formOpen"
             x-transition.opacity
             style="display: none;">
            <div class="absolute inset-0 bg-black/40" @click="closeForm()"></div>

            <aside class="absolute right-0 top-0 h-full w-full max-w-2xl bg-surface-container-lowest shadow-2xl overflow-y-auto caja-modal-panel"
                   x-show="formOpen"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="translate-x-full">

                <form @submit.prevent="submitForm()" class="caja-modal-panel__body flex flex-col gap-2">
                    <div class="caja-modal-panel__header flex items-center justify-between border-b border-outline-variant pb-2">
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Nuevo Pedido</h2>
                        <button type="button" class="w-10 h-10 rounded-xl bg-surface-container-low hover:bg-surface-container-high flex items-center justify-center" @click="closeForm()">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <template x-if="errors.length">
                        <div class="caja-errors" x-text="errors.join(' · ')"></div>
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                        <div class="flex flex-col gap-xs">
                            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="codigo_pedido">N° de Pedido</label>
                            <input id="codigo_pedido" x-model="form.codigo_pedido" type="text" required
                                   class="w-full px-md py-sm rounded-lg border border-outline-variant bg-surface-container-lowest h-12 font-order-number text-order-number focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div class="flex flex-col gap-xs">
                            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="tipo">Tipo</label>
                            <select id="tipo" x-model="form.tipo" required
                                    class="w-full px-md py-sm rounded-lg border border-outline-variant bg-surface-container-lowest h-12 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="PRESENCIAL">Presencial</option>
                                <option value="PARA_LLEVAR">Para llevar</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-xs md:col-span-2 relative">
                            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="cliente_input">Cliente (opcional)</label>
                            <input id="cliente_input" type="text" autocomplete="off"
                                   x-model="form.cliente_input"
                                   @input.debounce.300ms="buscarClientes()"
                                   @focus="buscarClientes()"
                                   placeholder="Escribe nombre o teléfono"
                                   class="w-full px-md py-sm rounded-lg border border-outline-variant bg-surface-container-lowest h-12 focus:outline-none focus:ring-2 focus:ring-primary">
                            <template x-if="clientesResultados.length">
                                <ul class="absolute top-full left-0 right-0 mt-1 bg-surface-container-lowest border border-outline-variant rounded-lg shadow-lg z-10 max-h-56 overflow-y-auto">
                                    <template x-for="c in clientesResultados" :key="c.id">
                                        <li class="px-md py-sm hover:bg-surface-container-low cursor-pointer text-body-md"
                                            @click="elegirCliente(c)"
                                            x-text="`${c.nombre} · ${c.telefono ?? 's/tel'}`"></li>
                                    </template>
                                </ul>
                            </template>
                        </div>

                        <div class="flex flex-col gap-xs md:col-span-2">
                            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="notas">Notas (opcional)</label>
                            <textarea id="notas" x-model="form.notas" rows="2"
                                      class="w-full px-md py-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                        </div>
                    </div>

                    {{-- Items dinámicos --}}
                    <div class="border-t border-outline-variant pt-md">
                        <div class="flex items-center justify-between mb-sm">
                            <h3 class="font-headline-sm text-body-md font-bold text-on-surface">Ítems del pedido</h3>
                            <button type="button" class="caja-action-btn caja-action-btn--light caja-action-btn--small" @click="addItem()">+ Agregar ítem</button>
                        </div>

                        <div class="flex flex-col gap-sm">
                            <template x-for="(item, idx) in form.items" :key="idx">
                                <div class="grid grid-cols-12 gap-sm items-end bg-surface-container-low p-sm rounded-lg">
                                    <div class="col-span-5 flex flex-col gap-xs">
                                        <label class="font-label-caps text-label-caps text-on-surface-variant">Producto</label>
                                        <input type="text" x-model="item.detalleProducto" required
                                               class="w-full px-sm py-xs rounded border border-outline-variant bg-surface-container-lowest">
                                    </div>
                                    <div class="col-span-2 flex flex-col gap-xs">
                                        <label class="font-label-caps text-label-caps text-on-surface-variant">Cant.</label>
                                        <input type="number" min="1" x-model.number="item.cantidad" required
                                               class="w-full px-sm py-xs rounded border border-outline-variant bg-surface-container-lowest">
                                    </div>
                                    <div class="col-span-3 flex flex-col gap-xs">
                                        <label class="font-label-caps text-label-caps text-on-surface-variant">P. Unit</label>
                                        <input type="number" min="0" step="0.01" x-model.number="item.precio_unitario" required
                                               class="w-full px-sm py-xs rounded border border-outline-variant bg-surface-container-lowest">
                                    </div>
                                    <div class="col-span-2 flex flex-col gap-xs">
                                        <label class="font-label-caps text-label-caps text-on-surface-variant">Subtotal</label>
                                        <span class="text-body-md font-bold text-primary"
                                              x-text="'S/ ' + (item.cantidad * item.precio_unitario).toFixed(2)"></span>
                                        <button type="button" class="caja-action-btn caja-action-btn--danger caja-action-btn--small" @click="removeItem(idx)" x-show="form.items.length > 1">Quitar</button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-between items-end mt-md border-t border-outline-variant pt-md">
                            <span class="font-body-md text-body-md text-on-surface-variant">Total</span>
                            <span class="font-display-lg text-display-lg text-primary" x-text="'S/ ' + totalCalculado()"></span>
                        </div>
                    </div>

                    <div class="caja-modal-panel__footer flex gap-2 justify-end pt-2 border-t border-outline-variant">
                        <button type="button" class="caja-action-btn caja-action-btn--light" @click="closeForm()">Cancelar</button>
                        <button type="submit" class="caja-action-btn" :disabled="saving">
                            <span x-show="!saving">Registrar y generar QR</span>
                            <span x-show="saving">Guardando…</span>
                        </button>
                    </div>
                </form>
            </aside>
        </div>

        {{-- Modal QR --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center caja-qr-modal"
             x-show="qrOpen"
             x-transition.opacity
             style="display: none;">
            <div class="absolute inset-0 bg-black/50" @click="closeQr()"></div>
            <div class="relative bg-surface-container-lowest rounded-2xl max-w-sm w-full mx-md text-center shadow-2xl caja-modal-panel"
                 x-show="qrOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="caja-modal-panel__body">
                    <h3 class="font-headline-sm text-headline-sm text-primary mb-xs">
                        Orden #<span x-text="qrData.codigo"></span> registrada
                    </h3>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-md">
                        Muestra este código al cliente para completar el pago.
                    </p>
                    <div class="bg-surface-container p-md rounded-2xl mb-lg border border-outline-variant inline-block">
                        <img :src="qrUrl()" alt="Código QR" class="w-64 h-64 object-contain rounded-lg bg-white">
                    </div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant mb-md">
                        Expira: <span x-text="qrData.expira"></span>
                    </p>
                </div>
                <div class="caja-modal-panel__footer flex gap-2">
                    <button type="button" class="caja-action-btn caja-action-btn--light flex-1" @click="resetForm()">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                        Nueva orden
                    </button>
                    <a :href="'{{ route('caja.index') }}'" class="caja-action-btn flex-1">
                        <span class="material-symbols-outlined text-[18px]">list</span>
                        Ver listado
                    </a>
                </div>
            </div>
        </div>

        {{-- Modal detalle pedido --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center caja-detail-modal"
             x-show="detailOpen"
             x-transition.opacity
             style="display: none;">
            <div class="absolute inset-0 bg-black/50" @click="detailOpen = false"></div>
            <div class="relative bg-surface-container-lowest max-w-lg w-full mx-md shadow-2xl max-h-[80vh] overflow-y-auto caja-modal-panel"
                 x-show="detailOpen">
                <div class="caja-modal-panel__header flex justify-between items-center border-b border-outline-variant">
                    <h3 class="font-headline-sm text-headline-sm">Detalle del pedido</h3>
                    <button type="button" class="w-8 h-8 rounded-lg hover:bg-surface-container-high" @click="detailOpen = false">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="caja-modal-panel__body">
                    <template x-if="detail">
                        <div class="flex flex-col gap-sm">
                            <p class="text-body-md"><strong x-text="'#' + detail.codigo_pedido"></strong></p>
                            <p class="text-body-md text-on-surface-variant" x-text="'Estado: ' + detail.estado"></p>
                            <p class="text-body-md text-on-surface-variant" x-text="'Total: S/ ' + Number(detail.total ?? 0).toFixed(2)"></p>
                            <div class="border-t border-outline-variant pt-sm">
                                <h4 class="font-bold mb-xs">Ítems</h4>
                                <template x-for="d in (detail.detalle_pedidos || [])" :key="d.id">
                                    <div class="flex justify-between text-body-md py-1">
                                        <span x-text="`${d.cantidad}× ${d.detalleProducto}`"></span>
                                        <span x-text="'S/ ' + Number(d.subtotal).toFixed(2)"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="border-t border-outline-variant pt-sm">
                                <h4 class="font-bold mb-xs">Historial</h4>
                                <template x-for="h in (detail.historial_estados || [])" :key="h.id">
                                    <div class="text-label-caps text-on-surface-variant py-0.5">
                                        <span x-text="h.estado_anterior ? h.estado_anterior + ' → ' + h.estado_nuevo : 'INICIO → ' + h.estado_nuevo"></span>
                                        <span x-text="' · ' + new Date(h.fecha_cambio).toLocaleString()"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function cajaApp(config) {
        return {
            // Estado formulario
            formOpen: false,
            saving: false,
            errors: [],
            form: {
                codigo_pedido: String(config.nextOrderNumber),
                tipo: 'PRESENCIAL',
                cliente_id: null,
                cliente_input: '',
                notas: '',
                items: [{ detalleProducto: '', cantidad: 1, precio_unitario: 0, instrucciones_especiales: '' }],
            },
            clientesResultados: [],

            // Estado QR
            qrOpen: false,
            qrData: { codigo: '', qr: '', expira: '', total: 0 },

            // Estado detalle
            detailOpen: false,
            detail: null,

            init() {},

            // ---------- Formulario ----------
            openForm() {
                this.errors = [];
                this.formOpen = true;
            },
            closeForm() {
                this.formOpen = false;
            },
            addItem() {
                this.form.items.push({ detalleProducto: '', cantidad: 1, precio_unitario: 0, instrucciones_especiales: '' });
            },
            removeItem(idx) {
                this.form.items.splice(idx, 1);
            },
            totalCalculado() {
                const t = this.form.items.reduce((s, i) => s + (Number(i.cantidad) * Number(i.precio_unitario)), 0);
                return t.toFixed(2);
            },

            // ---------- Clientes ----------
            async buscarClientes() {
                const q = (this.form.cliente_input || '').trim();
                if (q.length < 2) { this.clientesResultados = []; return; }
                try {
                    const r = await fetch(`${config.clientesBuscarUrl}?q=${encodeURIComponent(q)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    this.clientesResultados = r.ok ? await r.json() : [];
                } catch { this.clientesResultados = []; }
            },
            elegirCliente(c) {
                this.form.cliente_id = c.id;
                this.form.cliente_input = c.nombre;
                this.clientesResultados = [];
            },

            // ---------- Submit ----------
            async submitForm() {
                this.saving = true;
                this.errors = [];
                const payload = {
                    cliente_id: this.form.cliente_id,
                    codigo_pedido: this.form.codigo_pedido,
                    tipo: this.form.tipo,
                    notas: this.form.notas,
                    items: this.form.items,
                };
                try {
                    const r = await fetch(config.cajaStoreUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': config.csrf,
                        },
                        body: JSON.stringify(payload),
                    });
                    const body = await r.json();
                    if (!r.ok) {
                        this.errors = Object.values(body.errors ?? {}).flat();
                        if (!this.errors.length) this.errors = [body.message ?? 'Error al registrar'];
                        return;
                    }
                    this.qrData = {
                        codigo: body.codigo,
                        qr: body.qr,
                        expira: new Date(body.expira).toLocaleString(),
                        total: body.total,
                    };
                    this.formOpen = false;
                    this.qrOpen = true;
                } catch (e) {
                    this.errors = ['Error de red: ' + e.message];
                } finally {
                    this.saving = false;
                }
            },

            // ---------- QR ----------
            qrUrl() {
                const data = encodeURIComponent(this.qrData.qr || '');
                return `https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=${data}`;
            },
            closeQr() { this.qrOpen = false; },
            resetForm() {
                this.qrOpen = false;
                this.form = {
                    codigo_pedido: String(Number(this.form.codigo_pedido) + 1),
                    tipo: 'PRESENCIAL',
                    cliente_id: null,
                    cliente_input: '',
                    notas: '',
                    items: [{ detalleProducto: '', cantidad: 1, precio_unitario: 0, instrucciones_especiales: '' }],
                };
                setTimeout(() => window.location.reload(), 200);
            },

            // ---------- Cambio de estado ----------
            async cambiarEstado(pedidoId, nuevoEstado, evt) {
                const url = config.cajaEstadoUrlTpl.replace('__ID__', pedidoId);
                try {
                    const r = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': config.csrf,
                        },
                        body: JSON.stringify({ estado: nuevoEstado, _method: 'PATCH' }),
                    });
                    if (!r.ok) {
                        const b = await r.json().catch(() => ({}));
                        alert(b.message ?? 'No se pudo cambiar el estado');
                        return;
                    }
                    window.location.reload();
                } catch (e) {
                    alert('Error de red al cambiar estado');
                }
            },

            // ---------- Detalle ----------
            async verDetalle(pedidoId) {
                try {
                    const r = await fetch(`/caja/${pedidoId}/detalle`, { headers: { 'Accept': 'application/json' } });
                    if (!r.ok) return;
                    this.detail = await r.json();
                    this.detailOpen = true;
                } catch {}
            },
        };
    }
</script>