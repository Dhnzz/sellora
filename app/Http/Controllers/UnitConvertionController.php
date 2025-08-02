<?php

namespace App\Http\Controllers;

use App\Models\UnitConvertion;
use Illuminate\Http\Request;

class UnitConvertionController
{
    public function store(Request $request)
    {
        $request->validate(
            [
                'from_unit' => 'required',
                'convertion_factor' => 'required',
            ],
            [
                'from_unit.required' => 'Unit yang ingin dikonversi wajib diisi.',
                'convertion_factor.required' => 'Nilai konversi wajib diisi.',
            ],
        );

        $unitConvertion = UnitConvertion::create([
            'product_id' => $request->product_id,
            'from_unit_id' => $request->from_unit,
            'to_unit_id' => $request->minimum_selling_unit_id,
            'convertion_factor' => $request->convertion_factor,
        ]);

        if (!$unitConvertion) {
            // Untuk AJAX
            if ($request->ajax()) {
                return response()->json(['error' => 'Unit konversi gagal ditambahkan'], 500);
            }
        }

        // Untuk AJAX
        if ($request->ajax()) {
            return response()->json(['success' => 'Unit konversi berhasil ditambahkan!', 'request' => $request->all()]);
        }
    }
}
