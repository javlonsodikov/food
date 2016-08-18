<?php

use yii\db\Migration;
use \yii\db\mssql\Schema;

class m160818_045704_create_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%food}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'ingredients' => Schema::TYPE_STRING . ' DEFAULT NULL',
        ], $tableOptions);


        $this->createTable('{{%ingredient}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'active' => Schema::TYPE_BOOLEAN . '  NOT NULL DEFAULT \'1\'',
        ], $tableOptions);

        $this->createTable('{{%food_ingredients}}', [
            'id' => Schema::TYPE_PK,
            'food_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'ingredient_id' => Schema::TYPE_INTEGER . ' NOT NULL'
        ], $tableOptions);

        $this->createIndex('food_id', '{{%food_ingredients}}', 'food_id');
        $this->createIndex('ingredient_id', '{{%food_ingredients}}', 'ingredient_id');

        $this->addForeignKey('fk_food_id', '{{%food_ingredients}}', 'food_id', '{{%food}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_ingredient_id', '{{%food_ingredients}}', 'ingredient_id', '{{%ingredient}}', 'id', 'RESTRICT', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('{{%food_ingredients}}');
        $this->dropTable('{{%food}}');
        $this->dropTable('{{%ingredient}}');
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
