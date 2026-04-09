import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { QuizModule } from './quiz/quiz.module';
import { Submission } from './quiz/entities/submission.entity';

@Module({
  imports: [
    TypeOrmModule.forRoot({
      type: 'postgres',
      host: process.env.DATABASE_HOST || 'localhost',
      port: parseInt(process.env.DATABASE_PORT || '5432', 10),
      username: process.env.DATABASE_USER || 'konkurs',
      password: process.env.DATABASE_PASSWORD || 'konkurs',
      database: process.env.DATABASE_NAME || 'konkurs',
      entities: [Submission],
      migrations: [__dirname + '/migrations/*{.ts,.js}'],
      migrationsRun: true,
      logging: process.env.NODE_ENV !== 'production',
      extra: {
        max: 20,
      },
    }),
    QuizModule,
  ],
})
export class AppModule {}
