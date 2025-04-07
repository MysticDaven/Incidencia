<x-base_layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-blue-300 flex items-center justify-center p-6">
        <div class="bg-white p-10 rounded-2xl shadow-xl shadow-blue-950 w-full max-w-2xl border border-blue-200">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-blue-950">Incidencia Delictiva</h1>
                <p class="text-gray-600 mt-3">Ingresa la información solicitada para generar el reporte.</p>
            </div>

            <form action="{{ route('home.procesarRangos') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="reporte_anio" class="block text-lg font-medium text-gray-800 mb-1">
                        <svg class="inline-block mb-3" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#152F4A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg> Año del reporte
                    </label>
                    <select 
                        name="reporte_anio" 
                        id="reporte_anio" 
                        class="w-full text-center border border-gray-300 rounded-xl px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all duration-200">
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="mes_inicial" class="block text-lg font-medium text-gray-800 mb-1">
                        <svg class="inline-block mb-3" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#152F4A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg> Mes Inicial
                    </label>
                    <select 
                        name="mes_inicial" 
                        id="mes_inicial" 
                        class="w-full text-center border border-gray-300 rounded-xl px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all duration-200">
                        @foreach ($months as $month => $description)
                            <option value="{{ $month }}">{{ $description }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="mes_final" class="block text-lg font-medium text-gray-800 mb-1">
                        <svg class="inline-block mb-3" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#152F4A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg> Mes Final
                    </label>
                    <select 
                        name="mes_final" 
                        id="mes_final" 
                        class="w-full text-center border border-gray-300 rounded-xl px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all duration-200">
                        @foreach ($months as $month => $description)
                            <option value="{{ $month }}">{{ $description }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="bg-blue-950 hover:bg-blue-800 text-white font-semibold px-6 py-2 rounded-xl shadow-md transition-all duration-200">
                        Siguiente
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-base_layout>
