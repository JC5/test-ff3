<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const string QUERY_ERROR = 'Could not execute query (table "%s", field "%s"): %s';
    private const string EXPL        = 'If the index already exists (see error), this is not an problem. Otherwise, please open a GitHub discussion.';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // add missing indices
        $set = [
            'account_meta'           => ['id', 'account_id'],
            'accounts'               => ['id', 'user_id', 'user_group_id', 'account_type_id'],
            'budgets'                => ['id', 'user_id', 'user_group_id'],
            'categories'             => ['id', 'user_id', 'user_group_id'],
            'transaction_currencies' => ['id', 'code'],
            'transaction_groups'     => ['id', 'user_id', 'user_group_id'],
            'transaction_journals'   => ['id', 'user_id', 'user_group_id', 'transaction_group_id', 'transaction_type_id', 'transaction_currency_id', 'bill_id'],
            'transactions'           => ['id', 'user_id', 'user_group_id', 'account_id', 'transaction_journal_id', 'transaction_currency_id', 'foreign_currency_id'],
        ];

        foreach ($set as $table => $fields) {
            foreach ($fields as $field) {
                try {
                    Schema::table(
                        $table,
                        static function (Blueprint $blueprint) use ($field): void {
                            $blueprint->index($field);
                        }
                    );
                } catch (QueryException $e) {
                    app('log')->error(sprintf(self::QUERY_ERROR, $table, $field, $e->getMessage()));
                    app('log')->error(self::EXPL);
                }
            }
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};