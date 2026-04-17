<?php

namespace Sanvex\Core;

use Illuminate\Support\Facades\DB;

class DbAccessor
{
    protected string $tablePrefix = 'sv_';

    public function table(string $name)
    {
        return DB::table($this->tablePrefix . $name);
    }

    public function find(string $table, $id)
    {
        return $this->table($table)->where('id', $id)->first();
    }

    public function insert(string $table, array $data)
    {
        return $this->table($table)->insertGetId($data);
    }

    public function update(string $table, $id, array $data)
    {
        return $this->table($table)->where('id', $id)->update($data);
    }

    public function delete(string $table, $id)
    {
        return $this->table($table)->where('id', $id)->delete();
    }
}
