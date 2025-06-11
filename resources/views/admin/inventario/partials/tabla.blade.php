@foreach($bienesalmacen as $bienes)
    <tr>
        <td>{{ $bienes->id }}</td>
        <td>{{ $bienes->tipoinventario }}</td>
        <td>{{ $bienes->nombreproducto }}</td>
        <td>{{ $bienes->especificacionmedida }}</td>
        <td>{{ $bienes->color }}</td>
        <td>{{ $bienes->cantidad }}</td>
        <td>{{ $bienes->precio }}</td>
        <td>{{ $bienes->proveedornombre }}</td>
        <td style="background-color: #f8ffed">{{ $bienes->cantidadalmacen ?? 'Sin stock' }}</td>
        <td>
            <input type="checkbox" class="select-item"
            data-id="{{ $bienes->id }}"
            data-nombre="{{ $bienes->nombreproducto }}"
            data-color="{{ $bienes->color }}"
            data-precio="{{ $bienes->precio }}"
            data-cantidad="{{ $bienes->cantidad }}"
            data-proveedorid="{{ $bienes->proveedorid }}"
            data-proveedorNombre="{{ $bienes->proveedornombre ?? '' }}">

        </td>
    </tr>
@endforeach


