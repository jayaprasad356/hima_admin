<?php

namespace App\Exports;

use App\Models\Withdrawals;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExports implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Withdrawals::query()
        ->select(
            'users.name as user_name', // Contact Person Name should be unique user name
            'users.unique_name as unique_name', // Contact Person Name should be unique user name
            'users.name as billing_name', // Billing Name should be unique user name
            'bank_details.account_number as beneficiary_account_number', // Beneficiary account number
            'bank_details.ifsc_code', // IFSC code
            'bank_details.bank_name', // Bank name
            'bank_details.branch_name', // Branch name
            'withdrawals.amount', // Withdrawal amount
            'withdrawals.datetime', // Withdrawal datetime
            'withdrawals.status' // Withdrawal status
        )
        ->join('users', 'withdrawals.user_id', '=', 'users.id') // Join users table
        ->leftJoin('bank_details', 'users.id', '=', 'bank_details.user_id') // Join bank_details table
        ->where('withdrawals.status', 0); // Only fetch status = 0

        // Apply other filters if needed
        if (isset($this->filters['filter_date'])) {
            $query->whereDate('withdrawals.datetime', $this->filters['filter_date']);
        }

        // Get the withdrawals data with related user and bank details
        $withdrawalsData = $query->get();

        // Map through the data to format status as needed
        return $withdrawalsData->map(function ($withdrawal) {
            return [
                'Contact Type' => 'Customer', // Unique user name
                'Business Name' => '', // Leave empty
                'Contact Person Name' => $withdrawal->unique_name, // Unique user name again
                'Mobile Number' => '', // Leave empty
                'Email' => '', // Leave empty
                'Billing Name' => $withdrawal->unique_name  , // Unique user name for billing
                'Billing Address' => '', // Leave empty
                'Billing Code Pincode' => '', // Leave empty
                'Shipping Name' => '', // Leave empty
                'Shipping Address' => '', // Leave empty
                'Shipping Code Pincode' => '', // Leave empty
                'Beneficiary Account Number' => $withdrawal->beneficiary_account_number, // Account number
                'IFSC Code' => $withdrawal->ifsc_code, // IFSC code
                'Bank Name' => $withdrawal->bank_name, // Bank name
                'Branch Name' => $withdrawal->branch_name, // Branch name
                'Pan' => '', // Leave empty
                'GSTIN' => '', // Leave empty
                'GST Registration Type' => '', // Leave empty
                'Is e-Commerce Operator?' => '', // Leave empty
                'Is Transporter?' => '', // Leave empty
                'Transporter ID' => '', // Leave empty
                'TDS' => '', // Leave empty
                'Notes' => '', // Leave empty
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Contact type - Select from List (Mandatory)',
            'Business Name - Minimum 3 char & Max 100 char., Name should not end with space (Mandatory)',
            'Contact Person Name - Minimum 3 char & Max 50 char., Name should not end with space (M)',
            'Mobile number - Numeric Value and 10 digits (Mandatory)', // Include status field
            'E-Mail id of Contact (O)',
            'Billing Name - This values will be displayed on Invoices (M)',
            'Billing Address - This values will be displayed on Invoices',
            'Billing code Pincode - (O)',
            'Shipping Name -(O)',
            'Shipping Address - (O)',
            'shipping code Pincode -(O)',
            'Beneficiary account number(O)',
            'IFSC code (O)',
            'Bank name(O)',
            'Branch name(O)',
            'Pan(O)',
            'GSTIN(O)',
            'GST Registration Type(O)',
            'Is e-Commerce Operator? (O)',
            'Is Transporter? (O)',
            'Transporter ID (O)',
            'TDS(O)',
            'Notes (O)'
        ];
    }
}
