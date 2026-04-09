import { MigrationInterface, QueryRunner } from 'typeorm';

export class RestoreSubmissionsEmailUnique1712700000002
  implements MigrationInterface
{
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(`DROP INDEX IF EXISTS "IDX_submissions_email"`);
    await queryRunner.query(
      `CREATE UNIQUE INDEX "IDX_submissions_email" ON "submissions" ("email")`,
    );
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(`DROP INDEX IF EXISTS "IDX_submissions_email"`);
    await queryRunner.query(
      `CREATE INDEX "IDX_submissions_email" ON "submissions" ("email")`,
    );
  }
}
