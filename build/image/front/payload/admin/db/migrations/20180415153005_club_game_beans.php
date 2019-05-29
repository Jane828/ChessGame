<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class ClubGameBeans extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('club_game_beans', [
            'id'      => 'log_id',
            'comment' => '游戏记录'
        ]);
        $table->addColumn('club_id', 'integer', [
            'default' => 0,
            'comment' => '公会ID'
        ])
            ->addColumn('game_type', 'integer', [
                'default' => 0,
                'comment' => '游戏代号'
            ])
            ->addColumn('room_id', 'integer', [
                'default' => 0,
                'comment' => '房间号'
            ])
            ->addColumn('game_num', 'integer', [
                'default' => 0,
                'limit'   => MysqlAdapter::INT_TINY,
                'comment' => '当前局数'
            ])
            ->addColumn('player_id', 'integer', [
                'default' => 0,
                'comment' => '玩家ID'
            ])
            ->addColumn('bean', 'integer', [
                'default' => 0,
                'comment' => '输赢豆数'
            ])
            ->addTimestamps()
            ->addIndex(['game_type', 'room_id', 'player_id'], ['name' => 'idx_game_room_player'])
            ->create();
    }
}
