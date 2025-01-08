<x-base_layout>
    <div class="container mt-5">
        <form action="{{ route('reporte.procesarRangos') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="reporte_anio">Ingresar el año del reporte:</label>
                <select name="reporte_anio" id="reporte_anio" class="form-control">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="mes_inicial">Ingresa el Mes Inicial:</label>
                <select name="mes_inicial" id="mes_inicial" class="form-control">
                    @foreach ($months as $month => $description)
                        <option value="{{ $month }}">{{ $description }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="mes_final">Ingresa el Mes Final:</label>
                <select name="mes_final" id="mes_final" class="form-control">
                    @foreach ($months as $month => $description)
                        <option value="{{ $month }}">{{ $description }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mt-3">
                <button class="btn btn-primary">Siguiente</button>
            </div>
        </form>
    </div>
</x-base_layout>
