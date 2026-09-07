<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                         => 'required|string|max:255',
            'email'                        => 'nullable|email|max:255',
            'phone'                        => 'nullable|string|max:50',
            'address'                      => 'nullable|string',
            'tax_id_type'                  => 'required|in:NPWP16,NIK,PASPOR',
            'tax_id_number'                => 'nullable|string|max:50',
            'nitku'                        => 'nullable|string|max:22',
            'default_tax_transaction_code' => 'required|string|max:10',
        ], [
            'name.required' => 'Nama customer/perusahaan wajib diisi.',
            'tax_id_type.in' => 'Jenis ID Pajak harus berupa NPWP16, NIK, atau PASPOR.',
        ]);

        $data = $request->all();
        // Bersihkan spasi/tanda baca jika ada pada NPWP/NITKU
        if (!empty($data['tax_id_number']) && in_array($data['tax_id_type'], ['NPWP16', 'NIK'])) {
            $data['tax_id_number'] = preg_replace('/[^0-9]/', '', $data['tax_id_number']);
        }
        if (!empty($data['nitku'])) {
            $data['nitku'] = preg_replace('/[^0-9]/', '', $data['nitku']);
        } else {
            $data['nitku'] = '0000000000000000000000';
        }

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Data Customer berhasil ditambahkan!');
    }

    // Method Edit: Menampilkan Form Edit Customer
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    // Method Update: Memperbarui Data Customer
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'                         => 'required|string|max:255',
            'email'                        => 'nullable|email|max:255',
            'phone'                        => 'nullable|string|max:50',
            'address'                      => 'nullable|string',
            'tax_id_type'                  => 'required|in:NPWP16,NIK,PASPOR',
            'tax_id_number'                => 'nullable|string|max:50',
            'nitku'                        => 'nullable|string|max:22',
            'default_tax_transaction_code' => 'required|string|max:10',
        ], [
            'name.required' => 'Nama customer/perusahaan wajib diisi.',
            'tax_id_type.in' => 'Jenis ID Pajak harus berupa NPWP16, NIK, atau PASPOR.',
        ]);

        $customer = Customer::findOrFail($id);
        $data = $request->all();

        if (!empty($data['tax_id_number']) && in_array($data['tax_id_type'], ['NPWP16', 'NIK'])) {
            $data['tax_id_number'] = preg_replace('/[^0-9]/', '', $data['tax_id_number']);
        }
        if (!empty($data['nitku'])) {
            $data['nitku'] = preg_replace('/[^0-9]/', '', $data['nitku']);
        } else {
            $data['nitku'] = '0000000000000000000000';
        }

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Data Customer berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Data Customer berhasil dihapus!');
    }
}