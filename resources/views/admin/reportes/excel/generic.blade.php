<table>
    <thead>
        <tr>
            <th colspan="{{ count($columnas) }}">
                {{ $titulo }}
            </th>
        </tr>

        <tr>
            <th colspan="{{ count($columnas) }}">
                Desde {{ $inicio->format('d/m/Y') }} hasta {{ $fin->format('d/m/Y') }}
            </th>
        </tr>

        <tr>
            @foreach ($columnas as $columna)
                <th>{{ $columna }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @forelse ($filas as $fila)
            <tr>
                @foreach ($fila as $valor)
                    <td>{{ $valor }}</td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($columnas) }}">
                    No existen registros para el periodo seleccionado.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>