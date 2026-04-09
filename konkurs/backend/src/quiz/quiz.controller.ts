import { Controller, Post, Get, Body, Query, Req, Ip } from '@nestjs/common';
import { Request } from 'express';
import { QuizService } from './quiz.service';
import { StartQuizDto } from './dto/start-quiz.dto';
import { SubmitQuizDto } from './dto/submit-quiz.dto';

@Controller('quiz')
export class QuizController {
  constructor(private readonly quizService: QuizService) {}

  @Get('status')
  getStatus(@Query('force') force?: string) {
    return this.quizService.getContestStatus(force);
  }

  @Post('start')
  async start(@Body() dto: StartQuizDto, @Req() req: Request) {
    const forwarded = req.headers['x-forwarded-for'];
    const ip = typeof forwarded === 'string'
      ? forwarded.split(',')[0].trim()
      : req.ip || '0.0.0.0';

    return this.quizService.startQuiz(dto, ip);
  }

  @Post('submit')
  async submit(@Body() dto: SubmitQuizDto) {
    return this.quizService.submitQuiz(dto);
  }
}
