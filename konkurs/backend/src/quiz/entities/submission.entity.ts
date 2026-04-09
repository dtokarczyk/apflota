import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  Index,
} from 'typeorm';

@Entity('submissions')
export class Submission {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @Index({ unique: true })
  @Column({ type: 'varchar', length: 255 })
  email: string;

  @Index({ unique: true })
  @Column({ type: 'uuid', name: 'session_id' })
  sessionId: string;

  @Column({ type: 'boolean', name: 'consent_regulations' })
  consentRegulations: boolean;

  @Column({ type: 'boolean', name: 'consent_marketing' })
  consentMarketing: boolean;

  @Column({ type: 'varchar', length: 45, name: 'ip_address' })
  ipAddress: string;

  @Column({ type: 'timestamp', name: 'started_at' })
  startedAt: Date;

  @Column({ type: 'timestamp', name: 'finished_at', nullable: true })
  finishedAt: Date | null;

  @Column({ type: 'timestamp', name: 'client_started_at', nullable: true })
  clientStartedAt: Date | null;

  @Column({ type: 'timestamp', name: 'client_finished_at', nullable: true })
  clientFinishedAt: Date | null;

  @Column({ type: 'jsonb', nullable: true })
  answers: Record<string, string> | null;

  @Column({ type: 'int', name: 'correct_count', nullable: true })
  correctCount: number | null;

  @Column({ type: 'int', name: 'total_questions' })
  totalQuestions: number;
}
