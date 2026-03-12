<?php

namespace App\Actions\Board;

use App\Models\Board;
use Illuminate\Support\Facades\DB;

class ReorderBoardColumnsAction
{
    /**
     * @param  array<int, int>  $columnIds  Ordered list of column IDs
     */
    public function execute(Board $board, array $columnIds): void
    {
        DB::transaction(function () use ($board, $columnIds) {
            $columns = $board->columns()->whereIn('id', $columnIds)->get()->keyBy('id');

            foreach ($columnIds as $position => $columnId) {
                $column = $columns->get($columnId);
                if ($column) {
                    $column->update(['sort_order' => $position]);
                }
            }
        });
    }
}
