<x-base_layout>
    <div class="container mt-5">
        <div>
            <h1>Se generará un reporte sobre los siguientes datos</h1>
            <p>Año seleccionado: {{ $rangos['reporte_anio'] ?? 'No definido' }}</p>
            <p>Mes inicial: {{ $rangos['mes_inicial'] ?? 'No definido' }}</p>
            <p>Mes final: {{ $rangos['mes_final'] ?? 'No definido' }}</p>
        </div>
        <form action="{{ route('reporte.procesarReporte') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="reporte">Selecciona Reporte</label>
                <select name="reporte" id="reporte" class="form-control">
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($reportes as $reporte => $description)
                        <option value="{{ $reporte }}">{{ $i . '- ' . $description }}</option>
                        @php $i++; @endphp
                    @endforeach
                </select>
            </div>
            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">Siguiente</button>
            </div>            
        </form>
    </div>
</x-base_layout>
