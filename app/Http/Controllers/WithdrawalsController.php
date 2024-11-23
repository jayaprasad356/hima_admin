<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Withdrawals;
use App\Models\BankDetails;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WithdrawalsExport; 
use App\Exports\UsersExports;
use Illuminate\Support\Facades\DB; 

class WithdrawalsController extends Controller
{
    public function verify(Request $request)
    {
        $withdrawalIds = $request->input('withdrawal_ids', []);

        foreach ($withdrawalIds as $withdrawalId) {
            $withdrawal = Withdrawals::find($withdrawalId);
            if ($withdrawal) {
                // Update the withdrawal status to Paid (1)
                $withdrawal->status = 1;
                $withdrawal->save();
            }
        }

        return response()->json(['success' => true]);
    }
    public function cancel(Request $request)
    {
        $withdrawalIds = $request->input('withdrawal_ids', []);
    
        foreach ($withdrawalIds as $withdrawalId) {
            $withdrawal = Withdrawals::find($withdrawalId);
            if ($withdrawal) {
                // Retrieve the user associated with the withdrawal
                $user = Users::find($withdrawal->user_id);
    
                if ($user) {
                    // Add the amount back to the user's balance
                    $user->balance += $withdrawal->amount;
                    $user->save();
                }
                \App\Models\Transaction::create([
                    'user_id' => $user->id,  // User associated with the withdrawal
                    'type' => 'cancelled',    // Transaction type (cancelled)
                    'points' => '0',  
                    'amount' => $withdrawal->amount,  // Points (amount of the withdrawal)
                    'datetime' => now(),      // Current timestamp
                ]);
                // Update the withdrawal status to Canceled (0)
                $withdrawal->status = 2;
                $withdrawal->save();
            }
        }
    
        return response()->json(['success' => true]);
    }
    
    public function index(Request $request)
    {
        $query = Withdrawals::query()->with(['user', 'user.bankDetails']); // Eager load the user and their bank details
    
        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }
    
        // Filter by search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      });
            });
        }
    
        // Filter by verified status
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }
    
        // Single Date Filter
        if ($request->filled('filter_date')) {
            $filterDate = $request->input('filter_date');
            $query->whereDate('datetime', $filterDate);
        }
    
        // Check if the request is AJAX
        if ($request->wantsJson()) {
            return response()->json($query->get());
        }
    
        $withdrawals = $query->latest()->paginate(10); // Paginate the results
        $bankdetails = BankDetails::all();  // Fetch all bank details for the filter dropdown
        $users = Users::all(); // Fetch all users for the filter dropdown
    
        return view('withdrawals.index', compact('withdrawals', 'users', 'bankdetails')); // Pass withdrawals, users, and bankdetails to the view
    }
    

    public function edit(Withdrawals $withdrawal)
    {
        return view('withdrawals.edit', compact('withdrawal'));
    }

    public function update(Request $request, Withdrawals $withdrawal)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $withdrawal->status = $request->input('status');

        if ($withdrawal->save()) {
            return redirect()->route('withdrawals.edit', $withdrawal->id)
                ->with('success', 'Success, withdrawals have been updated.');
        } else {
            return redirect()->back()
                ->with('error', 'Sorry, something went wrong while updating the withdrawal.');
        }
    }

    public function destroy(Withdrawals $withdrawal)
    {
        $withdrawal->delete();

        return response()->json([
            'success' => true,
        ]);
    }
    public function export(Request $request)
    {
        return Excel::download(new WithdrawalsExport($request->all()), 'withdrawals.xlsx');
    }
    public function exportUsers(Request $request)
    {
        return Excel::download(new UsersExports($request->all()), 'users.xlsx');
    }
    
}
