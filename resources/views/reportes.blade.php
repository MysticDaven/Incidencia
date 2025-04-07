<x-base_layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-blue-300 flex items-center justify-center p-6">
        <div class="bg-white p-10 rounded-2xl shadow-xl shadow-blue-950 w-full max-w-2xl border border-blue-200">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-blue-950">Incidencia Delictiva</h1>
                <p class="text-gray-600 mt-3">Se generará un reporte en el siguiente periodo:</p>
                @if ($rangos['mes_inicial'] == $rangos['mes_final'])
                    <p class="text-center text-2xl mt-3 font-bold">
                        {{ $months[$rangos['mes_inicial']] ?? 'No definido' }} 
                        {{ $rangos['reporte_anio'] ?? 'No definido' }}
                    </p>
                @else
                    <p class="text-center text-2xl mt-3 font-bold">
                        {{ $months[$rangos['mes_inicial']] ?? 'No definido' }} - 
                        {{ $months[$rangos['mes_final']] ?? 'No definido' }} 
                        {{ $rangos['reporte_anio'] ?? 'No definido' }}
                    </p>
                @endif            
            </div>
            <form id="formReporte" action="{{ route('home.procesarReporte') }}" method="POST">
                @csrf
                <div>
                    <label for="reporte" class="block text-lg font-medium text-gray-800 mb-1">
                        <svg class="inline-block mb-3" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#152F4A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M8 18v-2"></path><path d="M12 18v-4"></path><path d="M16 18v-6"></path></svg> Selecciona Reporte
                    </label>
                    <select 
                        name="reporte" 
                        id="reporte" 
                        class="w-full text-center border border-gray-300 rounded-xl px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all duration-200">
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
                    <button id="btnGenereteReport" 
                        type="submit" 
                        class="bg-blue-950 hover:bg-blue-800 text-white font-semibold px-6 py-2 rounded-xl shadow-md transition-all duration-200">Generar
                    </button>
                </div>            
            </form>
            <iframe id="downloadFrame" style="display: none;"></iframe>            
        </div>

        <div id="loaderReport" style="display: none;" class="fixed inset-0 bg-white bg-opacity-60 flex items-center justify-center z-50">
            <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-24 w-24"></div>
        </div>
    </div>

    {{-- <script>
        const boton = document.getElementById('btnGenereteReport');
        const loader = document.getElementById('loaderReport');

        boton.addEventListener('click', () => {
            boton.style.display = 'none';
            loader.style.display = 'flex';
        });
    </script> --}}
    {{-- <script>
        const boton = document.getElementById('btnGenereteReport');
        const loader = document.getElementById('loaderReport');
        const form = document.getElementById('formReporte');
        const iframe = document.getElementById('downloadFrame');
    
        boton.addEventListener('click', () => {
            boton.disabled = true;
            loader.style.display = 'flex';
            form.submit();
        });
    
        // Detecta cuando termina la petición de descarga
        iframe.addEventListener('load', () => {
            loader.style.display = 'none';
            location.reload();
        });
    </script> --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            const boton = document.getElementById('btnGenereteReport');
            const loader = document.getElementById('loaderReport');
            const form = document.getElementById('formReporte');
            const iframe = document.getElementById('downloadFrame');
    
            if (!iframe) {
                console.error("El iframe 'downloadFrame' no existe en el DOM.");
                return;
            }
    
            if (!form) {
                console.error("El formulario 'formReporte' no existe en el DOM.");
                return;
            }
    
            // Detectar que el iframe ha cargado
            iframe.addEventListener('load', () => {
                console.log("Evento load del iframe disparado.");
                loader.style.display = 'none';
                location.reload();
            });
    
            // Enviar formulario al hacer click
            boton.addEventListener('click', () => {
                console.log("Botón clickeado. Enviando formulario.");
                boton.disabled = true;
                loader.style.display = 'flex';
                form.submit();
            });
        });
    </script> --}}

    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            const boton = document.getElementById('btnGenereteReport');
            const loader = document.getElementById('loaderReport');
            const form = document.getElementById('formReporte');
    
            const checkCookieInterval = 500; // ms
            const maxWaitTime = 15000; // 15 segundos máximo
    
            boton.addEventListener('click', () => {
                boton.disabled = true;
                loader.style.display = 'flex';
    
                const downloadToken = Date.now(); // o usa UUID
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'download_token';
                input.value = downloadToken;
                form.appendChild(input);
    
                form.submit();
    
                // Revisa periódicamente si se puso la cookie
                let waited = 0;
                const check = setInterval(() => {
                    const cookies = document.cookie.split(';').map(c => c.trim());
                    const found = cookies.find(c => c.startsWith('downloaded='));
    
                    if (found && found.includes(downloadToken)) {
                        clearInterval(check);
                        document.cookie = "downloaded=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; // eliminar cookie
                        loader.style.display = 'none';
                        location.reload(); // o cualquier otra acción
                    }
    
                    waited += checkCookieInterval;
                    if (waited > maxWaitTime) {
                        clearInterval(check);
                        loader.style.display = 'none';
                        alert("La descarga no pudo ser confirmada.");
                    }
                }, checkCookieInterval);
            });
        });
    </script> --}}
    
        
    
</x-base_layout>
