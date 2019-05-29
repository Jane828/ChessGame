<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class AddFields extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('bull_room');
        if (! $table->hasColumn('times_type')) {
            $table->addColumn('times_type', 'integer', [
                'default' => 1,
                'limit'   => MysqlAdapter::INT_TINY,
                'comment' => '倍数规则：1，2，3',
                'after'   => 'game_type'
            ])->update();
        }
        if (! $table->hasColumn('club_id')) {
            $table->addColumn('club_id', 'integer', [
                'default' => 0,
                'comment' => '公会ID',
                'after'   => 'times_type'
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

        $table = $this->table('wechat_account');
        if (! $table->hasColumn('secret')) {
            $table->addColumn('secret', 'string', [
                'default' => '',
                'limit'   => 32,
                'comment' => '密钥',
                'after'   => 'phone'
            ])->update();
        }
    }
}
