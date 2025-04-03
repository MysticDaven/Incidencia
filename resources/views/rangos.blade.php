<x-base_layout>
    <div class="bg-gray-100 flex items-baseline justify-center h-screen w-full">
        <div class="bg-white p-8 rounded-lg shadow-lg shadow-blue-950 w-full max-w-lg mt-32">     
            <h1 class="text-5xl font-semibold">Incidencia Delictiva</h1>       
            <p class="mt-2 mb-8 inline-block">Ingresa la información solicitada para generar el reporte.</p>
            <form action="{{ route('home.procesarRangos') }}" method="POST">
                @csrf
                <div class=" text-center">
                    <label for="reporte_anio" class="block text-2xl font-semibold mt-2">Ingresar el año del reporte:</label>
                    <select 
                        name="reporte_anio" 
                        id="reporte_anio" 
                        class=" w-3/4 text-center my-2 border-2 rounded-lg focus:outline-none focus:bg-white focus:border-blue-700">
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class=" text-center">
                    <label for="mes_inicial" class="block text-2xl font-semibold mt-2">Ingresa el Mes Inicial:</label>
                    <select 
                        name="mes_inicial" 
                        id="mes_inicial" 
                        class=" w-3/4 text-center my-2 border-2 rounded-lg focus:outline-none focus:bg-white focus:border-blue-700">
                        @foreach ($months as $month => $description)
                            <option value="{{ $month }}">{{ $description }}</option>
                        @endforeach
                    </select>
                </div>
    
                <div class=" text-center">
                    <label for="mes_final" class="block text-2xl font-semibold mt-2">Ingresa el Mes Final:</label>
                    <select 
                        name="mes_final" 
                        id="mes_final" 
                        class=" w-3/4 text-center my-2 border-2 rounded-lg focus:outline-none focus:bg-white focus:border-blue-700">
                        @foreach ($months as $month => $description)
                            <option value="{{ $month }}">{{ $description }}</option>
                        @endforeach
                    </select>
                </div>
    
                <div class=" mt-16 flex justify-end">
                    <button class="btn btn-primary px-8 py-2 bg-blue-950 hover:bg-opacity-90 text-white font-semibold rounded-lg">Siguiente</button>
                </div>
            </form>
        </div>
    </div>
</x-base_layout>
