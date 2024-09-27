<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class TablaExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $tabla;
    protected $fechaInicial;
    protected $fechaFinal;

    public function __construct($data, $tabla, $fechaInicial, $fechaFinal)
    {
        $this->data = $data;
        $this->tabla = $tabla;
        $this->fechaInicial = $fechaInicial;
        $this->fechaFinal = $fechaFinal;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        // Lógica para definir los encabezados según la tabla
        switch ($this->tabla) {
            case 'clientes':
                return ['ID', 'Nombre', 'Correo'];
            case 'asociados':
                return ['ID', 'Nombre', 'Teléfono'];
            case 'proveedores':
                return ['ID', 'Nombre', 'Dirección'];
            default:
                return [];
        }
    }
}

