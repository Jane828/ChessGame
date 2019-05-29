<?php

use Phinx\Db\Adapter\MysqlAdapter;

use Phinx\Migration\AbstractMigration;

class ClubConsumes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('club_consumes', [
            'id'      => 'log_id',
            'comment' => '消耗记录'
        ]);
        $table->addColumn('club_id', 'integer', [
            'default' => 0,
            'comment' => '公会ID'
        ])
            ->addColumn('player_id', 'integer', [
                'default' => 0,
                'comment' => '玩家ID'
            ])
            ->addColumn('game_type', 'integer', [
                'default' => 0,
                'comment' => '游戏代号'
            ])
            ->addColumn('room_id', 'integer', [
                'default' => 0,
                'comment' => '房间号'
            ])
            ->addColumn('score', 'integer', [
                'default' => 0,
                'comment' => '赢豆数'
            ])
            ->addColumn('rate', 'integer', [
                'default' => 0,
                'limit'   => MysqlAdapter::INT_TINY,
                'comment' => '消耗比例'
            ])
            ->addColumn('bean', 'integer', [
                'default' => 0,
                'comment' => '消耗豆数'
            ])
            ->addTimestamps()
            ->addIndex(['game_type', 'room_id', 'player_id'], ['name' => 'idx_game_room_player'])
            ->create();
    }
}
