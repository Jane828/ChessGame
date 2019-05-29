<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class Clubs extends AbstractMigration
{
    public function up()
    {
        // 公会表
        $table = $this->table('clubs', [
            'id' => 'club_id',
            'comment' => '公会表'
        ]);
        $table->addColumn('club_no', 'integer', [
                'default' => 0,
                'comment' => '公会编号'
            ])
            ->addColumn('club_name', 'string', [
                'default' => '',
                'limit'   => 32,
                'comment' => '公会名称'
            ])
            ->addColumn('admin_id', 'integer', [
                'default' => 0,
                'comment' => '会长ID'
            ])
            ->addColumn('admin_code', 'integer', [
                'default' => 0,
                'comment' => '会长编号'
            ])
            ->addColumn('admin_nick', 'string', [
                'default' => '',
                'comment' => '会长昵称'
            ])
            ->addColumn('admin_head', 'string', [
                'default' => '',
                'comment' => '会长头像'
            ])
            ->addColumn('club_status', 'boolean', [
                'default' => 1,
                'comment' => '公会状态 1正常 0禁用'
            ])
            ->addColumn('create_card', 'integer', [
                'default' => 10,
                'signed'  => false,
                'limit'   => MysqlAdapter::INT_TINY,
                'comment' => '创建公会消耗房卡'
            ])
            ->addColumn('max_player', 'integer', [
                'default' => 200,
                'signed'  => false,
                'limit'   => MysqlAdapter::INT_SMALL,
                'comment' => '公会人数上限'
            ])
            ->addTimestamps()
            ->create();

        // 申请记录
        $table = $this->table('club_applies', [
            'id' => 'data_id',
            'comment' => '申请记录表'
        ]);
        $table->addColumn('club_id', 'integer', [
            'default' => 0,
            'comment' => '公会ID'
        ])
            ->addColumn('player_id', 'integer', [
                'default' => 0,
                'comment' => '玩家ID'
            ])
            ->addColumn('player_code', 'integer', [
                'default' => 0,
                'comment' => '玩家编号'
            ])
            ->addColumn('player_nick', 'string', [
                'default' => '',
                'comment' => '玩家昵称'
            ])
            ->addColumn('player_head', 'string', [
                'default' => '',
                'comment' => '玩家头像'
            ])
            ->addColumn('apply_status', 'integer', [
                'default' => 0,
                'signed'  => false,
                'limit'   => MysqlAdapter::INT_TINY,
                'comment' => '申请状态 0待处理 1已同意 2已拒绝'
            ])
            ->addTimestamps()
            ->addIndex(['club_id', 'player_id'], ['name'=>'idx_club_player'])
            ->create();

        // 公会成员表
        $table = $this->table('club_players', [
            'id' => 'data_id',
            'comment' => '公会成员表'
        ]);
        $table->addColumn('club_id', 'integer', [
                'default' => 0,
                'comment' => '公会ID'
            ])
            ->addColumn('player_id', 'integer', [
                'default' => 0,
                'comment' => '玩家ID'
            ])
            ->addColumn('player_code', 'integer', [
                'default' => 0,
                'comment' => '玩家编号'
            ])
            ->addColumn('player_nick', 'string', [
                'default' => 0,
                'comment' => '玩家昵称'
            ])
            ->addColumn('player_head', 'string', [
                'default' => '',
                'comment' => '玩家头像'
            ])
            ->addColumn('player_bean', 'integer', [
                'default' => 0,
                'comment' => '玩家余豆'
            ])
            ->addColumn('player_status', 'boolean', [
                'default' => 0,
                'comment' => '玩家状态 1:加入 2:主动退出 3:会长踢出'
            ])
            ->addColumn('is_last', 'boolean', [
                'default' => 0,
                'comment' => '0:未选中1:已选中'
            ])
            ->addColumn('is_gaming', 'boolean', [
                'default' => 0,
                'comment' => '0:未游戏1:游戏中'
            ])
            ->addTimestamps()
            ->addIndex(['club_id', 'player_id'], ['name'=>'idx_club_player'])
            ->create();

        // 公会动态表
        $table = $this->table('club_logs', [
            'id' => 'log_id',
            'comment' => '公会动态表'
        ]);
        $table->addColumn('log_type', 'integer', [
                'default' => 0,
                'limit'   => MysqlAdapter::INT_TINY,
                'comment' => '日志类型 1:创建公会 2:公会改名 3:加入公会 4:玩家退出 5:会长踢出'
            ])
            ->addColumn('club_id', 'integer', [
                'default' => 0,
                'comment' => '公会ID'
            ])
            ->addColumn('player_id', 'integer', [
                'default' => 0,
                'comment' => '玩家ID'
            ])
            ->addColumn('player_code', 'integer', [
                'default' => 0,
                'comment' => '玩家编号'
            ])
            ->addColumn('player_nick', 'string', [
                'default' => '',
                'comment' => '玩家昵称'
            ])
            ->addColumn('player_head', 'string', [
                'default' => '',
                'comment' => '玩家头像'
            ])
            ->addColumn('player_bean', 'integer', [
                'default' => 0,
                'comment' => '玩家余豆'
            ])
            ->addColumn('club_name', 'string', [
                'default' => '',
                'limit'   => 32,
                'comment' => '公会名称'
            ])
            ->addTimestamps()
            ->create();

        // 欢乐豆明细表
        $table = $this->table('club_beans', [
            'id' => 'log_id',
            'comment' => '欢乐豆明细表'
        ]);
        $table->addColumn('log_type', 'integer', [
                'default' => 0,
                'signed'  => false,
                'limit'   => MysqlAdapter::INT_TINY,
                'comment' => '明细类型：1:购买(会长赠豆) 2:提现兑换 3:游戏赢 4:游戏输 5:游戏提成'
            ])
            ->addColumn('content', 'string', [
                'default' => '',
                'comment' => '明细内容'
            ])
            ->addColumn('club_id', 'integer', [
                'default' => 0,
                'comment' => '公会ID'
            ])
            ->addColumn('player_id', 'integer', [
                'default' => 0,
                'comment' => '玩家ID'
            ])
            ->addColumn('player_code', 'integer', [
                'default' => 0,
                'comment' => '玩家编号'
            ])
            ->addColumn('player_nick', 'string', [
                'default' => '',
                'comment' => '玩家昵称'
            ])
            ->addColumn('player_head', 'string', [
                'default' => '',
                'comment' => '玩家头像'
            ])
            ->addColumn('game_type', 'integer', [
                'default' => 0,
                'comment' => '游戏代号'
            ])
            ->addColumn('room_id', 'integer', [
                'default' => 0,
                'comment' => '房间号'
            ])
            ->addColumn('before_bean', 'integer', [
                'default' => 0,
                'comment' => '变动前豆数'
            ])
            ->addColumn('change_bean', 'integer', [
                'default' => 0,
                'comment' => '变动的豆数'
            ])
            ->addColumn('after_bean', 'integer', [
                'default' => 0,
                'comment' => '变动后豆数'
            ])
            ->addTimestamps()
            ->addIndex(['club_id', 'player_id'], ['name'=>'idx_club_player'])
            ->addIndex(['game_type', 'room_id', 'player_id'], ['name'=>'idex_game_room_player'])
            ->create();
    }

    public function down()
    {
        $this->dropTable('clubs');
        $this->dropTable('club_applies');
        $this->dropTable('club_bean_logs');
        $this->dropTable('club_logs');
        $this->dropTable('club_players');
    }
}
