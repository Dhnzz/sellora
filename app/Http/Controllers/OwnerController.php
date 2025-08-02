<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesTransaction;
use App\Models\SupplierPurchase;
use Illuminate\Support\Facades\Auth;

class OwnerController
{
    public function dashboard()
    {
        $omset = SalesTransaction::sum('final_total_amount');
        $expense = SupplierPurchase::sum('total_amount');
        $income = $omset - $expense;

        $data = [
            'role' => Auth::user()->getRoleNames()->first(),
            'active' => 'dashboard',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('owner.dashboard'),
                ],
            ],
            'omset' => $omset,
            'expense' => $expense,
            'income' => $income,
        ];
        return view('owner.dashboard', compact('data'));
    }

    public function filterData(Request $request)
    {
        $type = $request->input('type');
        $filter = $request->input('filter', 'all');

        $queryOmset = SalesTransaction::query();
        $queryExpense = SupplierPurchase::query();

        if ($type === 'omset') {
            if ($filter === 'day') {
                $queryOmset->whereDate('invoice_date', now());
            } elseif ($filter === 'month') {
                $queryOmset->whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year);
            } elseif ($filter === 'year') {
                $queryOmset->whereYear('invoice_date', now()->year);
            }

            $omset = $queryOmset->sum('final_total_amount');
            return response()->json([
                'omset' => number_format($omset, 2, ',', '.'),
            ]);
        } elseif ($type === 'expense') {
            if ($filter === 'day') {
                $queryExpense->whereDate('purchase_date', now());
            } elseif ($filter === 'month') {
                $queryExpense->whereMonth('purchase_date', now()->month)->whereYear('purchase_date', now()->year);
            } elseif ($filter === 'year') {
                $queryExpense->whereYear('purchase_date', now()->year);
            }

            $expense = $queryExpense->sum('total_amount');
            return response()->json([
                'expense' => number_format($expense, 2, ',', '.'),
            ]);
        } elseif ($type === 'income') {
            if ($filter === 'day') {
                $queryOmset->whereDate('invoice_date', now());
                $queryExpense->whereDate('purchase_date', now());
            } elseif ($filter === 'month') {
                $queryOmset->whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year);
                $queryExpense->whereMonth('purchase_date', now()->month)->whereYear('purchase_date', now()->year);
            } elseif ($filter === 'year') {
                $queryOmset->whereYear('invoice_date', now()->year);
                $queryExpense->whereYear('purchase_date', now()->year);
            }

            $omset = $queryOmset->sum('final_total_amount');
            $expense = $queryExpense->sum('total_amount');
            $income = $omset - $expense;
            return response()->json([
                'omset' => number_format($omset, 2, ',', '.'),
                'expense' => number_format($expense, 2, ',', '.'),
                'income' => number_format($income, 2, ',', '.'),
            ]);
        }
    }
}
