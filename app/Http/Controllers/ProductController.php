<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\ProductBrand;
use Illuminate\Http\Request;
use App\Models\UnitConvertion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController
{
    public function index(Request $request)
    {
        $data = [
            'title' => 'Manajemen Produk',
            'role' => Auth::user()->getRoleNames()->first(),
            'active' => 'master_data_product',
            'breadcrumbs' => [
                [
                    'name' => 'Manajemen User',
                    'link' => '#',
                ],
                [
                    'name' => 'Produk',
                    'link' => route('owner.master_data.product.index'),
                ],
            ],
        ];
        // Untuk tampilan awal, $products sudah di-paginate di atas
        return view('owner.master_data.product.page.index', compact('data'));
    }

    public function getAll(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::latest()->leftJoin('stocks', 'products.id', '=', 'stocks.product_id')->rightJoin('product_units', 'product_units.id', '=', 'products.minimum_selling_unit_id')->rightJoin('product_brands', 'product_brands.id', '=', 'products.product_brand_id')->select('products.id', 'products.name as product_name', 'products.selling_price', 'product_units.name as msu_name', 'stocks.quantity', 'products.created_at')->whereHas('stock')->whereHas('product_brand')->whereHas('product_unit');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('options', function ($row) {
                    $btn = '<div class="d-flex justify-content-center gap-1">';
                    $btn .= '<a href="' . route('owner.master_data.product.detail', $row->id) . '" class="btn btn-sm btn-primary"><i class="ti ti-eye"></i></a>';
                    $btn .= '<a href="' . route('owner.master_data.product.edit', $row->id) . '" class="btn btn-sm btn-warning"><i class="ti ti-pencil"></i></a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '"><i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('selling_price', function ($row) {
                    // Format angka ke dalam format rupiah
                    return 'Rp ' . number_format($row->selling_price, 0, ',', '.');
                })
                ->editColumn('quantity', function ($row) {
                    return $row->quantity . ' ' . ucfirst($row->msu_name);
                })
                ->rawColumns(['options'])
                ->filter(function ($query) use ($request) {
                    // Filter berdasarkan pencarian global DataTables
                    if ($request->has('search') && !empty($request->input('search')['value'])) {
                        $searchValue = $request->input('search')['value'];
                        $query
                            ->where('product_name', 'like', "%{$searchValue}%")
                            ->orWhere('selling_price', 'like', "%{$searchValue}%")
                            ->orWhere('msu_name', 'like', "%{$searchValue}%")
                            ->orWhere('quantity', 'like', "%{$searchValue}%");
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
                        $sortableColumns = ['id', 'product_name', 'selling_price', 'msu_name', 'quantity']; // Kolom yang bisa di-sort
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

    public function getById(Product $product)
    {
        $unit_to_convert = ProductUnit::where('id', '!=', $product->minimum_selling_unit_id)->get();
        $data = [
            'title' => 'Manajemen Produk',
            'role' => Auth::user()->getRoleNames()->first(),
            'active' => 'master_data_product',
            'breadcrumbs' => [
                [
                    'name' => 'Manajemen User',
                    'link' => '#',
                ],
                [
                    'name' => 'Produk',
                    'link' => route('owner.master_data.product.index'),
                ],
                [
                    'name' => 'Detail Produk',
                    'link' => route('owner.master_data.product.detail', $product->id),
                ],
            ],
            'unit_to_convert' => $unit_to_convert,
        ];

        return view('owner.master_data.product.page.detail', compact('data', 'product'));
    }

    public function create()
    {
        $data = [
            'title' => 'Manajemen Produk',
            'role' => Auth::user()->getRoleNames()->first(),
            'active' => 'master_data_product',
            'breadcrumbs' => [
                [
                    'name' => 'Manajemen User',
                    'link' => '#',
                ],
                [
                    'name' => 'Produk',
                    'link' => route('owner.master_data.product.index'),
                ],
                [
                    'name' => 'Tambah Produk',
                    'link' => route('owner.master_data.product.create'),
                ],
            ],
            'product_brands' => ProductBrand::all(),
            'product_units' => ProductUnit::all()
        ];
        return view('owner.master_data.product.page.create', compact('data'));
    }

    public function edit(Admin $admin)
    {
        $data = [
            'title' => 'Manajemen Admin',
            'role' => Auth::user()->getRoleNames()->first(),
            'active' => 'master_data_admin',
            'breadcrumbs' => [
                [
                    'name' => 'Manajemen User',
                    'link' => '#',
                ],
                [
                    'name' => 'Admin',
                    'link' => route('owner.master_data.admin.index'),
                ],
                [
                    'name' => 'Edit Admin',
                    'link' => route('owner.master_data.admin.edit', $admin->id),
                ],
            ],
        ];
        return view('owner.master_data.admin.page.edit', compact('data', 'admin'));
    }

    public function deletePhoto(Admin $admin, Request $request)
    {
        if ($request->ajax()) {
            try {
                if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
                    Storage::disk('public')->delete($admin->photo);
                    $photoPath = 'uploads/images/users/user-1.jpg';
                    $admin->update([
                        'photo' => $photoPath,
                    ]);
                    return response()->json(['success' => 'Berhasil menghapus foto ' . $admin->name, 'photo' => $photoPath]);
                }
            } catch (\Exception $e) {
                // Log error untuk debugging
                \Log::error('Gagal menghapus foto admin: ' . $e->getMessage(), ['admin_id' => $admin->id]);
                return response()->json(['error' => 'Gagal menghapus foto admin. ' . $e->getMessage()], 500);
            }
        }
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'product_brand' => 'required',
                'product_unit' => 'required',
                'selling_price' => 'required|numeric',
                'stock' => 'nullable',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            [
                'name.required' => 'Nama produk wajib diisi.',
                'product_brand.required' => 'Brand produk wajib diisi.',
                'product_unit.required' => 'MSU wajib diisi.',
                'selling_price.required' => 'Harga jual wajib diisi.',
                'image.image' => 'File gambar harus berupa gambar.',
                'image.mimes' => 'Gambar harus berformat jpeg, png, jpg, atau gif.',
                'image.max' => 'Ukuran gambar maksimal 2MB.',
            ],
        );

        // Cek apakah ada file foto yang diupload
        $imagePath = 'uploads/images/products/product-1.png';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $imagePath = Storage::disk('public')->putFileAs('uploads/images/products', $file, $filename);
        }

        $product = Product::create([
            'name' => $request->name,
            'product_brand_id' => $request->product_brand,
            'minimum_selling_unit_id' => $request->product_unit,
            'selling_price' => $request->selling_price,
            'image' => $imagePath
        ]);

        if ($request->stock != null) {
            Stock::create([
                'product_id' => $product->id,
                'quantity' => $request->stock
            ]);
        }

        if (!$product) {
            return redirect()->route('owner.master_data.product.index')->with('error', 'Produk gagal ditambahkan');
        }

        return redirect()->route('owner.master_data.product.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate(
            [
                'email' => 'required|email|unique:users,email,' . $admin->user_id,
                'name' => 'required',
                'phone' => 'required|min:12',
                'address' => 'required',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'name.required' => 'Nama wajib diisi.',
                'phone.required' => 'Nomor telepon wajib diisi.',
                'phone.min' => 'Nomor telepon minimal 12 digit.',
                'address.required' => 'Alamat wajib diisi.',
                'photo.image' => 'File foto harus berupa gambar.',
                'photo.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif.',
                'photo.max' => 'Ukuran foto maksimal 2MB.',
            ],
        );

        // Cek apakah ada file foto yang diupload
        $photoPath = $admin->photo;
        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $photoPath = Storage::disk('public')->putFileAs('uploads/images/users', $file, $filename);
        }

        $admin->user->update([
            'email' => $request->email,
        ]);

        $admin->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'photo' => $photoPath,
            'address' => $request->address,
        ]);

        if (!$admin) {
            return redirect()->route('owner.master_data.admin.index')->with('error', 'Admin gagal diupdate');
        }

        return redirect()->route('owner.master_data.admin.index')->with('success', 'Admin berhasil diupdate');
    }

    public function destroy(Request $request, Admin $admin)
    {
        if ($admin->photo != 'uploads/images/users/user-1.jpg') {
            if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
                Storage::disk('public')->delete(paths: $admin->photo);
            } else {
                // Opsional: Log peringatan jika path gambar ada di DB tapi file tidak ditemukan di storage
                // Ini bisa terjadi jika file sudah dihapus secara manual atau ada inkonsistensi data
                if ($admin->photo) {
                    \Log::warning('File gambar tidak ditemukan di storage saat mencoba menghapus: ' . $admin->photo);
                }
            }
        }

        if ($request->ajax()) {
            try {
                $userAdmin = $admin->user;
                $userAdmin->delete();
                $admin->delete();
                return response()->json(['success' => 'Admin berhasil dihapus!']);
            } catch (\Exception $e) {
                // Log error untuk debugging
                \Log::error('Gagal menghapus admin: ' . $e->getMessage(), ['admin_id' => $admin->id]);
                return response()->json(['error' => 'Gagal menghapus admin. ' . $e->getMessage()], 500);
            }
        } else {
            $admin->delete();
            return redirect()->route('owner.master_data.admin.index')->with('success', 'Admin berhasil dihapus!');
        }
    }
}
