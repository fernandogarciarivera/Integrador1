@php($editing = $trabajador->exists)
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ $editing ? 'Editar trabajador' : 'Nuevo trabajador' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-6">

            {{-- Formulario --}}
            <div class="md:col-span-8 bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Datos personales y acceso</h3>

                <form method="POST"
                      action="{{ $editing ? route('trabajadores.update', $trabajador) : route('trabajadores.store') }}"
                      class="space-y-4">
                    @csrf
                    @if ($editing) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="nombre_completo" value="Nombre completo" />
                            <x-text-input id="nombre_completo" name="nombre_completo" type="text" class="mt-1 block w-full"
                                :value="old('nombre_completo', $trabajador->user->name ?? '')" required />
                            <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email / Usuario" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $trabajador->user->email ?? '')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="restaurante_id" value="Restaurante" />
                            <x-text-input id="restaurante_id" name="restaurante_id" type="text"
                                list="dl-restaurantes" autocomplete="off"
                                class="mt-1 block w-full"
                                :value="old('restaurante_id', $trabajador->restaurante_id ?? '')" required />
                            <datalist id="dl-restaurantes">
                                @foreach ($restaurantes as $r)
                                    <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                @endforeach
                            </datalist>
                            <p class="text-xs text-gray-500 mt-1">Empieza a escribir el nombre.</p>
                            <x-input-error :messages="$errors->get('restaurante_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="local_id" value="Local" />
                            <x-text-input id="local_id" name="local_id" type="text"
                                list="dl-locales" autocomplete="off"
                                class="mt-1 block w-full"
                                :value="old('local_id', $trabajador->local_id ?? '')" required />
                            <datalist id="dl-locales">
                                @foreach ($locales as $l)
                                    <option value="{{ $l->id }}">{{ $l->nombre }}</option>
                                @endforeach
                            </datalist>
                            <x-input-error :messages="$errors->get('local_id')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="rol" value="Rol" />
                            <x-text-input id="rol" name="rol" type="text"
                                list="dl-perfiles" autocomplete="off"
                                class="mt-1 block w-full"
                                :value="old('rol', $trabajador->rol ?? '')" required />
                            <datalist id="dl-perfiles">
                                @foreach ($perfiles as $p)
                                    <option value="{{ $p->perfil }}"></option>
                                @endforeach
                            </datalist>
                            <x-input-error :messages="$errors->get('rol')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="telefono" value="Teléfono (opcional)" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full"
                                :value="old('telefono', $trabajador->telefono ?? '')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="puesto" value="Puesto (opcional)" />
                        <x-text-input id="puesto" name="puesto" type="text" class="mt-1 block w-full"
                            :value="old('puesto', $trabajador->puesto ?? '')" />
                    </div>

                    <div class="pt-4 border-t flex justify-end gap-2">
                        <a href="{{ route('trabajadores.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm">Cancelar</a>
                        <x-primary-button>{{ $editing ? 'Guardar cambios' : 'Crear trabajador' }}</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Panel lateral --}}
            <div class="md:col-span-4 bg-white shadow-sm rounded-lg p-6">
                <div class="flex flex-col items-center mb-4">
                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center mb-2">
                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 10a4 4 0 100-8 4 4 0 000 8zm0 2c-4 0-8 2-8 5v1h16v-1c0-3-4-5-8-5z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-medium text-gray-900">
                        {{ $trabajador->user->name ?? 'Nuevo trabajador' }}
                    </h3>
                    @if ($editing)
                        <span class="mt-1 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ $trabajador->rol }}</span>
                    @endif
                </div>

                @if ($editing)
                    <div class="pt-4 border-t space-y-3">
                        <form method="POST" action="{{ route('trabajadores.reset-password', $trabajador) }}">
                            @csrf
                            <button class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                                    onclick="return confirm('¿Resetear contraseña a 12345678?')">
                                Resetear contraseña
                            </button>
                        </form>
                        <form method="POST" action="{{ route('trabajadores.toggle-activo', $trabajador) }}">
                            @csrf
                            <button class="w-full px-4 py-2 rounded-md text-sm border
                                    {{ $trabajador->activo
                                        ? 'border-red-300 text-red-600 hover:bg-red-50'
                                        : 'border-green-300 text-green-600 hover:bg-green-50' }}"
                                    onclick="return confirm('¿{{ $trabajador->activo ? 'Desactivar' : 'Activar' }} trabajador?')">
                                {{ $trabajador->activo ? 'Desactivar trabajador' : 'Activar trabajador' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>