<?php

namespace App\Exports;

use App\Models\Withdrawals;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WithdrawalsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // Start with a query on Withdrawals
        $query = Withdrawals::query()
            ->select(
                'withdrawals.id',
                'users.name as user_name',
                'users.unique_name as unique_name',
                'withdrawals.amount',
                'withdrawals.status', // Include status field
                'withdrawals.datetime',
                'bank_details.bank_name',
                'bank_details.branch_name',
                'bank_details.account_number',
                'bank_details.account_holder_name',
                'bank_details.ifsc_code'
            )
            ->join('users', 'withdrawals.user_id', '=', 'users.id')
            ->leftJoin('bank_details', 'users.id', '=', 'bank_details.user_id') // Assuming bank_details is the correct table name
            ->where('withdrawals.status', 0); // Only fetch status = 0

        // Apply filters if needed
        if (isset($this->filters['status'])) {
            $query->where('withdrawals.status', $this->filters['status']);
        }

        if (isset($this->filters['filter_date'])) {
            $query->whereDate('withdrawals.datetime', $this->filters['filter_date']);
        }

        // Get the withdrawals data with related user and bank details
        $withdrawalsData = $query->get();

        // Map through the data to format status as needed
        return $withdrawalsData->map(function ($withdrawal) {
            // Map numeric status to descriptive text
            $statusDescription = match($withdrawal->status) {
                0 => 'Pending',
                1 => 'Paid',
                2 => 'Cancelled',
                default => 'Unknown', // Fallback for any unexpected status
            };

            return [
                'Beneficiary Name' => $withdrawal->unique_name, // Unique user name again
                'Beneficiary Account number' => $withdrawal->account_number,
                'IFSC code' => $withdrawal->ifsc_code,
                'Amount' => $withdrawal->amount,
                'Description / Purpose' => 'salary', // Unique user name
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Beneficiary Name (Mandatory)',
            'Beneficiary Account number (Mandatory)',
            'IFSC code (Mandatory)',
            'Amount (Mandatory)', // Column for descriptive status
            'Description / Purpose (Optional)',
        ];
    }
}
