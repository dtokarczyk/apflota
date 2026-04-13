import { MigrationInterface, QueryRunner } from 'typeorm';

export class ResetSubmissionsConsents1712700000003
  implements MigrationInterface
{
  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(`DROP TABLE IF EXISTS "submissions" CASCADE`);

    await queryRunner.query(`
      CREATE TABLE "submissions" (
        "id" uuid NOT NULL DEFAULT gen_random_uuid(),
        "email" varchar(255) NOT NULL,
        "session_id" uuid NOT NULL,
        "consent_contest_rules" boolean NOT NULL,
        "consent_personal_data_marketing" boolean NOT NULL,
        "consent_commercial_information_email" boolean NOT NULL,
        "ip_address" varchar(45) NOT NULL,
        "started_at" timestamp NOT NULL,
        "finished_at" timestamp,
        "client_started_at" timestamp,
        "client_finished_at" timestamp,
        "answers" jsonb,
        "correct_count" int,
        "total_questions" int NOT NULL,
        CONSTRAINT "PK_submissions" PRIMARY KEY ("id")
      )
    `);

    await queryRunner.query(
      `CREATE UNIQUE INDEX "IDX_submissions_email" ON "submissions" ("email")`,
    );
    await queryRunner.query(
      `CREATE UNIQUE INDEX "IDX_submissions_session_id" ON "submissions" ("session_id")`,
    );
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(`DROP TABLE IF EXISTS "submissions" CASCADE`);

    await queryRunner.query(`
      CREATE TABLE "submissions" (
        "id" uuid NOT NULL DEFAULT gen_random_uuid(),
        "email" varchar(255) NOT NULL,
        "session_id" uuid NOT NULL,
        "consent_regulations" boolean NOT NULL,
        "consent_marketing" boolean NOT NULL,
        "ip_address" varchar(45) NOT NULL,
        "started_at" timestamp NOT NULL,
        "finished_at" timestamp,
        "client_started_at" timestamp,
        "client_finished_at" timestamp,
        "answers" jsonb,
        "correct_count" int,
        "total_questions" int NOT NULL,
        CONSTRAINT "PK_submissions" PRIMARY KEY ("id")
      )
    `);

    await queryRunner.query(
      `CREATE UNIQUE INDEX "IDX_submissions_email" ON "submissions" ("email")`,
    );
    await queryRunner.query(
      `CREATE UNIQUE INDEX "IDX_submissions_session_id" ON "submissions" ("session_id")`,
    );
  }
}
