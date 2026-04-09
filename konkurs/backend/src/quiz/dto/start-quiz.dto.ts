import { IsEmail, IsBoolean, Equals } from 'class-validator';

export class StartQuizDto {
  @IsEmail()
  email: string;

  @IsBoolean()
  @Equals(true, { message: 'Consent to regulations is required' })
  consentRegulations: boolean;

  @IsBoolean()
  consentMarketing: boolean;
}
