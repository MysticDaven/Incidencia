<x-base_layout>
    <div class="bg-gray-100 flex items-baseline justify-center h-screen w-full">
        <div class="bg-white p-8 rounded-lg shadow-lg shadow-blue-950 w-full max-w-lg mt-32">
            <div class="mb-4">
                <h1 class="text-5xl font-semibold bg-gradient-to-r">Incidencia Delictiva</h1>
                <p class="mt-2 inline-block ">Se generará un reporte en el siguiente periodo:</p>
                @if ($rangos['mes_inicial'] == $rangos['mes_final'])
                    <p class="text-center text-2xl ">
                        {{ $months[$rangos['mes_inicial']] ?? 'No definido' }} 
                        {{ $rangos['reporte_anio'] ?? 'No definido' }}
                    </p>
                @else
                    <p class="text-center text-2xl ">
                        {{ $months[$rangos['mes_inicial']] ?? 'No definido' }} - 
                        {{ $months[$rangos['mes_final']] ?? 'No definido' }} 
                        {{ $rangos['reporte_anio'] ?? 'No definido' }}
                    </p>
                @endif            
            </div>
            <form action="{{ route('home.procesarReporte') }}" method="POST">
                @csrf
                <div class="text-center">
                    <label for="reporte" class="block text-3xl font-semibold mt-2">Selecciona Reporte</label>
                    <select 
                        name="reporte" 
                        id="reporte" 
                        class=" w-3/4 text-center my-2 border-2 rounded-lg focus:outline-none focus:bg-white focus:border-blue-700">
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($reportes as $reporte => $description)
                            <option value="{{ $reporte }}">{{ $i . '- ' . $description }}</option>
                            @php $i++; @endphp
                        @endforeach
                    </select>
                </div>
                <div class="mt-16 flex justify-end">
                    <button type="submit" class="bg-blue-950 hover:bg-opacity-90 text-white font-semibold rounded-lg px-8 py-2">Siguiente</button>
                </div>            
            </form>
        </div>
    </div>
</x-base_layout>
