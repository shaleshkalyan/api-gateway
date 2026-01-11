import { MigrationInterface, QueryRunner } from "typeorm";

export class CreateUsersTable1768126908901 implements MigrationInterface {
    name = 'CreateUsersTable1768126908901'

  async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(`
      CREATE TABLE IF NOT EXISTS payflow_users (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        roles TEXT[] DEFAULT '{}',
        permissions TEXT[] DEFAULT '{}',
        created_at TIMESTAMP DEFAULT NOW()
      )
    `);
  }

  async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(`DROP TABLE users`);
  }

}
