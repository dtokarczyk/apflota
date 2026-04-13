import { IsEmail, IsBoolean, Equals } from 'class-validator';

export class StartQuizDto {
  @IsEmail()
  email: string;

  @IsBoolean()
  @Equals(true, { message: 'Consent to regulations is required' })
  consentContestRules: boolean;

  @IsBoolean()
  consentPersonalDataMarketing: boolean;

  @IsBoolean()
  consentCommercialInformationEmail: boolean;
}
