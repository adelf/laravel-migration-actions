<?php

use DragonCode\LaravelActions\Support\Actionable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class RunSuccess extends Actionable
{
    public function up(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    public function down(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    public function success(): void
    {
        $this->table()->insert([
            'value' => Uuid::uuid4(),
        ]);
    }

    protected function table(): Builder
    {
        return DB::table('success');
    }
}
