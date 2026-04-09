import { IsUUID, IsObject, IsDateString } from 'class-validator';

export class SubmitQuizDto {
  @IsUUID()
  sessionId: string;

  @IsObject()
  answers: Record<string, string>;

  @IsDateString()
  clientStartedAt: string;

  @IsDateString()
  clientFinishedAt: string;
}
