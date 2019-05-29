<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class InsertVipFlower extends AbstractMigration
{
    public $table_name = 'game_list';
    public $game_type  = 92;
    public $game_title = 'vip6人金花';

    public function up()
    {
        // return array or bool(false)
        $rs = $this->fetchRow("SELECT game_id FROM {$this->table_name} WHERE game_type={$this->game_type} AND is_delete=0");

        if (!$rs) {
            $data = [
                'game_type'    => $this->game_type,
                'game_title'   => $this->game_title,
                'create_time'  => 0,
                'create_appid' => '',
                'update_time'  => 0,
                'update_appid' => '',
                'is_delete'    => 0,
                'domain_host'  => '',
                'domain_port'  => -1,
                'ip_host'      => '',
                'ip_port'      => -1
            ];
            $this->insert($this->table_name, $data);
        }

        $table = $this->table('flower_room');
        if (! $table->hasColumn('club_id')) {
            $table->addColumn('club_id', 'integer', [
                'default' => 0,
                'comment' => '公会ID',
                'after'   => 'game_type'
            ])->update();
        }
        if (! $table->hasColumn('bean_type')) {
            $table->addColumn('bean_type', 'integer', [
                'default' => 0,
                'limit'   => MysqlAdapter::INT_TINY,
                'comment' => '准入规则 1:≥300豆 2:≥1000豆 3:2000豆 4:5000豆',
                'after'   => 'club_id'
            ])->update();
        }
    }

    public function down()
    {
        $rs = $this->fetchRow("SELECT game_id FROM {$this->table_name} WHERE game_type={$this->game_type} AND is_delete=0");

        if ($rs) {
            $this->execute("DELETE FROM {$this->table_name} WHERE game_id={$rs['game_id']}");
        }
    }
}
