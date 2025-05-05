<x-base_layout>    
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-blue-300 flex items-center justify-center p-6">
        <div class="bg-white p-10 rounded-2xl shadow-xl shadow-blue-950 w-full max-w-2xl border border-blue-200">
            <div class="text-center mb-10">
                <div class="flex">
                    <div class="icon">
                        <a href="{{route('home.ingresarRangos')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#cdcdcd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m12 19-7-7 7-7"></path>
                                <path d="M19 12H5"></path>
                            </svg>
                        </a>
                    </div>                    
                    <div class="mx-auto">
                        <h1 class="text-4xl font-bold text-blue-950">Incidencia Delictiva</h1>
                    </div>                    
                </div>                               
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
                        class="
                            w-full 
                            text-center border 
                            @error ('reporte')
                                border-red-300
                                focus:ring-red-600
                                
                            @else
                                border-gray-300
                                focus:ring-blue-400
                            @enderror
                            rounded-xl px-4 py-2 shadow-sm focus:ring-2 focus:outline-none transition-all duration-200">
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($reportes as $reporte => $description)
                            <option value="{{ $reporte }}">{{ $i . '- ' . $description }}</option>
                            @php $i++; @endphp
                        @endforeach
                    </select>
                    @error ('reporte')
                        <div class="flex m-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FF0000" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>                          
                            <span class="italic font-serif ml-4">{{$message}}</span>
                        </div>
                    @enderror                    
                </div>
                <div class="mt-16 flex justify-end">
                    <button id="btnGenereteReport" 
                        type="submit" 
                        class="bg-blue-950 hover:bg-[rgb(192,159,119)] text-white font-semibold px-6 py-2 rounded-xl shadow-md transition-all duration-200">Generar
                    </button>
                </div>            
            </form>            
        </div>

        <div id="loaderReport" style="display: none;" class="fixed inset-0 bg-white bg-opacity-60 flex items-center justify-center z-50">
            <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-24 w-24"></div>
        </div>
    </div>

    <script type="text/javascript">       
        const formReporte = document.getElementById("formReporte");
        const meses= @json($months);
        var year = {{ $rangos['reporte_anio'] }};
        var monthStart = {{ $rangos['mes_inicial'] }};        
        var monthEnd = {{ $rangos['mes_final'] }};

        monthStart = meses[monthStart];
        monthEnd = meses[monthEnd];
        year = year.toString().slice(-2);
        monthStart = monthStart.toString().slice(0, 3).toUpperCase();
        monthEnd = monthEnd.toString().slice(0, 3).toUpperCase();

        if (monthStart === monthEnd) {            
            var temporary = '_' + monthStart + '_' + year + '.xlsm'
        }
        else {
            var temporary = '_' + monthStart + '-' + monthEnd + '_' + year + '.xlsm'
        }
    
        formReporte.addEventListener("submit", function (e) {
            const reportes = {
                1: '1.INCIDENCIA_COMPARATIVO.xlsm',
                2: '2.INCIDENCIA_POR_MES.xlsm',
                3: '3.Alto_Impacto_COMPARATIVO.xlsm',
                4: '7.Secuestros_KARDEX.xlsm',
                5: '8.Extorsiones_KARDEX.xlsm',
                6: 'Reporte_Homicidios.zip',
                7: '9.Privacion_libertad_KARDEX_COMPARATIVO.xlsm',
                8: 'Reporte_Lesiones.zip',
                9: '15.Robos_POR_SUB_COMPARATIVO.xlsm',
                10: '15.1Robo_por_Meses.xlsm',
                11: '14.Informativo_MES.xlsm',
                12: '14.1_Informativo_Acumulado' + temporary,
                13: 'Incremento_Decremento.zip',
                14: '16.Incidencia_Estatal_Fiscalia.xlsm',
                15: '4.delitosmodalidad.xlsm',
                16: '13.Trata_Personas.xlsm',
                17: '12.Feminicidios.xlsm',
                18: '3.1Graficas_Alto_Impacto.xlsm'
            };
    
            e.preventDefault(); 
            
            const reporte = document.getElementById("reporte").value;
            const loader = document.getElementById("loaderReport");
            loader.style.display = "flex";            
    
            const formData = new FormData(formReporte);
    
            fetch("{{ route('home.procesarReporte') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => {
                loader.style.display = "none";
    
                const contentType = response.headers.get("Content-Type");
    
                if (contentType && contentType.includes("text/html")) {
                    return response.text().then(html => {
                        document.open();
                        document.write(html);
                        document.close();
                    });
                }
    
                if (!response.ok) {
                    throw new Error("Error en la petición: " + response.status);
                }
    
                return response.blob();
            })
            .then(blob => {
                if (!blob || blob.type === "text/html") return;
    
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = reportes[reporte] || "archivo.xlsx";
                document.body.appendChild(a);
                a.click();
                a.remove();
            })
            .catch(error => {
                loader.style.display = "none";
                console.error("Hubo un error:", error);
            });
        });
    </script>

</x-base_layout>
