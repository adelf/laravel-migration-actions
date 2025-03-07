<?php

use DragonCode\LaravelActions\Support\Actionable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

return new class () extends Actionable {
    public function up(): void
    {
    }

    public function down(): void
    {
    }

    public function failed(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    protected function table(): Builder
    {
        return DB::table('success');
    }
};
