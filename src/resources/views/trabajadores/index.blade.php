<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Trabajadores') }}</h2>
            <a href="{{ route('trabajadores.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-700">
                + Nuevo trabajador
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Restaurante</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Local</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($trabajadores as $t)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ $t->user->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $t->user->email }}</td>
                                <td class="px-4 py-3 text-sm">{{ $t->restaurante->nombre }}</td>
                                <td class="px-4 py-3 text-sm">{{ $t->local?->nombre ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $t->rol }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($t->activo)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Activo</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('trabajadores.edit', $t) }}"
                                       class="text-indigo-600 hover:underline text-sm">Editar</a>

                                    <form action="{{ route('trabajadores.reset-password', $t) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-amber-600 hover:underline text-sm"
                                                onclick="return confirm('¿Resetear contraseña a 12345678?')">
                                            Reset pass
                                        </button>
                                    </form>

                                    <form action="{{ route('trabajadores.toggle-activo', $t) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-sm {{ $t->activo ? 'text-red-600' : 'text-green-600' }} hover:underline"
                                                onclick="return confirm('¿{{ $t->activo ? 'Desactivar' : 'Activar' }} trabajador?')">
                                            {{ $t->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Sin trabajadores.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $trabajadores->links() }}</div>
        </div>
    </div>
</x-app-layout>