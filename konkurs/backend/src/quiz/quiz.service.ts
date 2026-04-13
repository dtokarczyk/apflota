import {
  Injectable,
  ConflictException,
  NotFoundException,
} from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { v4 as uuidv4 } from 'uuid';
import { Submission } from './entities/submission.entity';
import { StartQuizDto } from './dto/start-quiz.dto';
import { SubmitQuizDto } from './dto/submit-quiz.dto';
import answersJson from '../data/answers.json';

interface QuizQuestion {
  id: number;
  question: string;
  options: string[];
  correctAnswer: string;
}

const questions: QuizQuestion[] = answersJson as QuizQuestion[];

function shuffle<T>(array: T[]): T[] {
  const result = [...array];
  for (let i = result.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [result[i], result[j]] = [result[j], result[i]];
  }
  return result;
}

const CONTEST_START = new Date('2026-04-13T18:00:00+02:00');
const CONTEST_END = new Date('2026-04-13T23:00:00+02:00');
const FORCE_CODE = 'kodiwoapflota';

@Injectable()
export class QuizService {
  constructor(
    @InjectRepository(Submission)
    private readonly submissionRepo: Repository<Submission>,
  ) {}

  getContestStatus(force?: string) {
    const now = new Date();

    let status: 'upcoming' | 'active' | 'finished';
    if (force === FORCE_CODE) {
      status = 'active';
    } else if (now < CONTEST_START) {
      status = 'upcoming';
    } else if (now > CONTEST_END) {
      status = 'finished';
    } else {
      status = 'active';
    }

    return {
      status,
      startsAt: CONTEST_START.toISOString(),
      endsAt: CONTEST_END.toISOString(),
      now: now.toISOString(),
    };
  }

  async startQuiz(dto: StartQuizDto, ipAddress: string) {
    const normalizedEmail = dto.email.toLowerCase().trim();

    const existing = await this.submissionRepo.findOne({
      where: { email: normalizedEmail },
    });
    if (existing) {
      throw new ConflictException(
        'Ten adres e-mail już wziął udział w konkursie',
      );
    }

    const sessionId = uuidv4();
    const now = new Date();

    const submission = this.submissionRepo.create({
      email: normalizedEmail,
      sessionId,
      consentContestRules: dto.consentContestRules,
      consentPersonalDataMarketing: dto.consentPersonalDataMarketing,
      consentCommercialInformationEmail: dto.consentCommercialInformationEmail,
      ipAddress,
      startedAt: now,
      totalQuestions: questions.length,
    });

    await this.submissionRepo.save(submission);

    const shuffledQuestions = shuffle(questions).map((q) => ({
      id: q.id,
      question: q.question,
      options: shuffle(q.options),
    }));

    return {
      sessionId,
      serverStartedAt: now.toISOString(),
      questions: shuffledQuestions,
    };
  }

  async submitQuiz(dto: SubmitQuizDto) {
    const submission = await this.submissionRepo.findOne({
      where: { sessionId: dto.sessionId },
    });

    if (!submission) {
      throw new NotFoundException('Sesja nie została znaleziona');
    }

    if (submission.finishedAt) {
      throw new ConflictException('Quiz został już zakończony');
    }

    const now = new Date();

    let correctCount = 0;
    const details = questions.map((q) => {
      const userAnswer = dto.answers[String(q.id)] || '';
      const isCorrect = userAnswer === q.correctAnswer;
      if (isCorrect) correctCount++;

      return {
        id: q.id,
        question: q.question,
        userAnswer,
        correctAnswer: q.correctAnswer,
        isCorrect,
      };
    });

    submission.finishedAt = now;
    submission.clientStartedAt = new Date(dto.clientStartedAt);
    submission.clientFinishedAt = new Date(dto.clientFinishedAt);
    submission.answers = dto.answers;
    submission.correctCount = correctCount;

    await this.submissionRepo.save(submission);

    const serverTimeMs = now.getTime() - submission.startedAt.getTime();
    const clientTimeMs =
      new Date(dto.clientFinishedAt).getTime() -
      new Date(dto.clientStartedAt).getTime();

    return {
      correctCount,
      totalQuestions: questions.length,
      serverTimeSeconds: Math.round(serverTimeMs / 10) / 100,
      clientTimeSeconds: Math.round(clientTimeMs / 10) / 100,
      details,
    };
  }
}
