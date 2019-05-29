<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class ClubConsumeSets extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('club_consume_sets', [
            'id' => 'data_id',
            'comment' => '消耗设置'
        ]);

        $table->addColumn('club_id', 'integer', [
            'default' => 0,
            'comment' => '公会ID'
        ])
            ->addColumn('club_no', 'integer', [
                'default' => 0,
                'comment' => '公会编号'
            ])
            ->addColumn('winner1', 'integer', [
                'limit' => MysqlAdapter::INT_TINY,
                'default' => 0,
                'comment' => '大赢家消耗百分比'
            ])
            ->addColumn('winner2', 'integer', [
                'limit' => MysqlAdapter::INT_TINY,
                'default' => 0,
                'comment' => '二赢家消耗百分比'
            ])
            ->addColumn('winner3', 'integer', [
                'limit' => MysqlAdapter::INT_TINY,
                'default' => 0,
                'comment' => '三赢家消耗百分比'
            ])
            ->addTimestamps()
            ->addIndex(['club_id', 'club_no'], ['name'=>'idx_id_no'])
            ->create();
    }
}
