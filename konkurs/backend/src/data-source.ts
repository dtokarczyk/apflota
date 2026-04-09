import { DataSource } from 'typeorm';
import { Submission } from './quiz/entities/submission.entity';

export default new DataSource({
  type: 'postgres',
  host: process.env.DATABASE_HOST || 'localhost',
  port: parseInt(process.env.DATABASE_PORT || '5432', 10),
  username: process.env.DATABASE_USER || 'konkurs',
  password: process.env.DATABASE_PASSWORD || 'konkurs',
  database: process.env.DATABASE_NAME || 'konkurs',
  entities: [Submission],
  migrations: ['src/migrations/*{.ts,.js}'],
});
