public function guardarrespuestaadjunto(Request $request, Cliente $cliente)
    {
        $request->validate([
            'document2adjunto' => 'nullable|file|mimes:pdf',
            'document3adjunto' => 'nullable|file|mimes:pdf',
            'observacionesadjunto' => '',
            'citenotaadjunto' => '',
            'fechacitenotaadjunto' => '',
            'fechainclusionadjunto' => '',
        ]);

        $tramite = Tramite::findOrFail($request->tramite_id);

        if ($request->hasFile('document2adjunto')) {
            $archivo = $request->file('document2adjunto');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/ADJUNTOS Y RESPUESTAS");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre);
            $tramite->document2 = $archivoNombre;
        }
        if ($request->hasFile('document3adjunto')) {
            $archivo = $request->file('document3adjunto');
            $archivoNombre2 = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/ADJUNTOS Y RESPUESTAS");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre2);
            $tramite->document3 = $archivoNombre2;
        }

        $tramite->observaciones = $request->observacionesadjunto;
        $tramite->citenota = $request->citenotaadjunto;
        $tramite->fechacitenota = $request->fechacitenotaadjunto;
        $tramite->fechainclusion = $request->fechainclusionadjunto;
        $tramite->save();

        return back()->with('info', 'Respuesta guardada correctamente.');
    }