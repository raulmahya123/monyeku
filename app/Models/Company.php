<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'email',
        'logo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Company $company) {
            $income = ['Gaji', 'Penjualan', 'Investasi', 'Freelance', 'Bunga Bank', 'Lain-lain (Pemasukan)'];
            $expense = ['Bahan Baku', 'Listrik', 'Air', 'Marketing', 'Gaji Karyawan', 'Sewa', 'Transportasi', 'Makanan', 'Perlengkapan', 'Pajak', 'Internet', 'Lain-lain (Pengeluaran)'];

            foreach ($income as $name) {
                Category::create([
                    'company_id' => $company->id,
                    'name' => $name,
                    'type' => 'income',
                    'is_active' => true,
                ]);
            }

            foreach ($expense as $name) {
                Category::create([
                    'company_id' => $company->id,
                    'name' => $name,
                    'type' => 'expense',
                    'is_active' => true,
                ]);
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'is_active')->withTimestamps();
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function recurringTransactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    public function approvalConfigs(): HasMany
    {
        return $this->hasMany(ApprovalConfig::class);
    }

    public function accountingPeriods(): HasMany
    {
        return $this->hasMany(AccountingPeriod::class);
    }

    public function auditTrails(): HasMany
    {
        return $this->hasMany(AuditTrail::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function bankReconciliations(): HasMany
    {
        return $this->hasMany(BankReconciliation::class);
    }

    public function coas(): HasMany
    {
        return $this->hasMany(Coa::class);
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }
}
