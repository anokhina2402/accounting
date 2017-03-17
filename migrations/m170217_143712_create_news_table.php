<?php

use yii\db\Migration;

/**
 * Handles the creation of table `news`.
 */
class m170217_143712_create_news_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'auth_key' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->createTable('plan_income', [
            'id' => $this->primaryKey(),
            'category' => $this->string()->notNull(),
            'sum' => $this->float()->notNull(),
            'date' => $this->date()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->createTable('plan_outgo', [
            'id' => $this->primaryKey(),
            'category' => $this->string()->notNull(),
            'sum' => $this->float()->notNull(),
            'date' => $this->date()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->createTable('income', [
            'id' => $this->primaryKey(),
            'category' => $this->string()->notNull(),
            'sum' => $this->float()->notNull(),
            'date' => $this->date()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->createTable('outgo', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'category' => $this->string()->notNull(),
            'category2' => $this->string()->notNull(),
            'sum' => $this->float()->notNull(),
            'date' => $this->date()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('plan_income');
        $this->dropTable('plan_outgo');
        $this->dropTable('income');
        $this->dropTable('outgo');
    }
}
