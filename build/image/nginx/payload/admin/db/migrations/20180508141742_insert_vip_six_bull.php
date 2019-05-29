<?php


use Phinx\Migration\AbstractMigration;

class InsertVipSixBull extends AbstractMigration
{
    public $table_name = 'game_list';
    public $game_type  = 93;
    public $game_title = 'vip6人牛牛';

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
    }

    public function down()
    {
        $rs = $this->fetchRow("SELECT game_id FROM {$this->table_name} WHERE game_type={$this->game_type} AND is_delete=0");

        if ($rs) {
            $this->execute("DELETE FROM {$this->table_name} WHERE game_id={$rs['game_id']}");
        }
    }
}
