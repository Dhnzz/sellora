<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\UnitConvertion;
use Yajra\DataTables\Facades\DataTables;

class UnitConvertionController
{
    public function getAll(Request $request)
    {
        if ($request->ajax()) {
            $data = UnitConvertion::leftJoin('product_units', 'product_units.id', '=', 'unit_convertions.from_unit_id')
                    ->select(
                        'unit_convertions.id as id',
                        'unit_convertions.product_id as product_id',
                        'product_units.name as from_unit_name',
                        'unit_convertions.convertion_factor as convertion_factor'
                    )
                    ->where('product_id', '=', $request->productId);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('options', function ($row) {
                    $btn = '<div class="d-flex justify-content-center gap-1">';
                    $btn .= '<button type="button" class="btn btn-sm btn-warning edit-btn" data-id="' . $row->id . '"><i class="ti ti-pencil"></i></a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '"><i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['options'])
                ->filter(function ($query) use ($request) {
                    // Filter berdasarkan pencarian global DataTables
                    if ($request->has('search') && !empty($request->input('search')['value'])) {
                        $searchValue = $request->input('search')['value'];
                        $query->where('from_unit_name', 'like', "%{$searchValue}%")->orWhere('convertion_factor', 'like', "%{$searchValue}%");
                    }
                })
                ->order(function ($query) use ($request) {
                    // Sortir berdasarkan kolom yang diminta DataTables
                    if ($request->has('order')) {
                        $order = $request->input('order')[0];
                        $columnIndex = $order['column'];
                        $columnName = $request->input('columns')[$columnIndex]['data'];
                        $sortDirection = $order['dir'];

                        // Pastikan hanya kolom yang boleh di-sort yang diproses
                        $sortableColumns = ['id', 'from_unit_name', 'convertion_factor']; // Kolom yang bisa di-sort
                        if (in_array($columnName, $sortableColumns)) {
                            $query->orderBy($columnName, $sortDirection);
                        } elseif ($columnName === 'DT_RowIndex') {
                            // Abaikan sorting untuk kolom nomor urut jika tidak diperlukan
                            // Atau tambahkan default sorting jika perlu
                            $query->orderBy('created_at', 'desc');
                        }
                    }
                })
                ->make(true);
        }
    }

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
