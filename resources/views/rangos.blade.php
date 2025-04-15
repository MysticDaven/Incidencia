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
                        class="
                            w-full 
                            text-center border 
                            @error ('reporte_anio')
                                border-red-300
                                focus:ring-red-600
                                
                            @else
                                border-gray-300
                                focus:ring-blue-400
                            @enderror
                            rounded-xl px-4 py-2 shadow-sm focus:ring-2 focus:outline-none transition-all duration-200">
                        @foreach ($years as $year)
                            <option 
                                value="{{ $year }}" 
                                    @if (session('rangos'))
                                        {{ session("rangos")['reporte_anio'] == $year ? 'selected' : '' }}
                                    @endif
                                >{{$year}}
                            </option>
                        @endforeach
                    </select>
                    @error ('reporte_anio')
                        <div class="flex m-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FF0000" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>                          
                            <span class="italic font-serif ml-4">{{$message}}</span>
                        </div>
                    @enderror                    
                </div>

                <div>
                    <label for="mes_inicial" class="block text-lg font-medium text-gray-800 mb-1">
                        <svg class="inline-block mb-3" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#152F4A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg> Mes Inicial
                    </label>
                    <select 
                        name="mes_inicial" 
                        id="mes_inicial" 
                        class="
                            w-full 
                            text-center border 
                            @error ('mes_inicial')
                                border-red-300
                                focus:ring-red-600
                                
                            @else
                                border-gray-300
                                focus:ring-blue-400
                            @enderror
                            rounded-xl px-4 py-2 shadow-sm focus:ring-2 focus:outline-none transition-all duration-200">
                        @foreach ($months as $month => $description)
                            <option value="{{ $month }}"
                                @if (session('rangos')) 
                                    {{ session('rangos')['mes_inicial'] ==  $month ? 'selected' : ''}}
                                @endif
                                >{{ $description}}
                            </option>                            
                        @endforeach
                    </select>
                    @error ('mes_inicial')
                        <div class="flex m-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FF0000" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>                          
                            <span class="italic font-serif ml-4">{{$message}}</span>
                        </div>
                    @enderror                    
                </div>

                <div>
                    <label for="mes_final" class="block text-lg font-medium text-gray-800 mb-1">
                        <svg class="inline-block mb-3" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#152F4A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg> Mes Final
                    </label>
                    <select 
                        name="mes_final" 
                        id="mes_final" 
                        class="
                            w-full 
                            text-center border                             
                            @error ('mes_final')
                                border-red-300
                                focus:ring-red-600
                                
                            @else
                                border-gray-300
                                focus:ring-blue-400
                            @enderror
                            rounded-xl px-4 py-2 shadow-sm focus:ring-2 focus:outline-none transition-all duration-200">
                        @foreach ($months as $month => $description)
                            <option value="{{ $month }}"
                                @if (session('rangos'))
                                    {{ session('rangos')['mes_final'] == $month ? 'selected' : ''}}
                                @endif
                                >{{ $description }}
                            </option>
                        @endforeach
                    </select>
                    @error ('mes_final')
                        <div class="flex m-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FF0000" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>                          
                            <span class="italic font-serif ml-4">{{$message}}</span>
                        </div>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="bg-blue-950 hover:bg-[rgb(192,159,119)] text-white font-semibold px-6 py-2 rounded-xl shadow-md transition-all duration-200">
                        Siguiente
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-base_layout>
